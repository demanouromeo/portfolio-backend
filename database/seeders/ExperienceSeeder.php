<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experiences = [
            [
                'role_fr' => 'Software Engineer',
                'role_en' => 'Software Engineer',
                'company' => 'Google',
                'period_fr' => 'Sep 2022 - Présent',
                'period_en' => 'Sep 2022 - Present',
                'description_fr' => [
                    'Développement de nouvelles fonctionnalités pour Google Maps.',
                    "Optimisation des performances de l'application.",
                ],
                'description_en' => [
                    'Developed new features for Google Maps.',
                    "Optimized the application's performance.",
                ],
                'image_path' => 'uploads/experiences/google.png',
                'sort_order' => 1,
            ],
            [
                'role_fr' => 'Fullstack Developer',
                'role_en' => 'Fullstack Developer',
                'company' => 'Meta',
                'period_fr' => 'Jan 2021 - Août 2022',
                'period_en' => 'Jan 2021 - Aug 2022',
                'description_fr' => [
                    "Création d'une plateforme interne de collaboration pour les équipes.",
                    "Mise en place d'une architecture scalable et optimisée.",
                ],
                'description_en' => [
                    'Built an internal collaboration platform for teams.',
                    'Set up a scalable, optimized architecture.',
                ],
                'image_path' => 'uploads/experiences/meta.webp',
                'sort_order' => 2,
            ],
            [
                'role_fr' => 'Frontend Developer',
                'role_en' => 'Frontend Developer',
                'company' => 'Amazon',
                'period_fr' => 'Mai 2019 - Déc 2020',
                'period_en' => 'May 2019 - Dec 2020',
                'description_fr' => [
                    "Développement d'une interface utilisateur pour Amazon Web Services.",
                    'Implémentation des tests unitaires et E2E.',
                ],
                'description_en' => [
                    'Developed a user interface for Amazon Web Services.',
                    'Implemented unit and E2E tests.',
                ],
                'image_path' => 'uploads/experiences/amazon.png',
                'sort_order' => 3,
            ],
        ];

        foreach ($experiences as $experience) {
            Experience::updateOrCreate(
                ['company' => $experience['company']],
                $experience
            );
        }
    }
}
