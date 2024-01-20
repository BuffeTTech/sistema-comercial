<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected function hashedId(): Attribute
    {
        $hashids = new Hashids(config('app.name'));
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $hashids->encode($attributes['id']),
        );
    }

    public function ownedBuffets()
    {
        return $this->hasMany(Buffet::class, 'owner_id');
    }

    public function user_phone1() {
        return $this->belongsTo(Phone::class, 'phone1');
    }
    public function user_phone2() {
        return $this->belongsTo(Phone::class, 'phone2');
    }
    public function user_address() {
        return $this->belongsTo(Address::class, 'address');
    }

    public function isBuffet(): bool {
        return !!$this->buffet_id;
    }
    public function isOwner(): bool
    {
        return $this->ownedBuffets()->exists();
    }


}
