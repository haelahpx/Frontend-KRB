<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// ========== Controllers ==========
use App\Http\Controllers\AttachmentController;

// ========== Livewire Pages (User) ==========
use App\Livewire\Pages\User\Home as UserHome;
use App\Livewire\Pages\User\CreateTicket;
use App\Livewire\Pages\User\Bookroom;
use App\Livewire\Pages\User\Profile;
use App\Livewire\Pages\User\Package;
use App\Livewire\Pages\User\Ticketstatus;
use App\Livewire\Pages\User\BookingStatus;
use App\Livewire\Pages\User\Ticketshow;

// ========== Livewire Pages (Admin / Superadmin / Receptionist) ==========
use App\Livewire\Pages\Admin\Dashboard as AdminDashboard;
use App\Livewire\Pages\Admin\Ticket as AdminTicket;

use App\Livewire\Pages\Superadmin\Dashboard as SuperadminDashboard;
use App\Livewire\Pages\Superadmin\Announcement;
use App\Livewire\Pages\Superadmin\Information;
use App\Livewire\Pages\Superadmin\Account as UserManagement;
use App\Livewire\Pages\Superadmin\Department as DepartmentPage;
use App\Livewire\Pages\Superadmin\Bookingroom as SuperadminBookingroom;
use App\Livewire\Pages\Superadmin\Ticketsupport as SuperadminTicketsupport;
use App\Livewire\pages\Superadmin\Manageroom as Manageroom;
use App\Livewire\Pages\Superadmin\Managerequirement as Managerequirements;

use App\Livewire\Pages\Receptionist\Dashboard as ReceptionistDashboard;
use App\Livewire\Pages\Receptionist\Documents as Documents;
use App\Livewire\Pages\Receptionist\Calendar as CalendarPage;
use App\Livewire\Pages\Receptionist\Guestbook as Guestbook;
use App\Livewire\Pages\Receptionist\MeetingSchedule as MeetingSchedule;

// ========== Auth Pages ==========
use App\Livewire\Pages\Auth\Login as LoginPage;
use App\Livewire\Pages\Auth\Register as RegisterPage;

// ========== Error ==========
use App\Livewire\Pages\Errors\error404 as Error404;

/*
|--------------------------------------------------------------------------
| Root: arahkan sesuai status login & role
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $roleName = $user->role->name ?? $user->role ?? null;

    return match ($roleName) {
        'Superadmin'   => redirect()->route('superadmin.dashboard'),
        'Admin'        => redirect()->route('admin.dashboard'),
        'Receptionist' => redirect()->route('receptionist.dashboard'),
        default        => redirect()->route('user.home'),
    };
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest only
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
});

/*
|--------------------------------------------------------------------------
| Auth only
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ---------- User routes ----------
    Route::get('/dashboard', UserHome::class)->name('user.home');
    Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');

    // Booking room (User)
    Route::get('/book-room', Bookroom::class)->name('book-room');
    Route::get('/bookingstatus', BookingStatus::class)->name('bookingstatus');

    // Profile & others
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/package', Package::class)->name('package');
    Route::get('/ticketstatus', Ticketstatus::class)->name('ticketstatus');
    Route::get('/tickets/{ticket:ticket_id}', Ticketshow::class)->name('user.ticket.show');

    // ---------- Attachments API ----------
    Route::post('/attachments/signature', [AttachmentController::class, 'signature'])
        ->name('attachments.signature');

    Route::post('/attachments', [AttachmentController::class, 'store'])
        ->name('attachments.store');

    Route::get('/tickets/{ticket}/attachments', [AttachmentController::class, 'index'])
        ->whereNumber('ticket')
        ->name('attachments.index');

    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])
        ->whereNumber('attachment')
        ->name('attachments.destroy');

    // Temporary attachments (sebelum ticket dibuat)
    Route::post('/attachments/signature-temp', [AttachmentController::class, 'signatureTemp'])
        ->name('attachments.signatureTemp');

    Route::delete('/attachments/temp', [AttachmentController::class, 'deleteTemp'])
        ->name('attachments.deleteTemp');

    Route::post('/tickets/finalize-attachments', [AttachmentController::class, 'finalizeTemp'])
        ->name('attachments.finalizeTemp');

    // ---------- Admin routes ----------
    Route::middleware('is.admin')->group(function () {
        Route::get('/admin-dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin-ticket', AdminTicket::class)->name('admin.ticket');
    });

    // ---------- Superadmin routes ----------
    Route::middleware('is.superadmin')->group(function () {
        Route::get('/superadmin-dashboard', SuperadminDashboard::class)->name('superadmin.dashboard');
        Route::get('/superadmin-announcement', Announcement::class)->name('superadmin.announcement');
        Route::get('/superadmin-information', Information::class)->name('superadmin.information');
        Route::get('/superadmin-user', UserManagement::class)->name('superadmin.user');
        Route::get('/superadmin-department', DepartmentPage::class)->name('superadmin.department');
        Route::get('/superadmin-bookingroom', SuperadminBookingroom::class)->name('superadmin.bookingroom');
        Route::get('/superadmin-ticketsupport', SuperadminTicketsupport::class)->name('superadmin.ticketsupport');
        Route::get('/superadmin-manageroom', Manageroom::class)->name('superadmin.manageroom');
        Route::get('/superadmin-managerequirements', Managerequirements::class)->name('superadmin.managerequirements');
    });

    // ---------- Receptionist routes ----------
    Route::middleware('is.receptionist')->group(function () {
        Route::get('/receptionist-dashboard', ReceptionistDashboard::class)->name('receptionist.dashboard');
        Route::get('/receptionist-guestbook', Guestbook::class)->name('receptionist.guestbook');
        Route::get('/receptionist-meetingschedule', MeetingSchedule::class)->name('receptionist.schedule');
        Route::get('/receptionist-document', Documents::class)->name('receptionist.documents');
        Route::get('/receptionist-calendar', CalendarPage::class)->name('receptionist.calendar');
    });

    // ---------- Logout (BERSIHKAN intended + invalidate session) ----------
    Route::post('/logout', function (Request $request) {
        Auth::logout();

        // buang URL yang tersimpan agar tidak redirect ke rute lama
        $request->session()->forget('url.intended');

        // invalidasi session & CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Fallback 404
|--------------------------------------------------------------------------
*/
Route::fallback(Error404::class);
