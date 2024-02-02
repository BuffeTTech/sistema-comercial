<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatisfactionAnswer extends Model
{
    use HasFactory;

    protected $table = "satisfaction_answers";

    protected $guarded = [];

    protected function hashedId(): Attribute
    {
        $hashids = new Hashids(config('app.name'));
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $hashids->encode($attributes['id']),
        );
    }

    public function question(){
        return $this->belongsTo(SatisfactionQuestion::class, 'question_id');
    }

    public function booking(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }

}
