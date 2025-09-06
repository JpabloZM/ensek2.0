<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Ruta principal redirige a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Auth::routes();

// Rutas para administradores
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Servicios
        Route::resource('services', ServiceController::class)->names('admin.services');
        
        // Técnicos
        Route::resource('technicians', TechnicianController::class)->names('admin.technicians');
        Route::post('/technicians/quick-add', [TechnicianController::class, 'quickAdd'])->name('admin.technicians.quick-add');
        
        // Categorías de inventario
        Route::resource('inventory-categories', InventoryCategoryController::class)->names('admin.inventory-categories');
        
        // Ítems de inventario
        Route::resource('inventory-items', InventoryItemController::class)->names('admin.inventory-items');
        Route::get('inventory-items/export/{format?}', [InventoryItemController::class, 'export'])->name('admin.inventory-items.export');
        Route::get('inventory-items/{id}/print-barcode', [InventoryItemController::class, 'printBarcode'])->name('admin.inventory-items.print-barcode');
        Route::patch('inventory-items/{id}/add-stock', [InventoryItemController::class, 'addStock'])->name('admin.inventory-items.add-stock');
        Route::patch('inventory-items/{id}/remove-stock', [InventoryItemController::class, 'removeStock'])->name('admin.inventory-items.remove-stock');
        
        // Solicitudes de servicio
        Route::resource('service-requests', ServiceRequestController::class)->names('admin.service-requests');
        Route::get('/service-requests/filter', [ServiceRequestController::class, 'filter'])->name('admin.service-requests.filter');
        
        // Agendamientos
        Route::resource('schedules', ScheduleController::class)->names('admin.schedules');
        Route::patch('/schedules/{schedule}/update-duration', [ScheduleController::class, 'updateDuration'])->name('admin.schedules.updateDuration');
    });
});

// Rutas para técnicos
Route::middleware(['auth'])->prefix('technician')->group(function () {
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':technician'])->group(function () {
        // Dashboard para técnicos
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('technician.dashboard');
        
        // Agendamientos asignados
        Route::get('/schedules', [ScheduleController::class, 'assigned'])->name('technician.schedules');
        
        // Actualizar estado de agendamiento
        Route::patch('/schedules/{schedule}/update-status', [ScheduleController::class, 'updateStatus'])->name('technician.schedules.update-status');
    });
});

// Ruta después de login
Route::get('/home', function () {
    if (Auth::user()->role->name === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('technician.dashboard');
    }
})->name('home');
