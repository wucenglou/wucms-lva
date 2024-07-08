<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    //关联用户
    public function post()
    {
        return $this->belongsTo(PostArticle::class, 'post_id', 'id');
    }

    //关联用户
    public function mode()
    {
        return $this->belongsTo(Mode::class, 'mode_id', 'id');
    }

    //关联用户
    public function cat()
    {
        return $this->belongsTo(CatMenu::class, 'cat_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
