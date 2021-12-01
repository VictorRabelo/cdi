<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkedSocialAccount extends Model
{
    use SoftDeletes;

    protected $table = 'linked_social_accounts';

    protected $primaryKey = 'id_social';
    
    protected $fillable = [
        'provider_name',
        'provider_id',
        'user_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}