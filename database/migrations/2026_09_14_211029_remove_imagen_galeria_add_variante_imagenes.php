<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quitar 'imagen' y 'galeria' de producto
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn(['imagen', 'galeria']);
        });

        // 2. Quitar 'imagen' de producto_variante (ya no la usamos aquí)
        Schema::table('producto_variante', function (Blueprint $table) {
            if (Schema::hasColumn('producto_variante', 'imagen')) {
                $table->dropColumn('imagen');
            }
        });

        // 3. Crear tabla nueva para imágenes de variantes
        Schema::create('producto_variante_imagen', function (Blueprint $table) {
            $table->id('id_imagen');
            $table->unsignedBigInteger('id_variante');
            $table->string('imagen', 255);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('id_variante')
                  ->references('id_variante')
                  ->on('producto_variante')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variante_imagen');

        Schema::table('producto', function (Blueprint $table) {
            $table->string('imagen')->nullable();
            $table->text('galeria')->nullable();
        });

        Schema::table('producto_variante', function (Blueprint $table) {
            $table->string('imagen', 255)->nullable();
        });
    }
};