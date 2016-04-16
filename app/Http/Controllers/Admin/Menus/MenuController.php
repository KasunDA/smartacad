<?php

namespace App\Http\Controllers\Admin\Menus;

use App\Models\Admin\Menus\Menu;
use App\Models\Admin\Menus\MenuHeader;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class MenuController extends Controller
{

    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $menu_header_lists = MenuHeader::orderBy('menu_header')->lists('menu_header', 'menu_header_id')->prepend('Select Menu header', '');
        $roles = Role::orderBy('name')->get();
        $menus = Menu::orderBy('menu_header_id')->get();
        return view('admin.menus.menu', compact('menus', 'menu_header_lists','roles'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['menu_id']); $i++){
            $menu = ($inputs['menu_id'][$i] > 0) ? Menu::find($inputs['menu_id'][$i]) : new Menu();
            $menu->menu = Str::upper($inputs['menu'][$i]);
            $menu->menu_url = $inputs['menu_url'][$i];
            $menu->icon = $inputs['icon'][$i];
            $menu->active = $inputs['active'][$i];
            $menu->type = $inputs['type'][$i];
            $menu->sequence = $inputs['sequence'][$i];
            $menu->menu_header_id = $inputs['menu_header_id'][$i];
            if($menu->save()){
                $count = $count+1;
                (isset($inputs['role_id'][$i + 1])) ? $menu->roles()->sync($inputs['role_id'][$i + 1]) : $menu->roles()->sync([]);
            }
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Menus has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menus');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $menu = Menu::findOrFail($id);
        //Delete The Warder Record
        $delete = ($menu !== null) ? $menu->delete() : null;

        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$menu->menu.' menu have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
