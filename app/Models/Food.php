<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = "foods";

    protected $guarded = [];

    public function photos()
    {
        return $this->hasMany(FoodPhoto::class, 'food_id');
    }
}
