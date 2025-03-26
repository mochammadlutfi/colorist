<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'uploads';

    protected $fillable = [
        'user_id', 'outlet_id', 'file_name', 'file_path', 'status', 'total_records', 'processed_records', 'error_message'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
