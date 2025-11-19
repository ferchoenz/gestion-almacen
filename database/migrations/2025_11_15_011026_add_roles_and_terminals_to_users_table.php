<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Usamos Schema::table() para MODIFICAR una tabla existente
    Schema::table('users', function (Blueprint $table) {

        // Columna para el ID del Rol
        // 'after' es solo para ordenarlo bonito en la base de datos
        $table->unsignedBigInteger('role_id')->nullable()->after('id');

        // Columna para el ID de la Terminal
        // 'nullable' permite que sea nulo (para el Administrador que ve todo)
        $table->unsignedBigInteger('terminal_id')->nullable()->after('role_id');

        // --- AHORA LAS CONEXIONES (LLAVES FORÁNEAS) ---

        // Conecta la columna 'role_id' con la tabla 'roles'
        // onDelete('set null') significa que si borras un Rol, el usuario no se borra,
        // solo su 'role_id' se pone en NULL. Es seguro.
        $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

        // Conecta la columna 'terminal_id' con la tabla 'terminals'
        $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('set null');
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    // El método 'down' hace lo contrario, en orden reverso
    Schema::table('users', function (Blueprint $table) {
        // 1. Borra las conexiones (llaves foráneas)
        $table->dropForeign(['role_id']);
        $table->dropForeign(['terminal_id']);

        // 2. Borra las columnas
        $table->dropColumn('role_id');
        $table->dropColumn('terminal_id');
    });
}
};
