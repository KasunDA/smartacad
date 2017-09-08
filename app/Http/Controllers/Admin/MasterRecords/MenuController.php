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
    public function getIndex()
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
    public function getLevel($no = 1, $encodeId=false)
    {
        $roles = Role::orderBy('name')->get();
        $filters = null;
        $menu = '';

        if(!$encodeId || $encodeId == 'all' || $encodeId == '') {
            $menus = ($no > 2) ? Menu::where('depth', $no - 2)->get() : Menu::roots()->orderBy('name')->get();
        }else{
            $menu = Menu::find($this->decode($encodeId));
            $menus = Menu::where('menu_id', $menu->menu_id)
                ->orderBy('name')
                ->get();
        }

        if($no == 2){
            $sub = Menu::whereNotNull('parent_id')->count();
            $parents = Menu::roots()
                ->orderBy('name')
                ->get()
                ->pluck('name', 'menu_id')
                ->prepend('- Select Parent -', '');
        }else if($no > 2){
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
    public function postLevel(Request $request)
    {
        $inputs = $request->all();
        $root = ($inputs['level'] >= 2) ? true : false;
        $count = $this->saveMenus($inputs, $root);
        // Set the flash message
        $this->setFlashMessage($count . ' Menus Level '.$inputs['level'].' has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus/level/' . $inputs['level']);
    }


    /**
     * Delete a Menu from the list of Categories using a given menu id
     * @param $menu_id
     */
    public function getDelete($menu_id)
    {
        $menu = Menu::findOrFail($menu_id);
        //Delete The Menu Record
        $delete = ($menu) ? $menu->delete() : false;
        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$menu->name.' Menu have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Helper Method for saving sub levels of a menu
     * @param mixed $inputs
     * @param Boolean $isRoot
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function saveMenus($inputs, $isRoot=false)
    {
        $count = 0;
        for($i = 0; $i < count($inputs['menu_id']); $i++){
            //Create New or Modify Existing Sub Menu
            $menu = ($inputs['menu_id'][$i] > 0) ? Menu::find($inputs['menu_id'][$i]) : new Menu();
            $menu->name = Str::upper($inputs['name'][$i]);
            $menu->url = $inputs['url'][$i];
            $menu->icon = $inputs['icon'][$i];
            $menu->active = $inputs['active'][$i];
            $menu->type = $inputs['type'][$i];
            $menu->sequence = $inputs['sequence'][$i];
            $menu->save();
            $count++;
            (isset($inputs['role_id'][$i + 1])) ? $menu->roles()->sync($inputs['role_id'][$i + 1]) : $menu->roles()->sync([]);

            if($isRoot){
                $root = Menu::find($inputs['parent_id'][$i]);
                //Attach a Parent(Menu) to it
                $menu->makeChildOf($root);
            }
        }
        return $count;
    }

    /**
     * Get The Menus Given a parent menu id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postFilter(Request $request)
    {
        $inputs = $request->all();
        return redirect('/menus/level/'.$inputs['level'].'/' . $this->encode($inputs['menu_id']));
    }
}
