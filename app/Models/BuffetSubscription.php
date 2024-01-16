<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuffetSubscription extends Model
{
    use HasFactory;

    protected $table = 'buffet_subscriptions';

    protected $guarded = [];

    public function buffet()
    {
        return $this->belongsTo(Buffet::class, 'buffet_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
