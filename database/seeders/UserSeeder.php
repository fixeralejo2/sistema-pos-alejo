<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@cheveramy.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Administrador');

        $cajero = User::create([
            'name' => 'Cajero',
            'email' => 'cajero@cheveramy.com',
            'password' => Hash::make('password'),
        ]);
        $cajero->assignRole('Cajero');
    }
}
