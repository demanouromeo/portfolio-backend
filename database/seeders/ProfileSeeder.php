<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profile::updateOrCreate(
            ['email' => 'gabinprof1@gmail.com'],
            [
                'name' => 'DEMANOU YMELE',
                'surname' => 'Romeo Gabin',
                'alias' => 'ROMEODEV',
                'password' => 'Rolindsay1988_',
                'years_experience' => 9,
                'phone' => '+1 418 261 3537',
                'linkedin' => null,
                'facebook' => null,
                'instagram' => null,
                'github' => 'Demanouromeo',
                'address' => '265 Rue de la Colombière Ouest, Québec, G1L1C5',
                'city' => 'Québec',
                'profile_picture_path' => 'uploads/profile/profile_picture.png',
                'short_intro_fr' => "Je suis un développeur fullstack avec 5 ans d'expérience, utilisant React et Node.js. Contactez-moi si vous avez besoin de mes services.",
                'short_intro_en' => "I'm a fullstack developer with 5 years of experience, using React and Node.js. Contact me if you need my services.",
            ]
        );
    }
}
