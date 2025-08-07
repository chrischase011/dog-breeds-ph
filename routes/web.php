<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;



Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'register')->name('register');

Route::middleware(['auth'])->group(function () {
  Volt::route('/', 'home')->name('home');
  Volt::route('/select-breeds', 'select-breeds')->name('select.breeds');
  Volt::route('/breeds', 'breeds')->name('breeds.list');

  Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
  })->name('logout');
});