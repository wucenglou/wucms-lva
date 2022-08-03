<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    //
    public function uploadImgs(Request $request)
    {
        $r = $request->all();
        dd($r);
        // dd(json_decode($r['form'], true)['catId']);
        if(empty($r['id'])){
            $path = Storage::disk('public')->put('/imgs', $r['file']);
            $url = env('APP_IMGS_URL') . '/storage/' . $path;
            return $this->response(["location" => $url]);
        } else {
            $path = Storage::disk('public')->put('/imgs', $r['file']);
            $url = env('APP_IMGS_URL') . '/storage/' . $path;
            // PostFile::create();
            return $this->response(["id"=> 1,"name" => "444","url"=>$url]);
        }
        
    }
}
