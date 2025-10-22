<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Ticket;
use App\Models\Department;

#[Layout('layouts.superadmin')]
#[Title('Ticket Support')]
class Ticketsupport extends Component
{
    use WithPagination;

    public $search = '';
    public $departmentFilter = '';
    public $priorityFilter = '';
    public $perPage = 10;
    public bool $showDeleted = false;

    // Modal / edit props
    public $modal = false;
    public $editingTicketId = null;
    public $subject, $description, $priority, $department_id, $status;

    public $deptLookup = [];

    protected $queryString = ['search', 'departmentFilter', 'priorityFilter', 'perPage', 'showDeleted'];

    protected $rules = [
        'subject' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'department_id' => 'nullable|exists:departments,department_id',
        'status' => 'nullable|string',
    ];

    public function mount()
    {
        // departments table uses department_id and department_name
        $this->deptLookup = Department::pluck('department_name', 'department_id')->toArray();
    }

    // Reset pagination when filters change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingDepartmentFilter() { $this->resetPage(); }
    public function updatingPriorityFilter() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatedShowDeleted() { $this->resetPage(); }

    public function render()
    {
        $query = Ticket::with(['user', 'attachments', 'department'])
            ->orderBy('created_at', 'desc');

        // If showDeleted = true, include soft-deleted tickets
        if ($this->showDeleted) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', $s)
                    ->orWhere('description', 'like', $s)
                    ->orWhereHas('user', function ($qu) use ($s) {
                        $qu->where('full_name', 'like', $s)
                            ->orWhere('email', 'like', $s);
                    });
            });
        }

        if ($this->departmentFilter) {
            $query->where('department_id', $this->departmentFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $tickets = $query->paginate($this->perPage);

        return view('livewire.pages.superadmin.ticketsupport', [
            'tickets' => $tickets,
            'deptLookup' => $this->deptLookup,
        ]);
    }

    // Open modal to edit ticket
    public function openEdit($id)
    {
        $t = Ticket::withTrashed()->findOrFail($id);

        $this->editingTicketId = $t->ticket_id;
        $this->subject = $t->subject;
        $this->description = $t->description;
        $this->priority = $t->priority ?? 'low';
        $this->department_id = $t->department_id;
        $this->status = $t->status ?? null;
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->modal = false;
        $this->resetValidation();
    }

    public function update()
    {
        $this->validate();

        $t = Ticket::withTrashed()->findOrFail($this->editingTicketId);
        $t->update([
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'department_id' => $this->department_id,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Ticket updated.');
        $this->closeModal();
    }

    // Soft delete ticket
    public function delete($id)
    {
        $t = Ticket::findOrFail($id);
        $t->delete(); // soft delete
        session()->flash('success', 'Ticket moved to recycle bin (soft deleted).');
        $this->resetPage();
    }

    // Restore ticket
    public function restore($id)
    {
        $t = Ticket::onlyTrashed()->findOrFail($id);
        $t->restore();
        session()->flash('success', 'Ticket restored successfully.');
        $this->resetPage();
    }
}
