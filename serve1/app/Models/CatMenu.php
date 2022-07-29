<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_level',
        'parent_id',
        'mode_id',
        'path',
        'name',
        'hidden',
        'sort',

        'seo_title',
        'seo_keywords',
        'seo_description',

        'meta_keep_alive',
        'meta_default_menu',
        'meta_title',
        'meta_icon',
        'meta_close_tab',
        'deleted_at'
    ];

    public function posts()
    {
        return $this->hasMany(PostArticle::class, 'cat_id', 'id');
    }

    public function modeName()
    {
        return $this->hasOne(Mode::class, 'id', 'mode_id');
    }

    public function mode()
    {
        return $this->hasOne(Mode::class, 'id', 'mode_id');
    }


}
