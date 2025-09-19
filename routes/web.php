<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\User\Home;
use App\Livewire\Pages\User\CreateTicket;
use App\Livewire\Pages\User\Bookroom;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Auth\Register;

Route::get('/', Home::class)->name('home');
Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
Route::get('/book-room', Bookroom::class)->name('book-room');

Route::get('/', Home::class)->name('home');
Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
