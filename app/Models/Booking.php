<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function buffet() {
        return $this->belongsTo(Buffet::class);
    }
    public function schedule() {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    } 
    public function food() {
        return $this->belongsTo(Food::class);
    } 
    public function decoration() {
        return $this->belongsTo(Decoration::class);
    } 
    public function user() {
        return $this->belongsTo(User::class);
    } 
}
