<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatMenu;


class IndexController extends Controller
{
    //
    public function index()
    {
        $navss = CatMenu::all();
        $navs = $this->toHumpTree($navss->toArray(),'id','parent_id');
        // return $navs;
        return view('welcome',compact('navs'));
    }
}
