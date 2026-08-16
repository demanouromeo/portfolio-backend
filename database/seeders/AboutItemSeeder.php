<?php

namespace Database\Seeders;

use App\Models\AboutItem;
use Illuminate\Database\Seeder;

class AboutItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'title_fr' => 'Développeur Frontend',
                'title_en' => 'Frontend Developer',
                'description_fr' => 'Je suis un développeur frontend avec une bonne expérience.',
                'description_en' => "I'm a frontend developer with solid experience.",
                'icon' => 'LetterText',
                'sort_order' => 1,
            ],
            [
                'title_fr' => 'Développeur Backend',
                'title_en' => 'Backend Developer',
                'description_fr' => 'Je maîtrise les bases du développement backend pour créer des APIs robustes.',
                'description_en' => 'I have a solid grasp of backend development to build robust APIs.',
                'icon' => 'CalendarSync',
                'sort_order' => 2,
            ],
            [
                'title_fr' => "Passionné par l'UI/UX",
                'title_en' => 'Passionate about UI/UX',
                'description_fr' => 'Créer des interfaces utilisateur attrayantes et fonctionnelles est ma priorité.',
                'description_en' => 'Creating attractive and functional user interfaces is my priority.',
                'icon' => 'Paintbrush',
                'sort_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            AboutItem::updateOrCreate(['icon' => $item['icon']], $item);
        }
    }
}
