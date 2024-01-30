<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SatisfactionQuestion extends Model
{
    use HasFactory;

    protected $table = "satisfaction_questions";

    protected $guarded = [];

    protected function hashedId(): Attribute
    {
        $hashids = new Hashids(config('app.name'));
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $hashids->encode($attributes['id']),
        );
    }

    public function user_answers(){
        return $this->hasMany(SatisfactionAnswer::class, 'question_id');
    }
    public function buffet(){
        return $this->hasMany(Buffet::class, 'buffet_id');
    }
}
