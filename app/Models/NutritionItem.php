<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionItem extends Model
{
    protected $fillable = [
        'name',
        'reference_amount',
        'reference_unit',
        'kcal',
        'protein',
        'carbs',
        'fat',
    ];
}