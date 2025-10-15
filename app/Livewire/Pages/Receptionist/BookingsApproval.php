<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BookingRoom;
use App\Services\GoogleMeetService;
use App\Services\ZoomService;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Bookings Approval')]
class BookingsApproval extends Component
{
    use WithPagination;

    /**
     * Tailwind pagination styles
     */
    protected string $paginationTheme = 'tailwind';

    /**
     * UI state
     */
    public string $filter = 'pending'; // pending|approved|rejected|all
    public string $q = '';             // search by meeting title
    public int $perPage = 12;

    /**
     * Reset page when filters/search change
     */
    public function updatingFilter(): void
    {
        $this->resetPage();
    }
    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function getGoogleConnectedProperty(): bool
    {
        return app(\App\Services\GoogleMeetService::class)->isConnected(\Auth::id());
    }


    /**
     * Approve a booking.
     * - For online meetings, create a real Zoom/Google Meet link (if not already present)
     * - Mark status approved & who approved.
     */
    public function approve(int $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                /** @var BookingRoom $b */
                $b = BookingRoom::lockForUpdate()->findOrFail($id);

                // Create link only for online meetings without link yet
                if ($b->booking_type === 'online_meeting' && empty($b->online_meeting_url)) {
                    $start = Carbon::parse($b->start_time);
                    $end = Carbon::parse($b->end_time);

                    $provider = strtolower((string) $b->online_provider);
                    $provider = str_replace([' ', '-'], '_', $provider);
                    $isGoogle = str_starts_with($provider, 'google');

                    if ($isGoogle) {
                        // Ensure receptionist has connected Google
                        if (!app(\App\Services\GoogleMeetService::class)->isConnected(Auth::id())) {
                            throw new \RuntimeException('Google not connected. Please connect first.');
                        }

                        $meet = app(\App\Services\GoogleMeetService::class)->createMeet(
                            $b->meeting_title,
                            $start,
                            $end,
                            'Auto-created from KRBS approval'
                        );
                    } else {
                        $meet = app(\App\Services\ZoomService::class)->createMeeting(
                            $b->meeting_title,
                            $start,
                            $end,
                            'Auto-created from KRBS approval'
                        );
                    }

                    $b->online_meeting_url = $meet['url'] ?? null;
                    $b->online_meeting_code = $meet['code'] ?? null;
                    $b->online_meeting_password = $meet['password'] ?? null;
                    $b->online_provider = $isGoogle ? 'google_meet' : 'zoom';
                }

                // Mark approved
                $b->status = 'approved';
                $b->approved_by = Auth::id();
                $b->is_approve = 1;
                $b->save();
            });

            $this->dispatch('toast', type: 'success', title: 'Approved', message: 'Booking disetujui & link meeting dibuat.');
        } catch (\RuntimeException $e) {
            // Likely Google not connected
            $this->dispatch('toast', type: 'error', title: 'Connect Google', message: 'Belum terhubung. Klik: ' . url('/google/connect'));
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal menyetujui booking: ' . $e->getMessage());
        }
    }

    /**
     * Reject a booking (or move an approved one back to rejected).
     */
    public function reject(int $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                /** @var BookingRoom $b */
                $b = BookingRoom::lockForUpdate()->findOrFail($id);

                $b->status = 'rejected';
                $b->approved_by = Auth::id();
                $b->is_approve = 0;
                $b->save();
            });

            $this->dispatch('toast', type: 'info', title: 'Rejected', message: 'Booking ditolak.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal menolak booking: ' . $e->getMessage());
        }
    }

    /**
     * Data provider for the page.
     */
    public function render()
    {
        $rows = BookingRoom::query()
            ->when($this->filter !== 'all', fn($q) => $q->where('status', $this->filter))
            ->when($this->q !== '', fn($q) => $q->where('meeting_title', 'like', '%' . $this->q . '%'))
            ->orderByDesc('created_at')
            ->paginate($this->perPage, [
                'bookingroom_id',
                'meeting_title',
                'booking_type',
                'online_provider',
                'online_meeting_url',
                'online_meeting_code',
                'online_meeting_password',
                'status',
                'date',
                'start_time',
                'end_time',
                'user_id',
                'room_id',
                'approved_by',
            ]);

        return view('livewire.pages.receptionist.bookings-approval', compact('rows'));
    }
}
