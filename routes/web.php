<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\User\Home;
use App\Livewire\Pages\User\CreateTicket;
use App\Livewire\Pages\User\Bookroom;

Route::get('/', Home::class)->name('home');
Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
Route::get('/book-room', Bookroom::class)->name('book-room');
