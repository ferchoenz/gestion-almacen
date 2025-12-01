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
        // 1. Campos para Hazmat (EPP)
        Schema::table('hazmat_products', function (Blueprint $table) {
            $table->text('epp')->nullable()->after('pictograms'); // Equipo de Protección Personal
        });

        // 2. Campos para Recepciones (Archivos)
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->string('invoice_path')->nullable()->after('sap_confirmation'); // Factura PDF
            $table->string('remission_path')->nullable()->after('invoice_path');   // Remisión PDF
            $table->string('certificate_path')->nullable()->after('remission_path'); // Certificado Calidad PDF
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hazmat_products', function (Blueprint $table) {
            $table->dropColumn('epp');
        });
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->dropColumn(['invoice_path', 'remission_path', 'certificate_path']);
        });
    }
};
