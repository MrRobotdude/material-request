<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            SpecificationSeeder::class,
            TypeSeeder::class,
            SubTypeSeeder::class,
            BrandProductSpecificationSeeder::class,
            ItemSeeder::class,
            ProjectSeeder::class,
            MaterialRequestSeeder::class,
            MaterialRequestItemSeeder::class,
            WarehouseLogSeeder::class,
        ]);
    }
}
