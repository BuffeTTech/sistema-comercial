<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatisfactionAnswer extends Model
{
    use HasFactory;

    protected $table = "satisfaction_answers";

    protected $guarded = [];

    public function question(){
        return $this->belongsTo(SatisfactionQuestion::class, 'question_id');
    }

    public function bookings(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }

}
