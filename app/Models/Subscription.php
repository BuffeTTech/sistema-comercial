<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function buffet_subscriptions()
    {
        return $this->hasMany(BuffetSubscription::class, 'subscription_id');
    }
}
