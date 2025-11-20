<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Almacen\MaterialOutputController;
use App\Http\Controllers\Almacen\MaterialReceptionController; // <-- Controlador de Recepciones
use App\Http\Controllers\Almacen\HazmatProductController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
     return view('welcome');
});

Route::middleware('auth')->group(function () {

     // --- DASHBOARD ---
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // --- PERFIL ---
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // --- CRUD DE USUARIOS (Solo Admin) ---
     Route::resource('users', UserController::class)
          ->middleware('role:Administrador');

     // ============================================================================
     // MÓDULO DE SALIDAS DE MATERIALES
     // Roles: Administrador, Gerencia, Mantenimiento
     // ============================================================================

     // 1. Exportar Excel
     Route::get('salidas/exportar', [MaterialOutputController::class, 'export'])
          ->name('material-outputs.export')
          ->middleware('role:Administrador,Gerencia,Mantenimiento');

     // 2. Generar PDF
     Route::get('salidas/{salida}/pdf', [MaterialOutputController::class, 'downloadPDF'])
          ->name('material-outputs.pdf')
          ->middleware('role:Administrador,Gerencia,Mantenimiento');

     // 3. Ver Lista (Index)
     Route::get('salidas', [MaterialOutputController::class, 'index'])
          ->name('material-outputs.index')
          ->middleware('role:Administrador,Gerencia,Mantenimiento');

     // 4. CRUD (Crear, Editar, Borrar) - Solo Admin
     Route::resource('salidas', MaterialOutputController::class)
          ->names('material-outputs')
          ->except(['index']) // Excluimos index porque ya lo definimos arriba
          ->middleware('role:Administrador');


     // ============================================================================
     // MÓDULO DE RECEPCIÓN DE MATERIALES
     // Roles: Administrador, Gerencia (Mantenimiento NO tiene acceso aquí)
     // ============================================================================

     // 1. Exportar Excel
     Route::get('recepciones/exportar', [MaterialReceptionController::class, 'export'])
          ->name('material-receptions.export')
          ->middleware('role:Administrador,Gerencia');

     // 2. Generar PDF
     Route::get('recepciones/{recepcione}/pdf', [MaterialReceptionController::class, 'downloadPDF'])
          ->name('material-receptions.pdf')
          ->middleware('role:Administrador,Gerencia');

     // 3. Ver Lista (Index)
     Route::get('recepciones', [MaterialReceptionController::class, 'index'])
          ->name('material-receptions.index')
          ->middleware('role:Administrador,Gerencia');

     // 4. CRUD (Crear, Editar, Borrar) - Solo Admin
     Route::resource('recepciones', MaterialReceptionController::class)
          ->names('material-receptions')
          ->except(['index'])
          ->middleware('role:Administrador');


     // ============================================================================
     // HAZMAT
     // ============================================================================

     Route::post('hazmat/analyze', [HazmatProductController::class, 'analyze'])
          ->name('hazmat.analyze')
          ->middleware('role:Administrador,Seguridad y Salud');

     // Ruta para descargar etiqueta
     // OJO: En el controlador la variable es $hazmat, pero Laravel usa el singular del resource ($hazmat)
     // Si usas Route::resource('hazmat', ...), el parámetro es {hazmat}
     Route::get('hazmat/{hazmat}/label', [HazmatProductController::class, 'downloadLabel'])
          ->name('hazmat.label')
          ->middleware('role:Administrador,Seguridad y Salud');

     // NUEVA RUTA SEGURA PARA VER EL PDF (Arregla el 403)
     Route::get('hazmat/{hazmat}/view-hds', [HazmatProductController::class, 'viewHds'])
          ->name('hazmat.view-hds')
          ->middleware('role:Administrador,Seguridad y Salud');

     Route::get('hazmat/exportar', [HazmatProductController::class, 'export'])
          ->name('hazmat.export')
          ->middleware('role:Administrador,Seguridad y Salud');

     Route::resource('hazmat', HazmatProductController::class)
          ->middleware('role:Administrador,Seguridad y Salud');
});

require __DIR__ . '/auth.php';

// ============================================================================
// RUTA DE EMERGENCIA PARA CORRER MIGRACIONES
// (USAR SOLO UNA VEZ Y LUEGO BORRAR)
// ============================================================================
Route::get('/reparar-base-de-datos', function () {
    try {
        // Forzamos la migración de la tabla de sesiones y otras faltantes
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true, // Necesario en producción
        ]);
        
        return "¡ÉXITO! Las tablas se crearon correctamente. Output: " . \Illuminate\Support\Facades\Artisan::output();

    } catch (\Exception $e) {
        return "ERROR AL MIGRAR: " . $e->getMessage();
    }
});
