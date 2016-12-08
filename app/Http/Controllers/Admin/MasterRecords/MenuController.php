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
     *
     * @return Response
     */
    public function getLevelOne()
    {
        $menus = Menu::roots()->get();
        $roles = Role::orderBy('name')->get();
        return view('admin.menus.level-1', compact('menus', 'roles'));
    }

    /**
     * Insert or Update the Menu Level One records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLevelOne(Request $request)
    {
        $count = $this->saveMenus($request);
        // Set the flash message
        $this->setFlashMessage($count . ' Menus Level One has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus/level-1');
    }

    /**
     * Display a listing of the Menu Level Two for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function getLevelTwo($encodeId=null)
    {
        $menu_id = '';
        if($encodeId === null or $encodeId == 'all' or $encodeId == '') {
            $menus = Menu::roots()->get();
        }else{
            $menu_id = $this->getHashIds()->decode($encodeId)[0];
            $menus = Menu::where('menu_id', $menu_id)->get();
        }
        $sub = Menu::whereNotNull('parent_id')->count();
        $parents = Menu::roots()->orderBy('name')->get()->pluck('name', 'menu_id')->prepend('Select Parent', '');
        $roles = Role::orderBy('name')->get();
        return view('admin.menus.level-2', compact('menus', 'parents', 'sub', 'roles', 'menu_id'));
    }

    /**
     * Insert or Update the Menu Level Two records also Assigning a Level One as the parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLevelTwo(Request $request)
    {
        $count = $this->saveMenus($request, true);
        // Set the flash message
        $this->setFlashMessage($count . ' Menus Level Two has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus/level-2');
    }

    /**
     * Display a listing of the Menu Level Three for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function getLevelThree($encodeId=null)
    {
        $menu_id = '';
        if($encodeId === null or $encodeId == 'all' or $encodeId == '') {
            $menus = Menu::where('depth', 1)->get();
        }else{
            $menu_id = $this->getHashIds()->decode($encodeId)[0];
            $menus = Menu::where('menu_id', $menu_id)->get();
        }
        $filters = Menu::where('depth', 1)->get();
        $parents = Menu::where('depth', 0)->get();
        $sub = Menu::where('depth', 2)->count();
        $roles = Role::orderBy('name')->get();
        return view('admin.menus.level-3', compact('menus', 'parents', 'sub', 'roles', 'menu_id', 'filters'));
    }

    /**
     * Insert or Update the menu Level Three records also Assigning a Level Two as the parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLevelThree(Request $request)
    {
        $count = $this->saveMenus($request, true);
        // Set the flash message
        $this->setFlashMessage($count . ' Menus Level Three has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus/level-3');
    }

    /**
     * Display a listing of the Menu Level Four for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function getLevelFour($encodeId=null)
    {
        $menu_id = '';
        if($encodeId === null or $encodeId == 'all' or $encodeId == '') {
            $menus = Menu::where('depth', 2)->get();
        }else{
            $menu_id = $this->getHashIds()->decode($encodeId)[0];
            $menus = Menu::where('menu_id', $menu_id)->get();
        }
        $filters = Menu::where('depth', 2)->get();
        $parents = Menu::where('depth', 1)->get();
        $sub = Menu::where('depth', 3)->count();
        $roles = Role::orderBy('name')->get();
        return view('admin.menus.level-4', compact('menus', 'parents', 'sub', 'roles', 'menu_id', 'filters'));
    }

    /**
     * Insert or Update the menu Level Four records also Assigning a Level Three as the parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLevelFour(Request $request)
    {
        $count = $this->saveMenus($request, true);
        // Set the flash message
        $this->setFlashMessage($count . ' Menus Level Four has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus/level-4');
    }

    /**
     * Display a listing of the Menu Level Five for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function getLevelFive($encodeId=null)
    {
        $menu_id = '';
        if($encodeId === null or $encodeId == 'all' or $encodeId == '') {
            $menus = Menu::where('depth', 3)->get();
        }else{
            $menu_id = $this->getHashIds()->decode($encodeId)[0];
            $menus = Menu::where('menu_id', $menu_id)->get();
        }
        $filters = Menu::where('depth', 3)->get();
        $parents = Menu::where('depth', 2)->get();
        $sub = Menu::where('depth', 4)->count();
        $roles = Role::orderBy('name')->get();
        return view('admin.menus.level-5', compact('menus', 'parents', 'sub', 'roles', 'menu_id', 'filters'));
    }

    /**
     * Insert or Update the menu Level Five records also Assigning a Level Four as the parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLevelFive(Request $request)
    {
        $count = $this->saveMenus($request, true);
        // Set the flash message
        $this->setFlashMessage($count . ' Menus Level Five has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus/level-5');
    }

    /**
     * Delete a Menu from the list of Categories using a given menu id
     * @param $menu_id
     */
    public function delete($menu_id)
    {
        $menu = Menu::findOrFail($menu_id);
        //Delete The Menu Record
        $delete = ($menu !== null) ? $menu->delete() : null;
        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$menu->name.' Menu have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Helper Method for saving sub levels of a menu
     * @param mixed $request
     * @param Boolean $isRoot
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function saveMenus($request, $isRoot=false)
    {
        $inputs = $request->all();
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
    public function menusFilter(Request $request)
    {
        $inputs = $request->all();
        return redirect('/menus/level-'.$inputs['level'].'/' . $this->getHashIds()->encode($inputs['menu_id']));
    }
}
