<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Information;
use App\Models\Ticket;
use App\Models\TicketAssignment;

#[Layout('layouts.app')]
#[Title('HomePage')]
class Home extends Component
{
    public string $activeTab = 'upcoming'; // upcoming|ongoing|past|all
    protected string $tz = 'Asia/Jakarta';

    // Data for announcements & informations
    public $announcements = [];
    public $informations = [];

    // Ticket shortcut data
    public int $ticketQueueCount = 0;        // total tickets in dept queue
    public int $ticketClaimsCount = 0;       // tickets claimed by current user
    public $ticketClaimsPreview = [];
    public $ticketQueuePreview = [];  // Ensure this is used consistently

    public function mount(): void
    {
        $this->loadAnnouncementsAndInformations();
        $this->loadTicketOverview();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['upcoming', 'ongoing', 'past', 'all'], true) ? $tab : 'upcoming';
    }

    protected function loadAnnouncementsAndInformations(): void
    {
        $user = Auth::user();
        $companyId = (int) ($user->company_id ?? 0);
        $deptId = $user->department_id ? (int) $user->department_id : null;

        // Announcements
        $annBase = Announcement::query();
        if (method_exists(Announcement::class, 'scopeForCompany')) {
            $annBase = $annBase->forCompany($companyId);
        } else {
            $annBase = $annBase->where('company_id', $companyId);
        }

        $this->announcements = $annBase
            ->orderBy('event_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Informations (global + user dept)
        $this->informations = Information::forCompany($companyId)
            ->forDepartment($deptId)
            ->orderBy('event_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Fallback: kalau tidak ada untuk dept user, tampilkan semua informasi perusahaan
        if ($this->informations->isEmpty()) {
            $this->informations = Information::forCompany($companyId)
                ->orderBy('event_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
    }

    /**
     * Load ringkasan tiket untuk shortcut di Home.
     * Logika mengikuti Ticketqueue::queueQuery() dan ::claimsQuery()
     */
    protected function loadTicketOverview(): void
    {
        $user = Auth::user();

        // QUEUE: sama logika seperti queueQuery() di Ticketqueue
        $queueBase = Ticket::query()
            ->where('company_id', $user->company_id)
            ->where('department_id', $user->department_id)
            ->where('user_id', '!=', $user->user_id)
            ->where('status', '!=', 'RESOLVED')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereNull('deleted_at');
            });

        $this->ticketQueueCount = (int) $queueBase->count();

        $this->ticketQueuePreview = $queueBase
            ->with(['requester'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // CLAIMS: sama logika seperti claimsQuery() di Ticketqueue
        $claimsBase = TicketAssignment::query()
            ->with(['ticket.requester'])
            ->where('user_id', $user->user_id)
            ->whereNull('deleted_at')
            ->whereRelation('ticket', 'status', '!=', 'CLOSED');

        $this->ticketClaimsCount = (int) $claimsBase->count();

        $this->ticketClaimsPreview = $claimsBase
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.user.home', [
            'announcements'       => $this->announcements,
            'informations'        => $this->informations,
            'activeTab'           => $this->activeTab,
            'ticketQueueCount'    => $this->ticketQueueCount,
            'ticketClaimsCount'   => $this->ticketClaimsCount,
            'ticketClaimsPreview' => $this->ticketClaimsPreview,
            'ticketQueuePreview'  => $this->ticketQueuePreview,  // Ensure this is passed
        ]);
    }
}
    