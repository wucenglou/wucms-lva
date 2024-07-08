<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseMenu;

class Authority extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'authority_id',
        'authority_name',
        'parent_id',
        'authority_sys',
        'authority_describe'
    ];

    protected $primaryKey = 'authority_id';

    /**
     * 数组中的属性会被隐藏
     *
     * @var array
     */
    protected $hidden = ['updated_at', 'created_at'];

    public function menus()
    {
        return $this->belongsToMany(BaseMenu::class, 'authority_menus', 'authority_id', 'base_menu_id');
    }
}
