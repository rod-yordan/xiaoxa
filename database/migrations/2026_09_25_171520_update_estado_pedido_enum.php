<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Convertir estados viejos a los nuevos
        DB::table('pedido')->where('estado_pedido', 'Pagado')->update(['estado_pedido' => 'Pendiente']);
        DB::table('pedido')->where('estado_pedido', 'Confirmado')->update(['estado_pedido' => 'Pendiente']);
        DB::table('pedido')->where('estado_pedido', 'Enviado')->update(['estado_pedido' => 'En camino']);
        DB::table('pedido')->where('estado_pedido', 'Anulado')->update(['estado_pedido' => 'Pendiente']);

        // 2. Modificar el ENUM (sin 'Anulado')
        DB::statement("
            ALTER TABLE pedido 
            MODIFY estado_pedido ENUM('Pendiente','En camino','Listo para recoger','Entregado') 
            DEFAULT 'Pendiente'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE pedido 
            MODIFY estado_pedido ENUM('Pendiente','Pagado','Confirmado','En camino','Listo para recoger','Entregado') 
            DEFAULT 'Pendiente'
        ");
    }
};