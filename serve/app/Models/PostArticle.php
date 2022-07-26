<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostArticle extends Model
{
    use HasFactory;

    protected $guarded = []; //不可以注入字段

    // protected $fillable = [
    //     'menu_level',
    //     'cat_id',
    //     'user_id',
    //     'mode_id',
    //     'status',
    //     'title',
    //     'keywords',
    //     'description',
    //     'content',
    //     'flag',

    //     'sort',
    //     'page_views',
    //     'praise_num',
    //     'trample_num',
    //     'forward_num',
    //     'extend'
    // ];

    protected $hidden = [
        'mode',
        'cat'
    ];


    public function userweb()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function mode()
    {
        return $this->hasOne(Mode::class, 'id', 'mode_id');
    }

    public function cat()
    {
        return $this->hasOne(CatMenu::class, 'id', 'cat_id');
    }

    // 所有评论
    public function comments()
    {
        // 'post_id'为'comments'表的外键，'id'为'post'的本地键。
        return $this->hasMany(Comment::class, 'post_id', 'id')->orderBy('created_at', 'desc');
    }
}
