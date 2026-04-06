<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Bolsos', 'description' => 'Bolsos y carteras', 'active' => true]);
        Category::create(['name' => 'Joyas', 'description' => 'Joyas y accesorios', 'active' => true]);
        Category::create(['name' => 'Ropa', 'description' => 'Prendas de vestir', 'active' => true]);
        Category::create(['name' => 'Calzado', 'description' => 'Zapatos y sandalias', 'active' => true]);
    }
}
