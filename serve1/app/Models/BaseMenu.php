<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseMenu extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'menu_level',
        'parent_id',
        'path',
        'name',
        'hidden',
        'component',
        'sort',
        'keep_alive',
        'default_menu',
        'title',
        'icon',
        'close_tab',
        'deleted_at'
    ];

    public function parameters()
    {
        return $this->hasMany(MenuParameter::class, 'menu_id', 'id');
    }
    // protected $hidden = ['pivot'];
}
