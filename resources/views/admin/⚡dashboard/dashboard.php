<?php
use App\Models\CakeColor;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeTopping;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReadyCake;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Settings\AppearanceSettings;
use App\Settings\CurrencySettings;
use Illuminate\Database\Eloquent\Collection;

/**
 * Dashboard Component
 *
 * This Multi-File Livewire 4 component manages the main bakery administration dashboard.
 * It utilizes #[Computed] properties for isolated logic and performance optimization
 * and follows the `with()` standard for passing layout variables.
 */
new #[Layout('layouts::admin', ['title' => 'Dashboard'])] class extends Component {

    /**
     * Compute statistics for Ready vs Custom Cakes sold.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function cakeStats(): array
    {
        return Cache::remember('dashboard.cake-stats', now()->addMinutes(60), function () {
            $readyCount = OrderItem::whereNotNull('ready_cake_id')->sum('quantity');
            $customCount = OrderItem::whereNull('ready_cake_id')->sum('quantity');

            return [
                'ready' => (int) $readyCount,
                'custom' => (int) $customCount,
            ];
        });
    }

    /**
     * Compute the total number of orders placed across the application.
     *
     * @return int
     */
    #[Computed]
    public function totalOrders(): int
    {
        return Cache::remember('dashboard.total-orders', now()->addMinutes(10), function () {
            return Order::count();
        });
    }

    /**
     * Compute the number of orders currently pending.
     *
     * @return int
     */
    #[Computed]
    public function pendingOrders(): int
    {
        return Cache::remember('dashboard.pending-orders', now()->addMinutes(10), function () {
            return Order::where('status', 'pending')->count();
        });
    }

    /**
     * Compute the total revenue globally processed, excluding pending or cancelled orders.
     *
     * @return float
     */
    #[Computed]
    public function totalRevenue(): float
    {
        return Cache::remember('dashboard.total-revenue', now()->addMinutes(60), function () {
            return (float) Order::whereNotIn('status', ['cancelled', 'pending'])->sum('total_price');
        });
    }

    /**
     * Compute the revenue restricted strictly to the current month.
     *
     * @return float
     */
    #[Computed]
    public function currentMonthRevenue(): float
    {
        return Cache::remember('dashboard.current-month-revenue', now()->addMinutes(30), function () {
            return (float) Order::whereNotIn('status', ['cancelled', 'pending'])
                ->whereMonth('created_at', now()->month)
                ->sum('total_price');
        });
    }

    /**
     * Compute the chart mapping structure generating Sales Overview over the last 7 days.
     *
     * @return array{labels: array<int, string>, data: array<int, float>}
     */
    #[Computed]
    public function chartMap(): array
    {
        return Cache::remember('dashboard.chart-map', now()->addMinutes(60), function () {
            $chartData = Order::where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->whereNotIn('status', ['cancelled'])
                ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(fn($item) => [$item->date => (float) $item->total]);

            $labels = [];
            $data = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $labels[] = now()->subDays($i)->format('M d');
                $data[] = $chartData[$date] ?? 0;
            }

            return [
                'labels' => $labels,
                'data' => $data,
            ];
        });
    }

    /**
     * Compute the top 5 most frequently sold Ready Cakes.
     *
     * @return Collection
     */
    #[Computed]
    public function bestSellers(): Collection
    {
        return Cache::remember('dashboard.best-sellers', now()->addHours(6), function () {
            return OrderItem::whereNotNull('ready_cake_id')
                ->select('ready_cake_id', DB::raw('count(*) as total_sold'))
                ->groupBy('ready_cake_id')
                ->orderByDesc('total_sold')
                ->take(5)
                ->with(['readyCake.media', 'readyCake.cakeShape', 'readyCake.cakeFlavor', 'readyCake.cakeColor', 'readyCake.cakeTopping'])
                ->get();
        });
    }

    /**
     * Compute the 5 most recent orders placed in the system.
     *
     * @return Collection
     */
    #[Computed]
    public function latestOrders(): Collection
    {
        // Cache this for only 5 minutes as it's the most "real-time" element of the dashboard
        return Cache::remember('dashboard.latest-orders', now()->addMinutes(5), function () {
            return Order::with('items')->latest()->take(5)->get();
        });
    }

    /**
     * Compute the primary color setting for the charts layout rendering.
     *
     * @return array{hex: string, rgb: string}
     */
    #[Computed]
    public function chartColorTheme(): array
    {
        $primaryColor = settings(AppearanceSettings::class)->primary_color ?? '#3b82f6';

        $hex = ltrim($primaryColor, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return [
            'hex' => $primaryColor,
            'rgb' => "$r, $g, $b",
        ];
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'shapesCount' => Cache::remember('dashboard.shapes-count', now()->addHours(24), fn() => CakeShape::count()),
            'flavorsCount' => Cache::remember('dashboard.flavors-count', now()->addHours(24), fn() => CakeFlavor::count()),
            'toppingsCount' => Cache::remember('dashboard.toppings-count', now()->addHours(24), fn() => CakeTopping::count()),
            'colorsCount' => Cache::remember('dashboard.colors-count', now()->addHours(24), fn() => CakeColor::count()),
        ];
    }
};
