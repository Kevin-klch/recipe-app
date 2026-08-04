<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;
use App\Models\User;

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
        'total_kcal',
        'total_protein',
        'total_carbs',
        'total_fat',
    ];

    /**
     * Beschriftungen fuer die in der DB englisch gespeicherten Werte.
     * Unbekannte Werte werden unveraendert durchgereicht.
     */
    public function getDifficultyLabelAttribute(): ?string
    {
        return match ($this->difficulty) {
            'easy' => 'Einfach',
            'medium' => 'Mittel',
            'hard' => 'Aufwendig',
            default => $this->difficulty,
        };
    }

    public function getDietLabelAttribute(): ?string
    {
        return match ($this->diet_type) {
            'none' => 'Normal',
            'vegetarian' => 'Vegetarisch',
            'vegan' => 'Vegan',
            default => $this->diet_type,
        };
    }

    public function getKcalPerServingAttribute()
    {
        return $this->servings
            ? round($this->kcal / $this->servings)
            : $this->kcal;
    }

    public function getProteinPerServingAttribute()
    {
        return $this->servings
            ? round($this->protein / $this->servings, 1)
            : $this->protein;
    }

    public function getCarbsPerServingAttribute()
    {
        return $this->servings
            ? round($this->carbs / $this->servings, 1)
            : $this->carbs;
    }

    public function getFatPerServingAttribute()
    {
        return $this->servings
            ? round($this->fat / $this->servings, 1)
            : $this->fat;
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}