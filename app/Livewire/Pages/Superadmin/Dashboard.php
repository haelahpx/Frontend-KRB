<?php

namespace App\Livewire\Pages\Superadmin;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Superadmin Dashboard')]
class Dashboard extends Component
{
    public string $admin_name = '';

    public function mount(): void
    {
        // Get the admin's name once on load
        $this->admin_name = Auth::user()->full_name ?? 'Admin';
    }

    public function render()
    {
        // STAT CARDS
        $stats = [
            [
                'label' => 'Total Companies',
                'value' => number_format(Company::count()),
            ],
            [
                'label' => 'Total Users',
                'value' => number_format(User::count()),
            ],
            [
                'label' => 'Total Departments',
                'value' => number_format(Department::count()),
            ],
            [
                'label' => 'Announcements (This Month)',
                'value' => number_format(Announcement::whereMonth('created_at', now()->month)->count()),
            ],
        ];

        // WIDGETS
        $recentUsers = User::with('company')
            ->latest()
            ->take(5)
            ->get();

        $companiesByUserCount = Company::withCount('users')
            ->orderByDesc('users_count')
            ->take(5)
            ->get();

        $recentAnnouncements = Announcement::with('company')
            ->latest()
            ->take(5)
            ->get();


        return view('livewire.pages.superadmin.dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'companiesByUserCount' => $companiesByUserCount,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }
}