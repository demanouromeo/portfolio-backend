<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // description_fr/en share the same Lorem Ipsum placeholder text seeded from
        // Projects.tsx - it isn't real French, so there's nothing to translate. Admin
        // replaces both with real copy per project via the dashboard.
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae magni deserunt debitis recusandae ab harum totam, eum facilis et ratione officia ut inventore aspernatur';

        $projects = [
            [
                'title_fr' => 'Gestionnaire de tâches',
                'title_en' => 'Task Manager',
                'technologies' => ['React', 'Tailwind CSS'],
                'image' => '1.png',
            ],
            [
                'title_fr' => 'Plateforme E-commerce',
                'title_en' => 'E-commerce Platform',
                'technologies' => ['Next.js', 'TypeScript', 'Prisma'],
                'image' => '2.png',
            ],
            [
                'title_fr' => 'Portfolio interactif',
                'title_en' => 'Interactive Portfolio',
                'technologies' => ['HTML', 'CSS', 'JavaScript', 'Node.js'],
                'image' => '3.png',
            ],
            [
                'title_fr' => 'Application de Chat en temps réel',
                'title_en' => 'Real-time Chat Application',
                'technologies' => ['React', 'Socket.io'],
                'image' => '4.png',
            ],
            [
                'title_fr' => 'Système de réservation de salles',
                'title_en' => 'Room Booking System',
                'technologies' => ['Next.js', 'MongoDB', 'Chakra UI'],
                'image' => '5.png',
            ],
            [
                'title_fr' => 'Analyseur de sentiment',
                'title_en' => 'Sentiment Analyzer',
                'technologies' => ['Python', 'Flask'],
                'image' => '6.png',
            ],
        ];

        foreach ($projects as $index => $data) {
            $project = Project::updateOrCreate(
                ['title_fr' => $data['title_fr']],
                [
                    'title_en' => $data['title_en'],
                    'description_fr' => $lorem,
                    'description_en' => $lorem,
                    'technologies' => $data['technologies'],
                    'repo_link' => null,
                    'video_link' => null,
                    'sort_order' => $index + 1,
                ]
            );

            $project->featureGraphics()->updateOrCreate(
                ['sort_order' => 1],
                ['path' => 'uploads/projects/' . $data['image']]
            );
        }
    }
}
