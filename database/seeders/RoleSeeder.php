<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::create(['name' => 'Administrador']);
        $cajeroRole = Role::create(['name' => 'Cajero']);

        $permissions = [
            'ver dashboard',
            'ver productos', 'crear productos', 'editar productos', 'eliminar productos',
            'ver categorias', 'crear categorias', 'editar categorias', 'eliminar categorias',
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver ventas', 'crear ventas', 'editar ventas', 'anular ventas',
            'ver caja', 'abrir caja', 'cerrar caja',
            'ver inventario', 'gestionar inventario',
            'ver apartados', 'crear apartados', 'editar apartados',
            'ver reportes',
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            'ver bitacora',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->givePermissionTo(Permission::all());

        $cajeroRole->givePermissionTo([
            'ver dashboard',
            'ver productos',
            'ver categorias',
            'ver clientes', 'crear clientes', 'editar clientes',
            'ver ventas', 'crear ventas',
            'ver caja', 'abrir caja', 'cerrar caja',
            'ver apartados', 'crear apartados',
        ]);
    }
}
