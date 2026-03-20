<?php

use App\Models\SettingsAuditLog;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class AuditLog (Livewire 4 Multi-File Component)
 *
 * This Livewire component governs the viewing and advanced filtering
 * interface for tracing changes logged within the System Settings configuration.
 */
new #[Layout('layouts::admin', ['title' => 'Settings Audit Log'])] class extends Component {
    use WithPagination;

    public $search = '';
    public $groupFilter = '';
    public $actionFilter = '';
    public $dateFilter = '';
    public int $perPage = 15;

    public $selectedLog = null;
    public $showDiffModal = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('view settings audit');
        $this->perPage = settings(GeneralSettings::class)->pagination_limit;
    }

    public function viewDiff($logId)
    {
        $this->selectedLog = SettingsAuditLog::find($logId);
        $this->showDiffModal = true;
    }

    public function closeDiffModal()
    {
        $this->showDiffModal = false;
        $this->selectedLog = null;
    }

    /**
     * Provide component state exclusively to the view file using Livewire 4 array mapping.
     *
     * @return array{logs: LengthAwarePaginator, groups: mixed}
     */
    public function with(): array
    {
        try {
            $logs = SettingsAuditLog::with('user')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('key', 'like', '%' . $this->search . '%')
                            ->orWhere('old_value', 'like', '%' . $this->search . '%')
                            ->orWhere('new_value', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->groupFilter, function ($query) {
                    $query->where('group', $this->groupFilter);
                })
                ->when($this->actionFilter, function ($query) {
                    $query->where('action', $this->actionFilter);
                })
                ->when($this->dateFilter, function ($query) {
                    $query->whereDate('created_at', $this->dateFilter);
                })
                ->latest()
                ->paginate($this->perPage);

            return [
                'logs' => $logs,
                'groups' => SettingsAuditLog::distinct('group')->pluck('group'),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to parse audit logs: ' . $e->getMessage());

            // Return an empty, safe paginator instead of crashing the view hard
            return [
                'logs' => new Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'groups' => collect([]),
            ];
        }
    }
};
