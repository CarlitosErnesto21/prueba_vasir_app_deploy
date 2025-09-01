<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios para los clientes
        $usuarios = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@email.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@email.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@email.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@email.com',
                'password' => Hash::make('password123'),
            ],
        ];

        $userIds = [];
        foreach ($usuarios as $userData) {
            $user = User::create($userData);
            $userIds[] = $user->id;
        }

        $clientes = [
            [
                'nombre' => 'Juan',                          // ✅ Máximo 80 chars
                'apellido' => 'Pérez',                      // ✅ Máximo 80 chars
                'numero_identificacion' => '1234567890',    // ✅ Máximo 25 chars
                'fecha_nacimiento' => '1990-05-15',
                'genero' => 'MASCULINO',
                'direccion' => 'Av. Principal 123',         // ✅ Máximo 200 chars
                'telefono' => '555-0101',                   // ✅ Máximo 20 chars
                'user_id' => $userIds[0],
                'tipo_documento_id' => 1,
            ],
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'numero_identificacion' => '1234567891',
                'fecha_nacimiento' => '1985-08-22',
                'genero' => 'FEMENINO',
                'direccion' => 'Calle Segunda 456',
                'telefono' => '555-0102',
                'user_id' => $userIds[1],
                'tipo_documento_id' => 1,
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'numero_identificacion' => '1234567892',
                'fecha_nacimiento' => '1992-12-10',
                'genero' => 'MASCULINO',
                'direccion' => 'Plaza Central 789',
                'telefono' => '555-0103',
                'user_id' => $userIds[2],
                'tipo_documento_id' => 1,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'numero_identificacion' => '1234567893',
                'fecha_nacimiento' => '1988-03-07',
                'genero' => 'FEMENINO',
                'direccion' => 'Avenida Norte 321',
                'telefono' => '555-0104',
                'user_id' => $userIds[3],
                'tipo_documento_id' => 1,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}