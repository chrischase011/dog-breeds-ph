<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;



Volt::route('/login', 'login')->middleware('guest')->name('login');
Volt::route('/register', 'register')->middleware('guest')->name('register');

Route::middleware(['auth'])->group(function () {
  Volt::route('/', 'home')->name('home');
  Volt::route('/select-breeds', 'select-breeds')->name('select.breeds');
  Volt::route('/breeds', 'breeds')->name('breeds.list');
  Volt::route('/profile', 'profile')->name('profile');

  Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
  })->name('logout');
});