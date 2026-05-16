<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('companies.index')
        : redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/companies/{company}/logo', [CompanyController::class, 'logo'])->name('companies.logo');
    Route::resource('companies', CompanyController::class);
    Route::resource('employees', EmployeeController::class);
});
