<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        // $role = Role::create(['name' => 'administrator']);
        // $role->givePermissionTo('crear_prestamo','crear_cliente', 'aprobar_prestamo');
 
        // $role = Role::create(['name' => 'recepcionista']);
        // $role->givePermissionTo('crear_prestamo','crear_cliente');

        $role = Role::create(['name' => 'analista']);
        $role->givePermissionTo('crear_prestamo','crear_cliente');
    }
}
