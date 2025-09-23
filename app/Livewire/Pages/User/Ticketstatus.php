<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Profile')]
class ticketstatus extends Component
{
    public string $dummyStatus = 'process';

    public array $tickets = [
        [
            'ticket_id'   => 12345,
            'user_id'     => 1,
            'subject'     => 'Wifi ruang rapat tidak bisa',
            'description' => 'Wifi di Gedung Konservasi lantai 2 tidak connect sejak pagi.',
            'priority'    => 'HIGH',     // LOW | MEDIUM | HIGH | URGENT
            'status'      => 'pending',  // pending | process | complete
            'created_at'  => '2025-09-22 09:15:00',
            'updated_at'  => '2025-09-22 09:15:00',
            'user'        => [
                'name'  => 'Clania Elmymora',
                'email' => 'user@example.com',
            ],
        ],
        [
            'ticket_id'   => 12346,
            'user_id'     => 2,
            'subject'     => 'Projector Meeting Room flicker',
            'description' => 'Layar berkedip-kedip saat HDMI disambungkan.',
            'priority'    => 'MEDIUM',
            'status'      => 'process',
            'created_at'  => '2025-09-22 10:20:00',
            'updated_at'  => '2025-09-22 11:05:00',
            'user'        => [
                'name'  => 'Bintang Putra',
                'email' => 'bintang@example.com',
            ],
        ],
        [
            'ticket_id'   => 12347,
            'user_id'     => 3,
            'subject'     => 'Request akun email baru',
            'description' => 'Butuh akun email untuk staf magang departemen IT.',
            'priority'    => 'LOW',
            'status'      => 'complete',
            'created_at'  => '2025-09-21 14:00:00',
            'updated_at'  => '2025-09-21 16:35:00',
            'user'        => [
                'name'  => 'Nadia Prameswari',
                'email' => 'nadia@example.com',
            ],
        ],
        [
            'ticket_id'   => 12348,
            'user_id'     => 4,
            'subject'     => 'Akses aplikasi internal error 500',
            'description' => 'Setelah login, aplikasi menampilkan error 500.',
            'priority'    => 'URGENT',
            'status'      => 'pending',
            'created_at'  => '2025-09-23 08:05:00',
            'updated_at'  => '2025-09-23 08:05:00',
            'user'        => [
                'name'  => 'Raka Mahendra',
                'email' => 'raka@example.com',
            ],
        ],
    ];

    public function render()
    {
        return view('livewire.pages.user.ticketstatus');
    }
}
