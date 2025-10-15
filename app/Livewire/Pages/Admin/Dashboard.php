<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

#[Layout('layouts.admin')]
#[Title('Admin - Dashboard')]
class Dashboard extends Component
{
    public string $admin_name = 'Admin';

    // KPI stats
    public array $stats = [];

    // Ticketing
    public array $recentTickets = [];

    // Comments (latest 3 only)
    public array $latestComments = [];

    // Booking Room
    public array $recentBookings = [];

    // Activity
    public array $recentActivities = [];

    public function tick() {}

    public function mount(): void
    {
        $this->admin_name = trim(Auth::user()->full_name ?? Auth::user()->name ?? 'Admin');
        $this->refreshData();
    }

    public function render()
    {
        $this->refreshData();
        return view('livewire.pages.admin.dashboard', [
            'admin_name'       => $this->admin_name,
            'stats'            => $this->stats,
            'recentTickets'    => $this->recentTickets,
            'latestComments'   => $this->latestComments,
            'recentBookings'   => $this->recentBookings,
            'recentActivities' => $this->recentActivities,
        ]);
    }

    /* ---------------- refresh ---------------- */

    private function refreshData(): void
    {
        $this->stats            = $this->buildStats();
        $this->recentTickets    = $this->buildRecentTickets();
        $this->latestComments   = $this->buildLatestComments();
        $this->recentBookings   = $this->buildRecentBookings();
        $this->recentActivities = $this->buildRecentActivities(limit: 10);
    }

    /* ---------------- KPIs ---------------- */

    private function buildStats(): array
    {
        return [
            ['label' => 'Open Tickets',   'value' => number_format($this->countTicketsByStatus('OPEN'))],
            ['label' => 'In Progress',    'value' => number_format($this->countTicketsByStatus('IN_PROGRESS'))],
            ['label' => 'Resolved',       'value' => number_format($this->countTicketsByStatus('RESOLVED'))],
            ['label' => 'Closed',         'value' => number_format($this->countTicketsByStatus('CLOSED'))],
            ['label' => 'Today',          'value' => number_format($this->countTicketsToday())],
            ['label' => 'Unassigned',     'value' => number_format($this->countTicketsUnassigned())],
        ];
    }

    private function countTicketsByStatus(string $status): int
    {
        if (!$this->tableExists('tickets') || !$this->columnExists('tickets', 'status')) {
            return 0;
        }
        return (int) DB::table('tickets')
            ->whereRaw('UPPER(status) = ?', [strtoupper($status)])
            ->count();
    }

    private function countTicketsToday(): int
    {
        if (!$this->tableExists('tickets') || !$this->columnExists('tickets', 'created_at')) {
            return 0;
        }
        return (int) DB::table('tickets')
            ->whereDate('created_at', Carbon::now('Asia/Jakarta')->toDateString())
            ->count();
    }

    private function countTicketsUnassigned(): int
    {
        if (!$this->tableExists('tickets')) return 0;

        if ($this->columnExists('tickets', 'assignment_id')) {
            return (int) DB::table('tickets')->whereNull('assignment_id')->count();
        }

        if ($this->tableExists('ticket_assignments')) {
            $tId = $this->chooseFirstExisting('tickets', ['ticket_id','id']);
            return (int) DB::table('tickets')
                ->leftJoin('ticket_assignments as ta', 'ta.ticket_id', '=', 'tickets.'.$tId)
                ->whereNull('ta.ticket_id')
                ->count();
        }

        return 0;
    }

    /* ---------------- Tickets ---------------- */

    private function buildRecentTickets(): array
    {
        if (!$this->tableExists('tickets')) return [];

        $idCol   = $this->chooseFirstExisting('tickets', ['ticket_id', 'id']);
        $subCol  = $this->columnExists('tickets', 'subject') ? 'subject' : null;
        $priCol  = $this->columnExists('tickets', 'priority') ? 'priority' : null;
        $statCol = $this->columnExists('tickets', 'status') ? 'status' : null;
        $userCol = $this->columnExists('tickets', 'user_id') ? 'user_id' : null;
        $deptCol = $this->columnExists('tickets', 'department_id') ? 'department_id' : null;
        $timeCol = $this->columnExists('tickets', 'created_at') ? 'created_at' : null;

        $q = DB::table('tickets');

        if ($statCol) $q->where('tickets.'.$statCol, '!=', 'DELETED');

        if ($userCol && $this->tableExists('users') && $this->columnExists('users', 'user_id')) {
            $q->leftJoin('users', 'users.user_id', '=', 'tickets.'.$userCol);
        }
        if ($deptCol && $this->tableExists('departments') && $this->columnExists('departments', 'department_id')) {
            $q->leftJoin('departments', 'departments.department_id', '=', 'tickets.'.$deptCol);
        }

        $selects = ['tickets.'.$idCol.' as id'];
        if ($subCol)  $selects[] = 'tickets.'.$subCol.' as subject';
        if ($priCol)  $selects[] = 'tickets.'.$priCol.' as priority';
        if ($statCol) $selects[] = 'tickets.'.$statCol.' as status';
        if ($timeCol) $selects[] = 'tickets.'.$timeCol.' as created_at';

        if ($this->tableExists('users')) {
            $selects[] = $this->sqlCoalesceExisting('users', ['full_name','name','email','user_id'], 'user_name');
        } else {
            $selects[] = DB::raw('"User" as user_name');
        }

        if ($this->tableExists('departments') && $this->columnExists('departments','department_name')) {
            $selects[] = 'departments.department_name as dept_name';
        } else {
            $selects[] = DB::raw('NULL as dept_name');
        }

        $rows = $q->orderByDesc($timeCol ?? $idCol)
            ->limit(6)
            ->get($selects);

        return collect($rows)->map(function ($r) {
            $id = (int)($r->id ?? 0);
            return [
                'id'       => $id,
                'subject'  => (string)($r->subject ?? '(no subject)'),
                'priority' => strtolower((string)($r->priority ?? '')),
                'status'   => strtolower((string)($r->status ?? '')),
                'user'     => (string)($r->user_name ?? 'User'),
                'dept'     => (string)($r->dept_name ?? '-'),
                'when'     => isset($r->created_at) ? Carbon::parse($r->created_at)->diffForHumans() : '-',
                'url'      => url('/admin/tickets/'.$id),
            ];
        })->all();
    }

    /* ---------------- Comments (latest 3) ---------------- */

    private function buildLatestComments(): array
    {
        if (!$this->tableExists('ticket_comments')) return [];

        $idCol    = $this->chooseFirstExisting('ticket_comments', ['comment_id', 'id']);
        $ticketId = $this->columnExists('ticket_comments', 'ticket_id') ? 'ticket_id' : null;
        $textCol  = $this->columnExists('ticket_comments', 'comment_text') ? 'comment_text' : null;
        $userCol  = $this->columnExists('ticket_comments', 'user_id') ? 'user_id' : null;
        $timeCol  = $this->columnExists('ticket_comments', 'created_at') ? 'created_at' : null;

        $q = DB::table('ticket_comments');

        if ($ticketId && $this->tableExists('tickets')) {
            $tIdCol = $this->chooseFirstExisting('tickets', ['ticket_id', 'id']);
            $q->leftJoin('tickets', 'tickets.'.$tIdCol, '=', 'ticket_comments.'.$ticketId);
        }
        if ($userCol && $this->tableExists('users') && $this->columnExists('users', 'user_id')) {
            $q->leftJoin('users', 'users.user_id', '=', 'ticket_comments.'.$userCol);
        }

        $selects = [
            'ticket_comments.'.$idCol.' as id',
            $ticketId ? 'ticket_comments.'.$ticketId.' as ticket_id' : DB::raw('NULL as ticket_id'),
            $textCol ? 'ticket_comments.'.$textCol.' as comment_text' : DB::raw('NULL as comment_text'),
            $timeCol ? 'ticket_comments.'.$timeCol.' as created_at'   : DB::raw('NULL as created_at'),
        ];

        if ($this->tableExists('tickets') && $this->columnExists('tickets','subject')) {
            $selects[] = 'tickets.subject as ticket_subject';
        } else {
            $selects[] = DB::raw('"(no subject)" as ticket_subject');
        }

        if ($this->tableExists('users')) {
            $selects[] = $this->sqlCoalesceExisting('users', ['full_name','name','email','user_id'], 'by_name');
        } else {
            $selects[] = DB::raw('"User" as by_name');
        }

        $rows = $q->orderByDesc($timeCol ?? $idCol)
            ->limit(3)
            ->get($selects);

        return collect($rows)->map(function ($c) {
            $cid = (int)($c->id ?? 0);
            $tid = (int)($c->ticket_id ?? 0);
            return [
                'id'             => $cid,
                'ticket_id'      => $tid,
                'ticket_subject' => (string)($c->ticket_subject ?? '(no subject)'),
                'by'             => (string)($c->by_name ?? 'User'),
                'text'           => (string) Str::of((string)($c->comment_text ?? ''))->limit(120),
                'when'           => isset($c->created_at) ? Carbon::parse($c->created_at)->diffForHumans() : '-',
                'url'            => url('/admin/tickets/'.$tid).'#comment-'.$cid,
            ];
        })->all();
    }

    /* ---------------- Bookings ---------------- */

    private function buildRecentBookings(): array
    {
        if (!$this->tableExists('booking_rooms')) return [];

        $idCol   = $this->chooseFirstExisting('booking_rooms', ['bookingroom_id', 'id']);
        $title   = $this->chooseFirstExisting('booking_rooms', ['meeting_title', 'title']);
        $roomId  = $this->columnExists('booking_rooms', 'room_id') ? 'room_id' : null;
        $userCol = $this->columnExists('booking_rooms', 'user_id') ? 'user_id' : null;
        $timeCol = $this->columnExists('booking_rooms', 'created_at') ? 'created_at' : null;

        $q = DB::table('booking_rooms');

        if ($roomId && $this->tableExists('rooms')) {
            $roomKey = $this->chooseFirstExisting('rooms', ['room_id', 'id']);
            $q->leftJoin('rooms', 'rooms.'.$roomKey, '=', 'booking_rooms.'.$roomId);
        }
        if ($userCol && $this->tableExists('users') && $this->columnExists('users', 'user_id')) {
            $q->leftJoin('users', 'users.user_id', '=', 'booking_rooms.'.$userCol);
        }

        $selects = [
            'booking_rooms.'.$idCol.' as id',
            $title ? 'booking_rooms.'.$title.' as meeting_title' : DB::raw('"(Meeting)" as meeting_title'),
            $timeCol ? 'booking_rooms.'.$timeCol.' as created_at' : DB::raw('NULL as created_at'),
        ];

        $roomLabel = 'NULL';
        if ($this->tableExists('rooms')) {
            if ($this->columnExists('rooms', 'room_number')) {
                $roomLabel = 'rooms.room_number';
            } elseif ($this->columnExists('rooms', 'room_name')) {
                $roomLabel = 'rooms.room_name';
            }
        }
        $selects[] = DB::raw("$roomLabel as room_label");

        if ($this->tableExists('users')) {
            $selects[] = $this->sqlCoalesceExisting('users', ['full_name','name','email','user_id'], 'by_name');
        } else {
            $selects[] = DB::raw('"User" as by_name');
        }

        $rows = $q->orderByDesc($timeCol ?? $idCol)
            ->limit(5)
            ->get($selects);

        return collect($rows)->map(function ($b) {
            $id = (int)($b->id ?? 0);
            $room = $b->room_label ?? null;
            return [
                'id'            => $id,
                'meeting_title' => (string)($b->meeting_title ?? 'Meeting'),
                'room_label'    => $room ? (string)$room : 'Room #'.$id,
                'by'            => (string)($b->by_name ?? 'User'),
                'when'          => isset($b->created_at) ? Carbon::parse($b->created_at)->diffForHumans() : '-',
                'url'           => url('/admin/room-monitoring#booking-'.$id),
            ];
        })->all();
    }

    /* ---------------- Activity (users/tickets/comments/bookings) ---------------- */

    private function buildRecentActivities(int $limit = 10): array
    {
        $items = collect();

        // ticket created
        if ($this->tableExists('tickets')) {
            $idCol = $this->chooseFirstExisting('tickets', ['ticket_id','id']);
            $rows = DB::table('tickets')
                ->when($this->columnExists('tickets','status'), fn($q) => $q->where('status','!=','DELETED'))
                ->orderByDesc($this->columnExists('tickets','created_at') ? 'created_at' : $idCol)
                ->limit($limit)
                ->get([$idCol.' as id', 'subject', 'created_at']);
            $items = $items->merge($rows->map(function($r){
                return [
                    'type'  => 'ticket',
                    'icon'  => '🎫',
                    'title' => 'New Ticket',
                    'desc'  => '#'.$r->id.' — '.($r->subject ?? '(no subject)'),
                    'url'   => url('/admin/tickets/'.(int)$r->id),
                    'ts'    => isset($r->created_at) ? Carbon::parse($r->created_at)->timestamp : 0,
                    'when'  => isset($r->created_at) ? Carbon::parse($r->created_at)->diffForHumans() : '-',
                ];
            }));
        }

        // comment added
        if ($this->tableExists('ticket_comments')) {
            $idCol = $this->chooseFirstExisting('ticket_comments', ['comment_id','id']);
            $rows = DB::table('ticket_comments')
                ->orderByDesc($this->columnExists('ticket_comments','created_at') ? 'created_at' : $idCol)
                ->limit($limit)
                ->get([$idCol.' as id', 'ticket_id', 'comment_text', 'created_at']);
            $items = $items->merge($rows->map(function($c){
                return [
                    'type'  => 'comment',
                    'icon'  => '💬',
                    'title' => 'New Comment',
                    'desc'  => 'On ticket #'.(int)$c->ticket_id.' — '.Str::of((string)($c->comment_text ?? ''))->limit(80),
                    'url'   => url('/admin/tickets/'.(int)$c->ticket_id).'#comment-'.(int)$c->id,
                    'ts'    => isset($c->created_at) ? Carbon::parse($c->created_at)->timestamp : 0,
                    'when'  => isset($c->created_at) ? Carbon::parse($c->created_at)->diffForHumans() : '-',
                ];
            }));
        }

        // booking created
        if ($this->tableExists('booking_rooms')) {
            $idCol = $this->chooseFirstExisting('booking_rooms', ['bookingroom_id','id']);
            $rows = DB::table('booking_rooms')
                ->orderByDesc($this->columnExists('booking_rooms','created_at') ? 'created_at' : $idCol)
                ->limit($limit)
                ->get([$idCol.' as id', 'meeting_title', 'created_at']);
            $items = $items->merge($rows->map(function($b){
                return [
                    'type'  => 'booking',
                    'icon'  => '📅',
                    'title' => 'Room Booking',
                    'desc'  => ($b->meeting_title ?? 'Meeting'),
                    'url'   => url('/admin/room-monitoring#booking-'.(int)$b->id),
                    'ts'    => isset($b->created_at) ? Carbon::parse($b->created_at)->timestamp : 0,
                    'when'  => isset($b->created_at) ? Carbon::parse($b->created_at)->diffForHumans() : '-',
                ];
            }));
        }

        // user created / updated
        if ($this->tableExists('users')) {
            $idCol = $this->chooseFirstExisting('users', ['user_id','id']);
            // created
            $created = DB::table('users')
                ->orderByDesc($this->columnExists('users','created_at') ? 'created_at' : $idCol)
                ->limit((int)ceil($limit/2))
                ->get([$idCol.' as id', 'created_at']);
            $items = $items->merge($created->map(function($u){
                return [
                    'type'  => 'user_created',
                    'icon'  => '👤',
                    'title' => 'New User',
                    'desc'  => 'User #'.(int)$u->id.' created',
                    'url'   => url('/admin/user-management#user-'.(int)$u->id),
                    'ts'    => isset($u->created_at) ? Carbon::parse($u->created_at)->timestamp : 0,
                    'when'  => isset($u->created_at) ? Carbon::parse($u->created_at)->diffForHumans() : '-',
                ];
            }));
            // updated
            if ($this->columnExists('users','updated_at')) {
                $updated = DB::table('users')
                    ->orderByDesc('updated_at')
                    ->limit((int)floor($limit/2))
                    ->get([$idCol.' as id', 'updated_at']);
                $items = $items->merge($updated->map(function($u){
                    return [
                        'type'  => 'user_updated',
                        'icon'  => '🛠',
                        'title' => 'User Updated',
                        'desc'  => 'User #'.(int)$u->id.' was updated',
                        'url'   => url('/admin/user-management#user-'.(int)$u->id),
                        'ts'    => isset($u->updated_at) ? Carbon::parse($u->updated_at)->timestamp : 0,
                        'when'  => isset($u->updated_at) ? Carbon::parse($u->updated_at)->diffForHumans() : '-',
                    ];
                }));
            }
        }

        return $items
            ->sortByDesc('ts')
            ->take($limit)
            ->values()
            ->all();
    }

    /* ---------------- helpers ---------------- */

    private function tableExists(string $table): bool
    {
        try { return Schema::hasTable($table); } catch (\Throwable $e) { return false; }
    }

    private function columnExists(string $table, string $col): bool
    {
        try { return Schema::hasColumn($table, $col); } catch (\Throwable $e) { return false; }
    }

    private function chooseFirstExisting(string $table, array $candidates): string
    {
        foreach ($candidates as $c) {
            if ($this->columnExists($table, $c)) return $c;
        }
        return $candidates[0];
    }

    /** Build COALESCE(...) using only existing columns; fallback to CAST(id) or literal. */
    private function sqlCoalesceExisting(string $table, array $cols, string $alias)
    {
        $parts = [];
        foreach ($cols as $c) {
            if ($this->columnExists($table, $c)) {
                if (strtolower($c) === 'user_id' || strtolower($c) === 'id') {
                    $parts[] = "CAST($table.$c AS CHAR)";
                } else {
                    $parts[] = "$table.$c";
                }
            }
        }
        if (empty($parts)) {
            return DB::raw('"User" as '.$alias);
        }
        $expr = 'COALESCE('.implode(', ', $parts).') as '.$alias;
        return DB::raw($expr);
    }
}
