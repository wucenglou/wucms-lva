<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fan extends Model
{
    use HasFactory;

    protected $guarded = []; //不可以注入字段
    //protected $fillable; // 可以注入字段

    public function fuser()
    {
        return $this->hasOne(User::class, 'id', 'fan_id');
    }

    public function suser()
    {
        return $this->hasOne(User::class, 'id', 'star_id');
    }
}
