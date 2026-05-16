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
    Route::get('/ajax/companies', [EmployeeController::class, 'companyOptions'])->name('companies.options');
    Route::get('/companies/{company}/logo', [CompanyController::class, 'logo'])->name('companies.logo');
    Route::get('/companies/{company}/employees/pdf', [CompanyController::class, 'exportEmployeesPdf'])->name('companies.employees.pdf');
    Route::get('/employees/import', [EmployeeController::class, 'importForm'])->name('employees.import.form');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::resource('companies', CompanyController::class);
    Route::resource('employees', EmployeeController::class);
});
