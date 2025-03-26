<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'name'
    ];

    public function outlet()
    {
        return $this->hasMany(Outlet::class, 'branch_id');
    }
}
