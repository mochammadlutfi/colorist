<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'image'
    ];

    protected $appends = [
        'image_url',
        
    ];
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
    
    public function getImageUrlAttribute($value)
    {
        if($this->attributes['image']){
            return $this->attributes['image'];
        }else{
            return '/images/placeholders/avatar.png';
        }
    }
    
    // public function outlet()
    // {
    //     return $this->belongsTo(Outlet::class, 'outlet_id');
    // }
    
    public function outlet()
    {
        return $this->belongsToMany(Outlet::class, 'user_outlet_rel', 'user_id', 'outlet_id')
                    ->withTimestamps();
    }
}
