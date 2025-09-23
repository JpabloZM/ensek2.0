<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [App\Http\Controllers\PublicController::class, 'index'])->name('public.landing');
Route::get('/thank-you', [App\Http\Controllers\PublicController::class, 'thankYou'])->name('public.thank-you');

// Ruta protegida para solicitar servicios (solo usuarios autenticados)
Route::middleware(['auth'])->group(function () {
    Route::post('/service-request', [App\Http\Controllers\PublicController::class, 'storeServiceRequest'])->name('public.service-request');
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
        
        // Movimientos de inventario
        Route::resource('inventory-movements', InventoryMovementController::class)->names('admin.inventory-movements');
        Route::get('inventory-movements-report/form', [InventoryMovementController::class, 'reportForm'])->name('admin.inventory-movements.report.form');
        Route::get('inventory-movements-report', [InventoryMovementController::class, 'report'])->name('admin.inventory-movements.report');
        Route::get('inventory-movements-valuation', [InventoryMovementController::class, 'valuation'])->name('admin.inventory-movements.valuation');
        
        // Solicitudes de servicio
        Route::resource('service-requests', ServiceRequestController::class)->names('admin.service-requests');
        Route::get('/service-requests/filter', [ServiceRequestController::class, 'filter'])->name('admin.service-requests.filter');
        
        // Agendamientos
        Route::resource('schedules', ScheduleController::class)->names('admin.schedules');
        Route::patch('/schedules/{schedule}/update-duration', [ScheduleController::class, 'updateDuration'])->name('admin.schedules.updateDuration');
        Route::get('/api/schedules', [ScheduleController::class, 'getCalendarData'])->name('api.schedules');
        Route::get('/schedules/{schedule}/json', [ScheduleController::class, 'getJson'])->name('admin.schedules.json');
        
        // Rutas de citas y calendario
        Route::prefix('appointments')->name('appointments.')->middleware(['auth'])->group(function () {
            Route::get('/', [AppointmentController::class, 'index'])->name('index');
            Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
            Route::get('/calendar-data', [AppointmentController::class, 'getCalendarData'])->name('calendar-data');
            Route::post('/store', [AppointmentController::class, 'store'])->name('store');
            Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
            Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
            Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy');
            
            // Nuevas rutas para las funcionalidades de agendamiento
            Route::post('/suggest-technicians', [AppointmentController::class, 'suggestTechnicians'])->name('suggest-technicians');
            Route::post('/check-conflicts', [AppointmentController::class, 'checkConflicts'])->name('check-conflicts');
            Route::post('/send-reminders', [AppointmentController::class, 'sendReminders'])->name('send-reminders');
            Route::get('/technicians', [AppointmentController::class, 'getTechnicians'])->name('technicians');
        });
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
        // Por defecto para clientes (redireccionar a la landing page)
        return redirect()->route('public.landing');
    }
})->name('home');

// Ruta pública para confirmar citas (accesible sin autenticación)
Route::get('/appointments/confirm/{id}/{token}', [AppointmentController::class, 'confirmAppointment'])->name('appointments.confirm');
