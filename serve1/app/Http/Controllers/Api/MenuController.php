<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BaseMenu;
use App\Models\Authority;
use App\Models\AuthorityMenu;
use App\Models\MenuParameter;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;

class MenuController extends Controller
{
    public function getMenu()
    {
        $user =  Auth::guard('api')->user();
        if ($user['authority_id'] != 1) {
            $authority = Authority::find($user['authority_id']);
            $BaseMenu = $authority->menus;
        } else {
            $BaseMenu = BaseMenu::orderBy('parent_id', 'asc')->orderBy('sort', 'asc')->get();
        }
        $items = $this->adapterMenu($BaseMenu);
        $arr = $this->tree($items);
        return response()->json(['code' => 0, 'data' => [
            'menus' => $arr,
        ], 'msg' => "获取成功"]);
    }

    public function tree($list, $pid = 0)
    {
        $arr = array();
        foreach ($list as $key => $value) {
            if ($value['parentId'] == $pid) {
                $value['children'] = $this->tree($list, $value['id']);
                $arr[] = $value;
                unset($list[$key]);
            }
        }
        return $arr;
    }

    public function getMenuList()
    {
        $user =  Auth::guard('api')->user();
        if ($user['authority_id'] != 1) {
            $authority = Authority::find($user['authority_id']);
            $BaseMenu = $authority->menus;
        } else {
            $BaseMenu = BaseMenu::orderBy('parent_id', 'asc')->orderBy('sort', 'asc')->get();
        }
        $items = $this->adapterMenu($BaseMenu);
        $arr = $this->tree($items);
        return response()->json(['code' => 0, 'data' => [
            'list' => $arr,
        ], 'msg' => "获取成功"]);
    }

    public function addBaseMenu(Request $request)
    {
        $res = $request->all();
        $flag =  BaseMenu::create(array(
            'menu_level' => '0',
            'parent_id' => $res['parentId'],
            'path' => $res['path'],
            'name' => $res['name'],
            'hidden' => $res['hidden'],
            'component' => $res['component'],
            'sort' => $res['sort'],
            'keep_alive' => $res['meta']['keepAlive'],
            'default_menu' => $res['meta']['defaultMenu'],
            'title' => $res['meta']['title'],
            'icon' => $res['meta']['icon'],
            'close_tab' => $res['meta']['closeTab'] ?? 1
        ));
        $user =  Auth::guard('api')->user();
        AuthorityMenu::create(['authority_id' => $user['authority_id'], 'base_menu_id' => $flag['id']]);
        if ($flag) {
            return response()->json(['code' => 0, 'data' => '', 'msg' => "添加成功"]);
        } else {
            return response()->json(['code' => 0, 'data' => '', 'msg' => "添加失败"]);
        }
    }

    public function getBaseMenuById(Request $request)
    {
        $id = $request->id;
        $oneMenu = BaseMenu::find($id);
        // return $oneMenu->parameters->where('menu_table',"BaseMenu");
        $item = $this->adapterMenu([$oneMenu]);

        return response()->json([
            'code' => 0, 'data' => [
                'menu' => $item,
            ], 'msg' => "获取成功"
        ]);
    }

    public function deleteBaseMenu(Request $request)
    {
        $id = $request->ID;
        $flag = BaseMenu::destroy($id);
        if ($flag) {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "删除成功"
            ]);
        } else {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "要删除的数据不存在"
            ]);
        }
    }

    public function updateBaseMenu(Request $request)
    {
        $res = $request->all();
        // dd($res);
        // 如果携带参数
        $this->menu_parameter($res['parameters'], "BaseMenu", $res['id']);
        try {
            $flag =  BaseMenu::where('id', $res['id'])->update(array(
                'menu_level' => '0',
                'parent_id' => $res['parentId'],
                'path' => $res['path'],
                'name' => $res['name'],
                'hidden' => $res['hidden'],
                'component' => $res['component'],
                'sort' => $res['sort'],
                'keep_alive' => $res['meta']['keepAlive'],
                'default_menu' => $res['meta']['defaultMenu'],
                'title' => $res['meta']['title'],
                'icon' => $res['meta']['icon'],
                'close_tab' => $res['meta']['closeTab']
            ));
        } catch (Exception $e) {
            $flag = false;
        }
        if($flag){
            return $this->response($flag);
        } else {
            return $this->response($flag,"请检查参数，操作");
        }  
    }

    public function getMenuAuthority()
    {
        $authorityId = request('authorityId');
        $res = Authority::find($authorityId)->menus->makeHidden('pivot');
        if (count($res) > 0) {
            return response()->json([
                'code' => 0, 'data' => [
                    'menus' => $this->adapterMenu($res),
                ], 'msg' => "获取成功"
            ]);
        }
        return response()->json([
            'code' => 0, 'data' => [
                'menus' => '',
            ], 'msg' => "获取成功"
        ]);
    }

    public function addMenuAuthority()
    {
        $res = request();
        foreach ($res['menus'] as $menu) {
            $arr[] = $menu['ID'];
        }
        $authority = Authority::find($res['authorityId']);
        $authority->menus()->sync($arr);
        // dd(request()->server('REQUEST_URI'));
        // $routes = Route::getRoutes()->get();
        // foreach ($routes as $k => $value) {
        //     $path[$k]['uri'] = $value->uri;
        //     $path[$k]['path'] = $value->methods;
        // }
        // return $arr;
        return response()->json([
            'code' => 0, 'data' => [], 'msg' => "添加成功"
        ]);
    }


    /**
     * 适配菜单数据类型
     *
     * @param [type] $Menu
     * @return void
     */
    public function adapterMenu($Menu, $menu_table = 'BaseMenu')
    {

        try {
            $length = count($Menu);
        } catch (Exception $e) {
            $length = 0;
        }
        // dd($Menu);
        if ($length) {
            for ($i = 0; $i < $length; $i++) {
                $meta['closeTab'] = $Menu[$i]['close_tab'];
                $meta['defaultMenu'] = $Menu[$i]['default_menu'];
                $meta['icon'] = $Menu[$i]['icon'];
                $meta['keepAlive'] = $Menu[$i]['keep_alive'] ? true : false;
                $meta['title'] = $Menu[$i]['title'];

                $items[$i]['id'] = (string)$Menu[$i]['id'];
                $items[$i]['parentId'] = $Menu[$i]['parent_id'];
                $items[$i]['meta'] = $meta;
                $items[$i]['children'] = null;
                $items[$i]['authoritys'] = null;
                $items[$i]['component'] = $Menu[$i]['component'];
                $items[$i]['menuId'] = $Menu[$i]['id'];
                $items[$i]['hidden'] = $Menu[$i]['hidden'];
                $items[$i]['name'] = $Menu[$i]['name'];
                $items[$i]['parameters'] = $Menu[$i]['parameters'];
                $items[$i]['path'] = $Menu[$i]['path'];
                $items[$i]['sort'] = $Menu[$i]['sort'];
                $items[$i]['title'] = $Menu[$i]['title'];
            }
            return $items;
        } else {
            $meta['closeTab'] = $Menu['close_tab'];
            $meta['defaultMenu'] = $Menu['default_menu'];
            $meta['icon'] = $Menu['icon'];
            $meta['keepAlive'] = $Menu['keep_alive'];
            $meta['title'] = $Menu['title'];

            $item['id'] = (string)$Menu['id'];
            $item['parentId'] = $Menu['parent_id'];
            $item['meta'] = $meta;
            $item['children'] = null;
            $item['authoritys'] = null;
            $item['component'] = $Menu['component'];
            $item['menuId'] = $Menu['id'];
            $item['hidden'] = $Menu['hidden'];
            $item['name'] = $Menu['name'];
            $item['parameters'] = $Menu->parameters->where('menu_table', $menu_table);
            $item['path'] = $Menu['path'];
            $item['sort'] = $Menu['sort'];
            $items['title'] = $Menu['title'];

            return $item;
        }
    }
}
