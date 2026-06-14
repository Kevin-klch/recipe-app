<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;

class Recipe extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'difficulty',
        'price_level',
        'servings',
        'kcal',
        'diet_type',
        'meal_type',
        'instructions',
        'notes',
        'source_url',
        'photo_path',
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}