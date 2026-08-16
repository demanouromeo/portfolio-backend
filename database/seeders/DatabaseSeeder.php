<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProfileSeeder::class,
            AboutItemSeeder::class,
            TechIconSeeder::class,
            ExperienceSeeder::class,
            ProjectSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
