<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// Livewire Pages
use App\Livewire\Pages\User\Home as UserHome;
use App\Livewire\Pages\User\CreateTicket;
use App\Livewire\Pages\User\Bookroom;
use App\Livewire\Pages\User\Profile;
use App\Livewire\Pages\User\Package;
use App\Livewire\Pages\User\Ticketstatus;
use App\Livewire\Pages\Admin\Dashboard;
use App\Livewire\Pages\Auth\Login as LoginPage;
use App\Livewire\Pages\Auth\Register as RegisterPage;
use App\Livewire\Pages\Errors\error404 as Error404; // pastikan nama class & file-nya EXACT sama (case-sensitive)

// -----------------------------
// GUEST ONLY (belum login)
// -----------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
});

// -----------------------------
// AUTH ONLY (sudah login)
// -----------------------------
Route::middleware('auth')->group(function () {
    // Home sebagai dashboard (root). Jika belum login, otomatis di-redirect ke route 'login' oleh middleware.
    Route::get('/', UserHome::class)->name('home');
    // Alias opsional: /home -> /
    Route::redirect('/home', '/')->name('home.alias');

    // Halaman user lain
    Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
    Route::get('/book-room',     Bookroom::class)->name('book-room');
    Route::get('/profile',       Profile::class)->name('profile');
    Route::get('/package',       Package::class)->name('package');
    Route::get('/ticketstatus',  Ticketstatus::class)->name('ticketstatus');

    // Logout (POST) – aman dengan CSRF
    Route::post('/logout', function (Request $request) {
        Auth::logout();                         // keluar & hapus remember cookie
        $request->session()->invalidate();      // invalidate session
        $request->session()->regenerateToken(); // regen CSRF
        return redirect()->route('login');
    })->name('logout');
});

// -----------------------------
// Fallback 404
// -----------------------------
Route::fallback(Error404::class);

