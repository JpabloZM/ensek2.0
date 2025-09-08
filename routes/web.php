<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SkillController;
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
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Administrador'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Servicios
        Route::get('/services/trashed', [ServiceController::class, 'trashed'])->name('admin.services.trashed');
        Route::post('/services/{service}/restore', [ServiceController::class, 'restore'])->name('admin.services.restore');
        Route::delete('/services/{service}/force-delete', [ServiceController::class, 'forceDelete'])->name('admin.services.force-delete');
        Route::resource('services', ServiceController::class)->names('admin.services');
        
        // Técnicos
        Route::resource('technicians', TechnicianController::class)->names('admin.technicians');
        Route::post('/technicians/quick-add', [TechnicianController::class, 'quickAdd'])->name('admin.technicians.quick-add');
        
        // Gestión de disponibilidad
        Route::get('/technicians/{id}/availability', [TechnicianController::class, 'manageAvailability'])->name('admin.technicians.availability');
        Route::post('/technicians/{id}/availability', [TechnicianController::class, 'storeAvailability'])->name('admin.technicians.store-availability');
        
        // Gestión de tiempo libre
        Route::get('/technicians/{id}/time-off', [TechnicianController::class, 'manageTimeOff'])->name('admin.technicians.time-off');
        Route::post('/technicians/{id}/time-off', [TechnicianController::class, 'storeTimeOff'])->name('admin.technicians.store-time-off');
        Route::patch('/technicians/{id}/time-off/{timeOffId}', [TechnicianController::class, 'updateTimeOffStatus'])->name('admin.technicians.update-time-off');
        
        // Gestión de habilidades
        Route::get('/technicians/{id}/skills', [TechnicianController::class, 'manageSkills'])->name('admin.technicians.skills');
        Route::post('/technicians/{id}/skills', [TechnicianController::class, 'storeSkills'])->name('admin.technicians.store-skills');
        
        // Actualización de imagen de perfil
        Route::post('/technicians/{id}/profile-image', [TechnicianController::class, 'updateProfileImage'])->name('admin.technicians.update-profile-image');
        
        // Habilidades
        Route::resource('skills', SkillController::class)->names('admin.skills');
        Route::get('/api/skills', [SkillController::class, 'apiList'])->name('api.skills');
        
        // Categorías de inventario
        Route::get('/inventory-categories/trashed', [InventoryCategoryController::class, 'trashed'])->name('admin.inventory-categories.trashed');
        Route::post('/inventory-categories/{category}/restore', [InventoryCategoryController::class, 'restore'])->name('admin.inventory-categories.restore');
        Route::delete('/inventory-categories/{category}/force-delete', [InventoryCategoryController::class, 'forceDelete'])->name('admin.inventory-categories.force-delete');
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
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Técnico'])->group(function () {
        // Dashboard para técnicos
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('technician.dashboard');
        
        // Agendamientos asignados
        Route::get('/schedules', [ScheduleController::class, 'assigned'])->name('technician.schedules');
        
        // Actualizar estado de agendamiento
        Route::patch('/schedules/{schedule}/update-status', [ScheduleController::class, 'updateStatus'])->name('technician.schedules.update-status');
        
        // Gestión de disponibilidad del técnico
        Route::get('/availability', [TechnicianController::class, 'myAvailability'])->name('technician.availability');
        Route::post('/availability', [TechnicianController::class, 'updateMyAvailability'])->name('technician.update-availability');
        
        // Gestión de tiempo libre del técnico
        Route::get('/time-off', [TechnicianController::class, 'myTimeOff'])->name('technician.time-off');
        Route::post('/time-off', [TechnicianController::class, 'requestTimeOff'])->name('technician.request-time-off');
        
        // Gestión de habilidades del técnico
        Route::get('/skills', [TechnicianController::class, 'mySkills'])->name('technician.skills');
        Route::post('/skills', [TechnicianController::class, 'updateMySkills'])->name('technician.update-skills');
        
        // Perfil del técnico
        Route::get('/profile', [TechnicianController::class, 'myProfile'])->name('technician.profile');
        Route::post('/profile', [TechnicianController::class, 'updateMyProfile'])->name('technician.update-profile');
        Route::post('/profile/image', [TechnicianController::class, 'updateMyProfileImage'])->name('technician.update-profile-image');
    });
});

// Ruta después de login
Route::get('/home', function () {
    if (Auth::user()->role->name === 'Administrador') {
        return redirect()->route('admin.dashboard');
    } elseif (Auth::user()->role->name === 'Técnico') {
        return redirect()->route('technician.dashboard');
    } else {
        // Por defecto para clientes
        return redirect()->route('client.dashboard');
    }
})->name('home');
