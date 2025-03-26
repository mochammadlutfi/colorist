<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlets';

    protected $fillable = [
        'name'
    ];

    // public function employee()
    // {
    //     return $this->hasMany(\App\Models\Employee::class, 'expertise_id');
    // }
    
    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_outlet_rel', 'outlet_id', 'user_id')
                    ->withTimestamps();
    }
}
