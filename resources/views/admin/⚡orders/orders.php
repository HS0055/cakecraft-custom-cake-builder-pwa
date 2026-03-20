<?php

use App\Models\Order;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class Orders (Livewire 4 Multi-File Component)
 *
 * This Livewire component handles the administrative listing and filtering
 * of customer Orders in the system.
 */
new #[Layout('layouts::admin', ['title' => 'Orders'])] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filter_status = '';
    public string $filter_fulfillment = '';

    public function mount(): void
    {
        $this->authorize('view orders');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterFulfillment(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter_status = '';
        $this->filter_fulfillment = '';
        $this->resetPage();
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{orders: LengthAwarePaginator}
     */
    public function with(): array
    {
        return [
            'orders' => Order::query()
                ->when($this->search, function ($query) {
                    $query->where('customer_name', 'like', "%{$this->search}%")
                        ->orWhere('customer_phone', 'like', "%{$this->search}%")
                        ->orWhere('id', 'like', "%{$this->search}%");
                })
                ->when($this->filter_status, fn($query) => $query->where('status', $this->filter_status))
                ->when($this->filter_fulfillment, fn($query) => $query->where('fulfillment_type', $this->filter_fulfillment))
                ->latest()
                ->paginate(settings(GeneralSettings::class)->pagination_limit),
        ];
    }
};
