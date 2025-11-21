<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use App\Models\Information;
use App\Models\Ticket;
use App\Models\TicketAssignment;

#[Layout('layouts.app')]
#[Title('HomePage')]
class Home extends Component
{
    public string $activeTab = 'upcoming';
    protected string $tz = 'Asia/Jakarta';

    // Data Announcements & Informations
    public $announcements = [];
    public $informations = [];

    // --- [DATA LAMA: AGENT VIEW] ---
    // (Tetap disimpan agar kompatibel jika user adalah agent atau view memanggilnya)
    public int $ticketQueueCount = 0;
    public int $ticketClaimsCount = 0;
    public $ticketClaimsPreview = [];
    public $ticketQueuePreview = [];

    // --- [DATA BARU: USER REQUESTER SUMMARY] ---
    // (Variabel untuk Statistik Tiket di Bento Grid)
    public int $totalTickets = 0;
    public int $ticketsOpen = 0;
    public int $ticketsProgress = 0;
    public int $ticketsResolved = 0;
    public int $ticketsClosed = 0;

    // --- [DATA BARU: SYSTEM HEALTH] ---
    // (Variabel untuk Support Center Realtime)
    public string $dbStatus = 'Checking...';
    public string $serverStatus = 'Checking...';
    
    // Network variables (Latency & Warna)
    public string $networkLatency = '...';
    public string $networkColor = 'text-gray-500 bg-gray-100'; // Default color

    public function mount(): void
    {
        $this->loadAnnouncementsAndInformations();
        // Panggil checkSystemHealth saat pertama load. 
        // Fungsi ini juga akan memanggil loadTicketOverview() di dalamnya.
        $this->checkSystemHealth(); 
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['upcoming', 'ongoing', 'past', 'all'], true) ? $tab : 'upcoming';
    }

    /**
     * Load Announcements & Informations
     */
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

        // Informations
        $this->informations = Information::forCompany($companyId)
            ->forDepartment($deptId)
            ->orderBy('event_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($this->informations->isEmpty()) {
            $this->informations = Information::forCompany($companyId)
                ->orderBy('event_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
    }

    /**
     * Logic Realtime Check System Health
     * Dipanggil otomatis oleh wire:poll setiap 10 detik dari Blade
     */
    public function checkSystemHealth()
    {
        // 1. Refresh Data Tiket (Agar angka tiket juga selalu update)
        $this->loadTicketOverview();

        // 2. Cek Koneksi Database
        try {
            DB::connection()->getPdo();
            $this->dbStatus = 'OK';
        } catch (\Exception $e) {
            $this->dbStatus = 'DOWN';
        }

        // 3. Cek Kapasitas Server (Disk Space)
        // Menggunakan '.' (current dir) agar kompatibel di berbagai OS/Hosting
        $freeSpace = @disk_free_space('.');
        $totalSpace = @disk_total_space('.');
        
        if ($freeSpace !== false && $totalSpace !== false && $totalSpace > 0) {
            $percentageFree = ($freeSpace / $totalSpace) * 100;
            $this->serverStatus = $percentageFree > 10 ? 'OK' : 'FULL';
        } else {
            $this->serverStatus = 'Unknown';
        }

        // 4. Cek Network Latency (Ping ke Google) & Tentukan Warna
        $startTime = microtime(true);
        try {
            $fsock = @fsockopen('www.google.com', 80, $errno, $errstr, 1); // Timeout 1 detik
            if ($fsock) {
                $endTime = microtime(true);
                $latency = round(($endTime - $startTime) * 1000);
                $this->networkLatency = $latency . 'ms';
                fclose($fsock);

                // Logika Warna Network berdasarkan kecepatan (ms)
                if ($latency < 150) {
                    $this->networkColor = 'text-green-700 bg-green-100'; // Cepat (Hijau)
                } elseif ($latency < 400) {
                    $this->networkColor = 'text-yellow-700 bg-yellow-100'; // Sedang (Kuning)
                } else {
                    $this->networkColor = 'text-red-700 bg-red-100'; // Lambat (Merah)
                }

            } else {
                $this->networkLatency = 'Timeout';
                $this->networkColor = 'text-red-700 bg-red-100';
            }
        } catch (\Exception $e) {
            $this->networkLatency = 'Offline';
            $this->networkColor = 'text-red-700 bg-red-100';
        }
    }

    /**
     * Load ringkasan tiket (User Summary & Agent Queue)
     */
    protected function loadTicketOverview(): void
    {
        $user = Auth::user();

        // ==========================================
        // 1. LOGIC BARU: USER TICKET SUMMARY (REQUESTER)
        // ==========================================
        // Statistik untuk User Dashboard (Bento Grid)
        // Menggunakan selectRaw untuk efisiensi query (1 query dapat semua status)
        $stats = Ticket::where('user_id', $user->user_id)
            ->selectRaw("count(*) as total")
            ->selectRaw("count(case when status = 'OPEN' then 1 end) as open")
            ->selectRaw("count(case when status = 'IN_PROGRESS' then 1 end) as progress")
            ->selectRaw("count(case when status = 'RESOLVED' then 1 end) as resolved")
            ->selectRaw("count(case when status = 'CLOSED' then 1 end) as closed")
            ->first();

        $this->totalTickets    = $stats->total ?? 0;
        $this->ticketsOpen     = $stats->open ?? 0;
        $this->ticketsProgress = $stats->progress ?? 0;
        $this->ticketsResolved = $stats->resolved ?? 0;
        $this->ticketsClosed   = $stats->closed ?? 0;

        // ==========================================
        // 2. LOGIC LAMA: AGENT QUEUE & CLAIMS
        // ==========================================
        // Disimpan untuk kompatibilitas jika user juga seorang Agent
        
        // QUEUE (Tiket departemen yang belum diambil)
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
            ->with(['user']) // Relasi ke user pembuat tiket
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // CLAIMS (Tiket yang sedang ditangani user ini)
        $claimsBase = TicketAssignment::query()
            ->with(['ticket.user'])
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
        return view('livewire.pages.user.home');
    }
}