<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\Home;
use App\Livewire\Pages\CreateTicket;

Route::get('/', Home::class)->name('home');
Route::get('/create-ticket', CreateTicket::class)->name('create-ticket');
