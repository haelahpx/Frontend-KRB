<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\User\Home;
use App\Livewire\Pages\User\CreateTicket;
use App\Livewire\Pages\User\Bookroom;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Auth\Register;
use App\Livewire\Pages\Errors\error404;
use App\Livewire\Pages\User\Profile;
use App\Livewire\Pages\User\Package;
use App\Livewire\Pages\User\Ticketstatus;
use App\Livewire\Pages\Admin\Dashboard;


Route::get('/', Home::class)->name('home');
Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
Route::get('/book-room', Bookroom::class)->name('book-room');
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::fallback(error404::class);
Route::get('/profile', Profile::class)->name('profile');
Route::get('/package', Package::class)->name('package');
Route::get('/ticketstatus', Ticketstatus::class)->name('ticketstatus');
Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');