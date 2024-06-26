<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_secret',
        'refresh_token',
        'access_token',
        'access_token_expires_at'
    ];
}
