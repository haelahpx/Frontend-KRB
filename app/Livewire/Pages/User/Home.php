<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Information;

#[Layout('layouts.app')]
#[Title('HomePage')]
class Home extends Component
{
    public function render()
    {
        $user = Auth::user();
        $companyId = (int) ($user->company_id ?? 0);

        // Ambil 5 item terdekat berdasar tanggal event
        $announcements = Announcement::forCompany($companyId)
            ->orderBy('event_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $informations = Information::forCompany($companyId)
            ->orderBy('event_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Kalau kamu sudah punya perhitungan ini, ganti sesuai logic-mu.
        $openTicketsCount = 0;
        $upcomingBookings = 0;

        return view('livewire.pages.user.home', [
            'announcements'      => $announcements,
            'informations'       => $informations,
            'openTicketsCount'   => $openTicketsCount,
            'upcomingBookings'   => $upcomingBookings,
        ]);
    }
}
