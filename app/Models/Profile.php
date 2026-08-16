<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'surname',
        'alias',
        'email',
        'password',
        'years_experience',
        'phone',
        'linkedin',
        'facebook',
        'instagram',
        'github',
        'address',
        'city',
        'profile_picture_path',
        'short_intro_fr',
        'short_intro_en',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'years_experience' => 'integer',
        ];
    }
}
