<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Img;
use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    //
    public function uploadImgs(Request $request)
    {
        $r = $request->all();
        // 如果是删除图片，单图
        if(isset($r['deleteid'])){
            $img = Img::find($r['deleteid']);
            $img->delete();
            $path = substr($img['url'], 9);
            Storage::disk('public')->delete($path);
            return $this->response($img);
        } else {
            if (empty($r['id'])) {
                $path = Storage::disk('public')->put('/imgs', $r['file']);
                $url = env('APP_IMGS_URL') . '/storage/' . $path;
                return $this->response(["location" => $url]);
            } else {
                $path = Storage::disk('public')->put('/imgs', $r['file']);
                $url = env('APP_IMGS_URL') . '/storage/' . $path;
                // PostFile::create();
                return $this->response(["id" => 1, "name" => "444", "url" => $url]);
            }
        }
        // dd($r);
        // dd(json_decode($r['form'], true)['catId']);
        
        
    }
}
