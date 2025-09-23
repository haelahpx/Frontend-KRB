<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
#[Title('Profile')]
class Profile extends Component
{
    // state untuk Blade/Alpine
    public array $profile = [];
    public array $stats = [];

    // field password (pakai konvensi *_confirmation untuk "confirmed")
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['department', 'company', 'role']);

        $this->profile = [
            'fullName'   => $user->name,
            'email'      => $user->email,
            'phone_number'      => $user->phone_number ?? null,
            'employeeId' => $user->employee_id ?? null,
            'department' => optional($user->department)->department_name ?? '-',
            'company'     => optional($user->company)->company_name ?? '-',
            'joinDate'   => optional($user->joined_at ?? $user->created_at)?->format('M Y'),
            'role'       => optional($user->role)->name ?? (is_string($user->role) ? $user->role : '-'),
        ];

        // Sesuaikan ini dengan modelmu: tickets(), bookings(), packages()
        $this->stats = [
            'openTickets'    => method_exists($user, 'tickets')  ? $user->tickets()->where('status', 'open')->count() : 0,
            'activeBookings' => method_exists($user, 'bookings') ? $user->bookings()->where('status', 'active')->count() : 0,
            'packages'       => method_exists($user, 'packages') ? $user->packages()->count() : 0,
            'memberSince'    => optional($user->created_at)?->format('M Y'),
        ];
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password'            => ['required', 'current_password'],
            'new_password'                => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ], [], [
            'current_password' => 'current password',
            'new_password'     => 'new password',
        ]);

        $user = Auth::user();
        $user->forceFill(['password' => Hash::make($this->new_password)])->save();

        // bersihkan field & kasih event ke front-end
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('password-updated');
    }

    public function render()
    {
        return view('livewire.pages.user.profile');
    }
}
