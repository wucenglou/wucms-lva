<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorityMenu extends Model
{
    use HasFactory;
    protected $fillable = [
        'authority_id',
        'base_menu_id'
    ];

}
