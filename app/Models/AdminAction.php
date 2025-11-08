<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    protected $fillable = [
        'admin_id','action_type','target_type','target_id','notes'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
