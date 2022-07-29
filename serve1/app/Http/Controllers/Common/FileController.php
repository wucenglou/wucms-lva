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
        if(empty($r['id'])){
            $path = Storage::disk('public')->put('/imgs', $r['file']);
            $url = env('APP_IMGS_URL') . '/storage/' . $path;
        } else {
            $path = Storage::disk('public')->put('/imgs', $r['file']);
            $url = env('APP_IMGS_URL') . '/storage/' . $path;
            PostFile::create();
        }
        
        return response()->json(["location" => $url], 200);
    }
}
