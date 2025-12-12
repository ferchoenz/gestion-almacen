<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Almacen\MaterialOutputController;
use App\Http\Controllers\Almacen\MaterialReceptionController;
use App\Http\Controllers\Almacen\HazmatProductController;
use Illuminate\Support\Facades\Artisan; // Para comandos de emergencia si los usaras

// 1. REDIRECCIÓN AL LOGIN (Adiós página de bienvenida)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    
    // --- DASHBOARD ---
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // --- PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- USUARIOS ---
    Route::resource('users', UserController::class)->middleware('role:Administrador');
    
    // ============================================================================
    // SALIDAS
    // ============================================================================
    Route::get('salidas/exportar', [MaterialOutputController::class, 'export'])
         ->name('material-outputs.export')
         ->middleware('role:Administrador,Gerencia,Mantenimiento');

    Route::get('salidas/{salida}/pdf', [MaterialOutputController::class, 'downloadPDF'])
         ->name('material-outputs.pdf')
         ->middleware('role:Administrador,Gerencia,Mantenimiento');
    
    Route::get('salidas', [MaterialOutputController::class, 'index'])
         ->name('material-outputs.index')
         ->middleware('role:Administrador,Gerencia,Mantenimiento');

    Route::resource('salidas', MaterialOutputController::class)
         ->names('material-outputs')
         ->except(['index'])
         ->middleware('role:Administrador');

    // ============================================================================
    // RECEPCIONES (ENTRADAS)
    // ============================================================================
    
    // Nueva ruta para ver archivos adjuntos (Factura, Remisión, Certificado)
    Route::get('recepciones/{recepcione}/file/{type}', [MaterialReceptionController::class, 'viewFile'])
         ->name('material-receptions.file')
         ->middleware('role:Administrador,Gerencia');

    Route::get('recepciones/exportar', [MaterialReceptionController::class, 'export'])
         ->name('material-receptions.export')
         ->middleware('role:Administrador,Gerencia');

    Route::get('recepciones/{recepcione}/pdf', [MaterialReceptionController::class, 'downloadPDF'])
         ->name('material-receptions.pdf')
         ->middleware('role:Administrador,Gerencia');

    Route::get('recepciones', [MaterialReceptionController::class, 'index'])
         ->name('material-receptions.index')
         ->middleware('role:Administrador,Gerencia');

    Route::resource('recepciones', MaterialReceptionController::class)
         ->names('material-receptions')
         ->except(['index'])
         ->middleware('role:Administrador');

    // ============================================================================
    // HAZMAT (MATERIALES PELIGROSOS)
    // ============================================================================
    
    // Solicitudes de Autorización (Workflow)
    Route::get('hazmat/requests/{hazmatRequest}/pdf', [\App\Http\Controllers\Almacen\HazmatRequestController::class, 'downloadPdf'])
         ->name('hazmat-requests.pdf');
    Route::get('hazmat/requests/{hazmatRequest}/hds', [\App\Http\Controllers\Almacen\HazmatRequestController::class, 'viewHds'])
         ->name('hazmat-requests.hds');
    Route::resource('hazmat/requests', \App\Http\Controllers\Almacen\HazmatRequestController::class)
         ->names('hazmat-requests')
         ->parameters(['requests' => 'hazmatRequest']);

    Route::post('hazmat/analyze', [HazmatProductController::class, 'analyze'])
         ->name('hazmat.analyze')
         ->middleware('role:Administrador,Seguridad y Salud');

    Route::get('hazmat/exportar', [HazmatProductController::class, 'export'])
         ->name('hazmat.export')
         ->middleware('role:Administrador,Seguridad y Salud');

    Route::get('hazmat/{hazmat}/label', [HazmatProductController::class, 'downloadLabel'])
         ->name('hazmat.label')
         ->middleware('role:Administrador,Seguridad y Salud');

    Route::get('hazmat/{hazmat}/view-hds', [HazmatProductController::class, 'viewHds'])
         ->name('hazmat.view-hds')
         ->middleware('role:Administrador,Seguridad y Salud');

    Route::resource('hazmat', HazmatProductController::class)
         ->middleware('role:Administrador,Seguridad y Salud');

    // ============================================================================
    // CONSUMIBLES (INVENTARIO)
    // ============================================================================
    
    // API endpoint for consumable search (for select2)
    Route::get('consumables/api/search', [\App\Http\Controllers\Almacen\ConsumableController::class, 'search'])
         ->name('consumables.search')
         ->middleware('role:Administrador,Gerencia,Almacenista');

    Route::get('consumables/exportar', [\App\Http\Controllers\Almacen\ConsumableController::class, 'export'])
         ->name('consumables.export')
         ->middleware('role:Administrador,Gerencia');

    // Rutas para etiquetas de consumibles
    Route::get('consumables/{consumable}/label', [\App\Http\Controllers\Almacen\LabelController::class, 'single'])
         ->name('consumables.label')
         ->middleware('role:Administrador,Almacenista');

    Route::get('consumables/{consumable}/labels', [\App\Http\Controllers\Almacen\LabelController::class, 'multiple'])
         ->name('consumables.labels')
         ->middleware('role:Administrador,Almacenista');

    Route::post('consumables/labels/batch', [\App\Http\Controllers\Almacen\LabelController::class, 'batch'])
         ->name('consumables.labels.batch')
         ->middleware('role:Administrador,Almacenista');

    // Rutas de importación (antes de resource)
    Route::get('consumables/importar', [\App\Http\Controllers\Almacen\ConsumableController::class, 'import'])
         ->name('consumables.import')
         ->middleware('role:Administrador,Almacenista');
    
    Route::post('consumables/importar', [\App\Http\Controllers\Almacen\ConsumableController::class, 'processImport'])
         ->name('consumables.import.process')
         ->middleware('role:Administrador,Almacenista');

    Route::resource('consumables', \App\Http\Controllers\Almacen\ConsumableController::class)
         ->middleware('role:Administrador,Almacenista');

    // ============================================================================
    // UBICACIONES DE INVENTARIO
    // ============================================================================
    Route::resource('inventory-locations', \App\Http\Controllers\Almacen\InventoryLocationController::class)
         ->middleware('role:Administrador,Almacenista');
});

require __DIR__.'/auth.php';