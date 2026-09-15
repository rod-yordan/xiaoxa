<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campo 'detalles' a producto (JSON array de strings)
        Schema::table('producto', function (Blueprint $table) {
            $table->json('detalles')->nullable()->after('descripcion');
        });

        // Agregar campo 'imagen' a producto_variante (opcional)
        Schema::table('producto_variante', function (Blueprint $table) {
            $table->string('imagen', 255)->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('detalles');
        });

        Schema::table('producto_variante', function (Blueprint $table) {
            $table->dropColumn('imagen');
        });
    }
};