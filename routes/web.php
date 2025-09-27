<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// ========== Livewire Pages (User) ==========
use App\Livewire\Pages\User\Home as UserHome;
use App\Livewire\Pages\User\CreateTicket;
use App\Livewire\Pages\User\Bookroom;          // <- komponen Bookroom (User)
use App\Livewire\Pages\User\Profile;
use App\Livewire\Pages\User\Package;
use App\Livewire\Pages\User\Ticketstatus;
use App\Livewire\Pages\User\BookingStatus;     // <- konsisten PascalCase

// ========== Livewire Pages (Admin / Superadmin / Receptionist) ==========
use App\Livewire\Pages\Admin\Dashboard as AdminDashboard;
use App\Livewire\Pages\Superadmin\Dashboard as SuperadminDashboard;
use App\Livewire\Pages\Superadmin\Announcement;
use App\Livewire\Pages\Superadmin\Information;
use App\Livewire\Pages\Superadmin\Account as UserManagement;
use App\Livewire\Pages\Receptionist\Dashboard as ReceptionistDashboard;
use App\Livewire\Pages\Receptionist\Guestbook as ReceptionistGuestbook;
use App\Livewire\Pages\Receptionist\Documents as Documents;

// ========== Auth Pages ==========
use App\Livewire\Pages\Auth\Login as LoginPage;
use App\Livewire\Pages\Auth\Register as RegisterPage;

// ========== Error ==========
use App\Livewire\Pages\Errors\error404 as Error404;

//receptionist
use App\Livewire\Pages\Receptionist\Guestbook as Guestbook;
use App\Livewire\Pages\Receptionist\MeetingSchedule as MeetingSchedule;


/*
|--------------------------------------------------------------------------
| Root: arahkan sesuai status login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Jika relasi role ada: $user->role->name
    // Jika tidak ada relasi, fallback ke kolom string $user->role (kalau ada)
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
    Route::get('/login',    LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
});

/*
|--------------------------------------------------------------------------
| Auth only
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ---------- User routes ----------
    Route::get('/dashboard',     UserHome::class)->name('user.home');
    Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');

    // Booking room (User)
    Route::get('/book-room',     Bookroom::class)->name('book-room');      // form + calendar (komponen User\Bookroom)
    Route::get('/bookingstatus', BookingStatus::class)->name('bookingstatus');

    // Profile & others
    Route::get('/profile',       Profile::class)->name('profile');
    Route::get('/package',       Package::class)->name('package');
    Route::get('/ticketstatus',  Ticketstatus::class)->name('ticketstatus');

    // ---------- Admin routes ----------
    Route::middleware('is.admin')->group(function () {
        Route::get('/admin-dashboard', AdminDashboard::class)->name('admin.dashboard');
    });

    // ---------- Superadmin routes ----------
    Route::middleware('is.superadmin')->group(function () {
        Route::get('/superadmin-dashboard',   SuperadminDashboard::class)->name('superadmin.dashboard');
        Route::get('/superadmin-announcement', Announcement::class)->name('superadmin.announcement');
        Route::get('/superadmin-information',  Information::class)->name('superadmin.information');
        Route::get('/superadmin-user',         UserManagement::class)->name('superadmin.user');
    });

    // ---------- Receptionist routes ----------
    Route::middleware('is.receptionist')->group(function () {
        Route::get('/receptionist-dashboard', ReceptionistDashboard::class)->name('receptionist.dashboard');
        Route::get('/receptionist-guestbook', Guestbook::class)->name('receptionist.guestbook');
        Route::get('/receptionist-meetingschedule', MeetingSchedule::class)->name('receptionist.schedule');
        Route::get('/receptionist-document', Documents::class)->name('receptionist.documents');
    });
    Route::middleware(['auth'])->group(function () {
    Route::get('/receptionist/documents', Documents::class)
        ->name('receptionist.documents');
});
        
    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
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
