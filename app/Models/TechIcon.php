<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechIcon extends Model
{
    protected $fillable = [
        'tech_name',
        'tech_category',
        'icon_path',
        'sort_order',
    ];
}
