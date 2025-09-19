<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\User\Home;
use App\Livewire\Pages\User\CreateTicket;

Route::get('/', Home::class)->name('home');
Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
