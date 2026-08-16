<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'title_fr',
        'title_en',
        'description_fr',
        'description_en',
        'technologies',
        'repo_link',
        'video_link',
        'play_store_link',
        'apple_store_link',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
        ];
    }

    public function featureGraphics(): HasMany
    {
        return $this->hasMany(ProjectFeatureGraphic::class)->orderBy('sort_order');
    }

    public function demoImages(): HasMany
    {
        return $this->hasMany(ProjectDemoImage::class)->orderBy('sort_order');
    }
}
