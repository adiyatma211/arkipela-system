<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ArkipelaParameterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use App\Enums\UserPermission;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->homeRoute())
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:' . UserPermission::DASHBOARD_VIEW->value)
        ->name('dashboard');

    Route::prefix('reports')->name('reports.')->middleware('permission:' . UserPermission::REPORTS_VIEW->value)->group(function (): void {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [ReportController::class, 'orders'])->name('orders');
        Route::get('/clients', [ReportController::class, 'clients'])->name('clients');
        Route::get('/products', [ReportController::class, 'products'])->name('products');
    });

    Route::controller(RolePermissionController::class)->prefix('settings/roles')->name('settings.roles.')->group(function (): void {
        Route::get('/', 'index')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('index');
        Route::get('/{role}/edit', 'edit')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('edit');
        Route::put('/{role}', 'update')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('update');
    });

    Route::controller(ArkipelaParameterController::class)->prefix('settings/parameters')->name('settings.parameters.')->group(function (): void {
        Route::get('/', 'index')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('index');
        Route::get('/create', 'create')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('create');
        Route::post('/', 'store')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('store');
        Route::get('/{parameter}/edit', 'edit')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('edit');
        Route::put('/{parameter}', 'update')
            ->middleware('permission:' . UserPermission::SETTINGS_MANAGE->value)
            ->name('update');
    });

    Route::controller(UserManagementController::class)->prefix('settings/users')->name('settings.users.')->group(function (): void {
        Route::get('/', 'index')
            ->middleware('permission:' . implode(',', [
                UserPermission::USERS_VIEW->value,
                UserPermission::USERS_MANAGE->value,
            ]))
            ->name('index');
        Route::get('/create', 'create')
            ->middleware('permission:' . UserPermission::USERS_MANAGE->value)
            ->name('create');
        Route::post('/', 'store')
            ->middleware('permission:' . UserPermission::USERS_MANAGE->value)
            ->name('store');
        Route::get('/{user}/edit', 'edit')
            ->middleware('permission:' . UserPermission::USERS_MANAGE->value)
            ->name('edit');
        Route::patch('/{user}/status', 'updateStatus')
            ->middleware('permission:' . UserPermission::USERS_MANAGE->value)
            ->name('status');
        Route::put('/{user}', 'update')
            ->middleware('permission:' . UserPermission::USERS_MANAGE->value)
            ->name('update');
        Route::delete('/{user}', 'destroy')
            ->middleware('permission:' . UserPermission::USERS_MANAGE->value)
            ->name('destroy');
    });

    Route::controller(SupplierController::class)->prefix('suppliers')->name('suppliers.')->group(function (): void {
        Route::get('/', 'index')
            ->middleware('permission:' . implode(',', [
                UserPermission::SUPPLIERS_VIEW->value,
                UserPermission::SUPPLIERS_MANAGE->value,
            ]))
            ->name('index');
        Route::get('/create', 'create')
            ->middleware('permission:' . UserPermission::SUPPLIERS_MANAGE->value)
            ->name('create');
        Route::post('/', 'store')
            ->middleware('permission:' . UserPermission::SUPPLIERS_MANAGE->value)
            ->name('store');
        Route::get('/{supplier}', 'show')
            ->middleware('permission:' . implode(',', [
                UserPermission::SUPPLIERS_VIEW->value,
                UserPermission::SUPPLIERS_MANAGE->value,
            ]))
            ->name('show');
        Route::get('/{supplier}/edit', 'edit')
            ->middleware('permission:' . UserPermission::SUPPLIERS_MANAGE->value)
            ->name('edit');
        Route::put('/{supplier}', 'update')
            ->middleware('permission:' . UserPermission::SUPPLIERS_MANAGE->value)
            ->name('update');
        Route::patch('/{supplier}/submit', 'submitForApproval')
            ->middleware('permission:' . UserPermission::SUPPLIERS_MANAGE->value)
            ->name('submit');
        Route::patch('/{supplier}/approve', 'approve')
            ->middleware('auth')
            ->name('approve');
        Route::patch('/{supplier}/reject', 'reject')
            ->middleware('auth')
            ->name('reject');
        Route::delete('/{supplier}', 'destroy')
            ->middleware('permission:' . UserPermission::SUPPLIERS_MANAGE->value)
            ->name('destroy');
    });

    Route::controller(ClientController::class)->prefix('clients')->name('clients.')->group(function (): void {
        Route::get('/', 'index')
            ->middleware('permission:' . implode(',', [
                UserPermission::CLIENTS_VIEW->value,
                UserPermission::CLIENTS_MANAGE->value,
            ]))
            ->name('index');
        Route::get('/create', 'create')
            ->middleware('permission:' . UserPermission::CLIENTS_MANAGE->value)
            ->name('create');
        Route::post('/', 'store')
            ->middleware('permission:' . UserPermission::CLIENTS_MANAGE->value)
            ->name('store');
        Route::get('/{client}', 'show')
            ->middleware('permission:' . implode(',', [
                UserPermission::CLIENTS_VIEW->value,
                UserPermission::CLIENTS_MANAGE->value,
            ]))
            ->name('show');
        Route::get('/{client}/edit', 'edit')
            ->middleware('permission:' . UserPermission::CLIENTS_MANAGE->value)
            ->name('edit');
        Route::put('/{client}', 'update')
            ->middleware('permission:' . UserPermission::CLIENTS_MANAGE->value)
            ->name('update');
        Route::delete('/{client}', 'destroy')
            ->middleware('permission:' . UserPermission::CLIENTS_MANAGE->value)
            ->name('destroy');
    });

    Route::controller(OrderController::class)->prefix('orders')->name('orders.')->group(function (): void {
        Route::get('/', 'index')
            ->middleware('permission:' . implode(',', [
                UserPermission::ORDERS_VIEW->value,
                UserPermission::ORDERS_MANAGE->value,
            ]))
            ->name('index');
        Route::get('/create', 'create')
            ->middleware('permission:' . UserPermission::ORDERS_MANAGE->value)
            ->name('create');
        Route::post('/', 'store')
            ->middleware('permission:' . UserPermission::ORDERS_MANAGE->value)
            ->name('store');
        Route::get('/{order}', 'show')
            ->middleware('permission:' . implode(',', [
                UserPermission::ORDERS_VIEW->value,
                UserPermission::ORDERS_MANAGE->value,
            ]))
            ->name('show');
        Route::get('/{order}/edit', 'edit')
            ->middleware('permission:' . UserPermission::ORDERS_MANAGE->value)
            ->name('edit');
        Route::patch('/{order}/documents/{document}/generate', 'generateDocument')
            ->middleware('permission:' . UserPermission::ORDERS_MANAGE->value)
            ->name('documents.generate');
        Route::get('/{order}/documents/{document}/preview', 'previewDocument')
            ->middleware('permission:' . implode(',', [
                UserPermission::ORDERS_VIEW->value,
                UserPermission::ORDERS_MANAGE->value,
            ]))
            ->name('documents.preview');
        Route::put('/{order}', 'update')
            ->middleware('permission:' . UserPermission::ORDERS_MANAGE->value)
            ->name('update');
        Route::delete('/{order}', 'destroy')
            ->middleware('permission:' . UserPermission::ORDERS_MANAGE->value)
            ->name('destroy');
    });
});
