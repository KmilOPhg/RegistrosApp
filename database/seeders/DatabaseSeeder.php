<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Registro;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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

        //Crear usuario developer y Admin por defecto
        $developer = User::create([
            'name' => 'Developer',
            'email' => 'camilohurtado256@gmail.com',
            'password' => bcrypt('123456')
        ]);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'massnivelsuplementosdeportivos@gmail.com',
            'password' => bcrypt('16461829')
        ]);

        //Creamos los roles
        $rol_dev = Role::create(['name' => 'dev']);
        $rol_admin = Role::create(['name' => 'admin']);
        $rol_user = Role::create(['name' => 'user']);

        //Conecar usuario developer con rol dev
        $developer->assignRole($rol_dev);
        $admin->assignRole($rol_admin);


        //Crear los estados
        Estado::create(['estado' => 'Pagado',]);
        Estado::create(['estado' => 'Credito',]);

        /**
         * COMENTAR ESTO LUEGO DE HACER LAS PRUEBAS
         */
        //Registro::factory(100)->create();
    }
}
