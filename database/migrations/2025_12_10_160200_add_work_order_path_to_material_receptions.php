<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->string('work_order_path')->nullable()->after('certificate_path');
        });
    }

    public function down(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->dropColumn('work_order_path');
        });
    }
};
