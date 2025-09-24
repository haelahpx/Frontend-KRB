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
use App\Livewire\Pages\Admin\Dashboard as AdminDashboard;
use App\Livewire\Pages\Auth\Login as LoginPage;
use App\Livewire\Pages\Auth\Register as RegisterPage;
use App\Livewire\Pages\Errors\error404 as Error404;


Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $user = Auth::user();
        $roleName = $user->role->name ?? $user->role ?? null;

        if ($roleName === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.home'); 
    })->name('home');
    
    Route::get('/dashboard', UserHome::class)->name('user.home');
    Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
    Route::get('/book-room',     Bookroom::class)->name('book-room');
    Route::get('/profile',       Profile::class)->name('profile');
    Route::get('/package',       Package::class)->name('package');
    Route::get('/ticketstatus',  Ticketstatus::class)->name('ticketstatus');
    Route::middleware('is.admin')->group(function () {
    Route::get('/admin-dashboard', AdminDashboard::class)->name('admin.dashboard');
    });
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
    Route::fallback(Error404::class);
