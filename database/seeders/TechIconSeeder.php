<?php

namespace Database\Seeders;

use App\Models\TechIcon;
use Illuminate\Database\Seeder;

class TechIconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = [
            ['tech_name' => 'HTML', 'tech_category' => 'programming_language', 'icon_path' => 'uploads/tech-icons/html.png'],
            ['tech_name' => 'CSS', 'tech_category' => 'programming_language', 'icon_path' => 'uploads/tech-icons/css.png'],
            ['tech_name' => 'JavaScript', 'tech_category' => 'programming_language', 'icon_path' => 'uploads/tech-icons/js.png'],
            ['tech_name' => 'React', 'tech_category' => 'framework', 'icon_path' => 'uploads/tech-icons/react.png'],
            ['tech_name' => 'Node.js', 'tech_category' => 'framework', 'icon_path' => 'uploads/tech-icons/node-js.png'],
            ['tech_name' => 'Tailwind CSS', 'tech_category' => 'framework', 'icon_path' => 'uploads/tech-icons/tailwind.png'],
            ['tech_name' => 'TypeScript', 'tech_category' => 'programming_language', 'icon_path' => 'uploads/tech-icons/typescript.svg'],
            ['tech_name' => 'Next.js', 'tech_category' => 'framework', 'icon_path' => 'uploads/tech-icons/next-js.webp'],
            ['tech_name' => 'Prisma', 'tech_category' => 'framework', 'icon_path' => 'uploads/tech-icons/prisma.webp'],
        ];

        foreach ($icons as $index => $icon) {
            TechIcon::updateOrCreate(
                ['tech_name' => $icon['tech_name']],
                $icon + ['sort_order' => $index + 1]
            );
        }
    }
}
