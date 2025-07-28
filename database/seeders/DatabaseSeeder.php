<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Registro;
use App\Models\Rol;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(100)->create();

        /**
         * Creamos las seeders que queramos aqui, creare user, roles y estado por defecto
         */

        User::create(['name' => 'Developer', 'email' => 'camilohurtado256@gmail.com', 'password' => bcrypt('123456')]);

        Rol::create(['nombre' => 'dev', 'descripcion' => 'Developer',]);
        Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador',]);
        Rol::create(['nombre' => 'user', 'descripcion' => 'Usuario',]);

        Estado::create(['estado' => 'Pagado',]);
        Estado::create(['estado' => 'Credito',]);

        /**
         * COMENTAR ESTO LUEGO DE HACER LAS PRUEBAS
         */
        //Registro::factory(100)->create();
    }
}
