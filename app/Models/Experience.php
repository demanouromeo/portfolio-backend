<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'role_fr',
        'role_en',
        'company',
        'period_fr',
        'period_en',
        'description_fr',
        'description_en',
        'image_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'description_fr' => 'array',
            'description_en' => 'array',
        ];
    }
}
