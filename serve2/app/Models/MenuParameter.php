<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_table',
        'menu_id',
        'type',
        'key',
        'value'
    ];

    protected $hidden = ['updated_at', 'created_at', 'deleted_at'];
}
