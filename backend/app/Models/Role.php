<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'user_id';
    
    public $timestamps = false;

    protected $fillable = [
        'role'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }
}