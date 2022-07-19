<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDynamic extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_level',
        'cat_id',
        'user_id',
        'mode_id',
        'status',
        'title',
        'keywords',
        'description',
        'content',
        'flag',

        'sort',
        'page_views',
        'praise_num',
        'trample_num',
        'forward_num',
        'extend'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'user',
        'mode',
        'cat'
    ];


    public function scopeTable($query, $tableName)
    {
        $query->from($tableName);
    }


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function mode()
    {
        return $this->hasOne(Mode::class, 'id', 'mode_id');
    }

    public function cat()
    {
        return $this->hasOne(CatMenu::class, 'id', 'cat_id');
    }
}
