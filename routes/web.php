<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\QrSessionController;
use App\Http\Controllers\Admin\RapportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrationVerificationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Employe\CheckInController;
use App\Http\Controllers\Employe\EmployeController;
use App\Http\Controllers\Routing\RoutingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::post('/register/admin/send-verification-code', [RegistrationVerificationController::class, 'sendAdminCode'])
        ->middleware('throttle:email-verification')
        ->name('register.admin.verification.send');

    Route::post('/admin', [AdminController::class, 'register'])
        ->middleware('throttle:mutation-heavy')
        ->name('admin.register');
        
    Route::delete('/admin/{user}', [AdminController::class, 'delete'])
        ->middleware('throttle:mutation-heavy')
        ->name('admin.delete');

    Route::get('/super-admin/dashboard', [RoutingController::class, 'superAdminDashboard'])
        ->middleware('throttle:views-default')
        ->name('super-admin.dashboard');
        
    Route::get('/admin/register', [RoutingController::class, 'adminRegister'])
        ->middleware('throttle:views-default')
        ->name('admin.register.form');
        
    Route::get('/admin', [RoutingController::class, 'listAdmin'])
        ->middleware('throttle:views-default')
        ->name('admin.list');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/register/employe/send-verification-code', [RegistrationVerificationController::class, 'sendEmployeCode'])
        ->middleware('throttle:email-verification')
        ->name('register.employe.verification.send');

    Route::get('/admin/dashboard', [RoutingController::class, 'adminDashboard'])
        ->middleware('throttle:views-default')
        ->name('admin.dashboard');
        
    Route::get('/employe/register', [RoutingController::class, 'employeRegister'])
        ->middleware('throttle:views-default')
        ->name('employe.register.form');
        
    Route::get('/employe', [RoutingController::class, 'listEmploye'])
        ->middleware('throttle:views-default')
        ->name('employe.list');
        
    Route::get('/employe/{user}', [RoutingController::class, 'showEmploye'])
        ->middleware('throttle:views-default')
        ->name('employe.show');
        
    Route::get('/employe/{user}/edit', [RoutingController::class, 'editEmploye'])
        ->middleware('throttle:views-default')
        ->name('employe.update.form');

    Route::put('/employe/{user}', [EmployeController::class, 'update'])
        ->middleware('throttle:mutation-heavy')
        ->name('admin.employe.update');
        
    Route::delete('/account', [AdminController::class, 'deleteOwnAccount'])
        ->middleware('throttle:mutation-heavy')
        ->name('admin.account.delete');
        
    Route::post('/employe', [EmployeController::class, 'register'])
        ->middleware('throttle:mutation-heavy')
        ->name('admin.employe.register');
        
    Route::delete('/employe/{user}', [EmployeController::class, 'delete'])
        ->middleware('throttle:mutation-heavy')
        ->name('admin.employe.delete');

    Route::get('/admin/qr-dashboard', [QrSessionController::class, 'show'])
        ->middleware('throttle:views-default')
        ->name('admin.qr.show');
        
    Route::post('/admin/qr-refresh', [QrSessionController::class, 'refresh'])
        ->middleware('throttle:qr-actions')
        ->name('admin.qr.refresh');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->middleware('throttle:60,1')
        ->name('admin.notifications.read');
        
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->middleware('throttle:20,1')
        ->name('admin.notifications.read-all');

    Route::get('/employe/{user}/rapport', [RapportController::class, 'exportEmploye'])
        ->middleware('throttle:5,1')
        ->name('admin.employe.rapport');

        
});

Route::middleware(['auth', 'role:employe'])->group(function () {
    Route::get('/mon-espace/dashboard', [RoutingController::class, 'employeDashboard'])
        ->middleware('throttle:views-default')
        ->name('employe.dashboard');
        
    Route::get('/mon-espace/scan', [RoutingController::class, 'employeScanForm'])
        ->middleware('throttle:views-default')
        ->name('employe.scan.form');

    Route::post('/mon-espace/check-in', [CheckInController::class, 'store'])
        ->middleware('throttle:qr-actions')
        ->name('employe.checkin');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [RoutingController::class, 'login'])
        ->middleware('throttle:views-default')
        ->name('login');
        
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:auth-strict')
        ->name('user.login');
});

Route::get('/', [RoutingController::class, 'home'])
    ->name('home');
    
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:public-contact')
    ->name('contact.send');