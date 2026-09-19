<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosBaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tipos de entrega
        if (DB::table('tipo_entrega')->count() === 0) {
            DB::table('tipo_entrega')->insert([
                ['id_tipo_entrega' => 1, 'nombre_tipo_entrega' => 'Recojo en tienda',    'estado' => 1],
                ['id_tipo_entrega' => 2, 'nombre_tipo_entrega' => 'Envío por provincia', 'estado' => 1],
            ]);
            $this->command->info('✅ Tipos de entrega insertados.');
        }

        // ── Roles
        if (DB::table('rol')->count() === 0) {
            DB::table('rol')->insert([
                ['id_rol' => 1, 'nombre_rol' => 'Administrador'],
                ['id_rol' => 2, 'nombre_rol' => 'Usuario'],
            ]);
            $this->command->info('✅ Roles insertados.');
        }

        // ── Tipos de documento
        if (DB::table('tipo_documento')->count() === 0) {
            DB::table('tipo_documento')->insert([
                ['nombre_tipo_documento' => 'DNI'],
                ['nombre_tipo_documento' => 'RUC'],
                ['nombre_tipo_documento' => 'Carné de Extranjería'],
            ]);
            $this->command->info('✅ Tipos de documento insertados.');
        }

        // ── Departamentos del Perú
        if (DB::table('departamento')->count() === 0) {
            $departamentos = [
                'Amazonas', 'Áncash', 'Apurímac', 'Arequipa', 'Ayacucho',
                'Cajamarca', 'Callao', 'Cusco', 'Huancavelica', 'Huánuco',
                'Ica', 'Junín', 'La Libertad', 'Lambayeque', 'Lima',
                'Loreto', 'Madre de Dios', 'Moquegua', 'Pasco', 'Piura',
                'Puno', 'San Martín', 'Tacna', 'Tumbes', 'Ucayali',
            ];

            $rows = array_map(
                fn($nombre) => ['nombre_departamento' => $nombre],
                $departamentos
            );

            DB::table('departamento')->insert($rows);
            $this->command->info('✅ Departamentos insertados (25).');
        }
    }
}