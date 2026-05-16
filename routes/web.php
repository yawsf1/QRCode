<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Employe\EmployeController;
use App\Http\Controllers\Routing\RoutingController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;





Route::middleware(['auth', 'role:super_admin'])->group(function () {

    Route::post('/admin', [AdminController::class, 'register'])
        ->name('admin.register');
        
    Route::delete('/admin/{user}',[AdminController::class, 'delete'])
        ->name('admin.delete');
            
    Route::get('/super-admin/dashboard', [RoutingController::class, 'SuperAdmindashboard'])->name('super-admin.dashboard');
    Route::get('/admin/register', [RoutingController::class, 'AdminRegister'])->name('admin.register.form');
    Route::get('/admin', [RoutingController::class, 'listAdmin'])->name('admin.list');
});


Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', [RoutingController::class, 'Admindashboard'])->name('admin.dashboard');
    Route::get('/employe/register', [RoutingController::class, 'EmployeRegister'])->name('employe.register.form');
    Route::get('/employe', [RoutingController::class, 'listEmploye'])->name('employe.list');
    Route::get('/employe/{user}', [RoutingController::class, 'showEmploye'])->name('employe.show');
    Route::get('/employe/{user}/edit', [RoutingController::class, 'editEmploye'])->name('employe.update.form');

    
    Route::put('/employe/{user}', [EmployeController::class, 'update'])
        ->name('employe.update');

    Route::delete('/account', [AdminController::class, 'deleteOwnAccount'])
        ->name('admin.account.delete');
    

    Route::post('/employe', [EmployeController::class, 'register'])
        ->name('employe.register');

        
    Route::delete('/employe/{user}', [EmployeController::class, 'delete'])
        ->name('employe.delete');



        


});


Route::middleware(['auth', 'role:employe'])->group(function () {
    
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [RoutingController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('user.login');
});

Route::get('/', [RoutingController::class, 'home'])->name('home');