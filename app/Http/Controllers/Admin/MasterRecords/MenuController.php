<?php

namespace App\Http\Controllers\Admin\MasterRecords;

use App\Models\Admin\Menus\Menu;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display a listing of all the Menus for Master Records.
     *
     * @return Response
     */
    public function index()
    {
        $menus = Menu::roots()->get();
        $max = Menu::max('depth');
        
        return view('admin.menus.index', compact('menus', 'max'));
    }


    /**
     * Display a listing of the Menu Level One for Master Records.
     * @param Int $no
     * @param Boolean $encodeId
     * @return Response
     */
    public function level($no = 1, $encodeId=false)
    {
        $roles = Role::orderBy('name')->get();
        $filters = null;
        $menu = '';

        if (!$encodeId || $encodeId == 'all' || $encodeId == '') {
            $menus = ($no > 2) 
                ? Menu::where('depth', $no - 2)->get() 
                : Menu::roots()->orderBy('name')->get();
        }else{
            $menu = Menu::find($this->decode($encodeId));
            $menus = Menu::where('menu_id', $menu->menu_id)
                ->orderBy('name')
                ->get();
        }

        if ($no == 2) {
            $sub = Menu::whereNotNull('parent_id')->count();
            $parents = Menu::roots()
                ->orderBy('name')
                ->get()
                ->pluck('name', 'menu_id')
                ->prepend('- Select Parent -', '');
        } elseif($no > 2) {
            $filters = Menu::where('depth', $no - 2)
                ->orderBy('name')
                ->get();
            $parents = Menu::where('depth', $no - 3)
                ->orderBy('name')
                ->get();
            $sub = Menu::where('depth', $no - 1)->count();
        }

        return ($no == 1)
            ? view('admin.menus.menu', compact('menus', 'roles', 'no'))
            : view('admin.menus.sub-menus', compact('menus', 'parents', 'sub', 'roles', 'menu', 'no', 'filters'));
    }

    /**
     * Insert or Update the Menu Level One records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveLevel(Request $request)
    {
        $inputs = $request->all();
        $root = ($inputs['level'] >= 2) ? true : false;
        $count = $this->_saveMenu($inputs, $root);
        
        $this->setFlashMessage($count . ' Menus Level '.$inputs['level'].' has been successfully updated.', 1);
        
        return redirect('/menus/level/' . $inputs['level']);
    }


    /**
     * Delete a Menu from the list of Categories using a given menu id
     * @param $menu_id
     */
    public function delete($menu_id)
    {
        $menu = Menu::findOrFail($menu_id);
     
        ($menu && $menu->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$menu->name.' Menu have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Helper Method for saving sub levels of a menu
     * @param mixed $inputs
     * @param Boolean $isRoot
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function _saveMenu($inputs, $isRoot=false)
    {
        $count = 0;
        for ($i = 0; $i < count($inputs['menu_id']); $i++) {
            //Create New or Modify Existing Sub Menu
            $menu = ($inputs['menu_id'][$i] > 0) 
                ? Menu::find($inputs['menu_id'][$i]) 
                : new Menu();
            
            $menu->name = Str::upper($inputs['name'][$i]);
            $menu->url = $inputs['url'][$i];
            $menu->icon = $inputs['icon'][$i];
            $menu->active = $inputs['active'][$i];
            $menu->type = $inputs['type'][$i];
            $menu->sequence = $inputs['sequence'][$i];
            $menu->save();
            $count++;
            (isset($inputs['role_id'][$i + 1])) 
                ? $menu->roles()->sync($inputs['role_id'][$i + 1]) 
                : $menu->roles()->sync([]);

            if ($isRoot) {
                $root = Menu::find($inputs['parent_id'][$i]);
                $menu->makeChildOf($root); //Attach a Parent(Menu) to it
            }
        }
        
        return $count;
    }

    /**
     * Get The Menus Given a parent menu id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function filter(Request $request)
    {
        $inputs = $request->all();
        return redirect('/menus/level/'.$inputs['level'].'/' . $this->encode($inputs['menu_id']));
    }
}
