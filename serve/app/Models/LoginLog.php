<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'username',
        'address',
        'ip',
        'useragent',
        'platform',
        'platform_ver',
        'browser',
        'browser_ver',
        'device',
        'device_type',
        'status',
        'msg',
    ];

}
