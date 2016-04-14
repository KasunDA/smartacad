<?php

namespace App\Http\Controllers\Admin\Menus;

use App\Models\Admin\Menus\MenuItem;
use App\Models\Admin\Menus\SubMenuItem;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class SubMenuItemController extends Controller
{
    /**
     *
     * Make sure the user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the Menu Items for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function getIndex($encodeId=null)
    {
        $menu_item_id = '';
        if($encodeId === null) {
            $sub_menu_items = SubMenuItem::orderBy('menu_item_id', 'sequence')->get();
        }else{
            $menu_item_id = $this->getHashIds()->decode($encodeId)[0];
            $sub_menu_items = SubMenuItem::where('menu_item_id', $menu_item_id)->orderBy('menu_item_id', 'sequence')->get();
        }
        $menu_item_lists = MenuItem::orderBy('menu_item')->lists('menu_item', 'menu_item_id')->prepend('Select Menu Item','');
        $roles = Role::orderBy('name')->get();

        return view('admin.menus.sub-menu-item', compact('sub_menu_items', 'menu_item_lists', 'roles', 'menu_item_id'));
    }

    /**
     * Insert or Update the menu items records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['sub_menu_item_id']); $i++) {
            $sub_menu_item = ($inputs['sub_menu_item_id'][$i] > 0) ? SubMenuItem::find($inputs['sub_menu_item_id'][$i]) : new SubMenuItem();
            $sub_menu_item->sub_menu_item = Str::upper($inputs['sub_menu_item'][$i]);
            $sub_menu_item->sub_menu_item_url = $inputs['sub_menu_item_url'][$i];
            $sub_menu_item->sub_menu_item_icon = $inputs['sub_menu_item_icon'][$i];
            $sub_menu_item->active = $inputs['active'][$i];
            $sub_menu_item->sequence = $inputs['sequence'][$i];
            $sub_menu_item->menu_item_id = $inputs['menu_item_id'][$i];
//            $count = ($sub_menu_item->save()) ? $count + 1 : '';

            if($sub_menu_item->save()){
                $count = $count+1;
                (isset($inputs['role_id'][$i + 1])) ? $sub_menu_item->roles()->sync($inputs['role_id'][$i + 1]) : $sub_menu_item->roles()->sync([]);
            }
        }
        // Set the flash message
        if ($count > 0)
            $this->setFlashMessage($count . ' Sub Menu Items has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/sub-menu-items');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $sub_menu_item = SubMenuItem::findOrFail($id);
        //Delete The Warder Record
        $delete = ($sub_menu_item !== null) ? $sub_menu_item->delete() : null;

        if ($delete) {
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! ' . $sub_menu_item->sub_menu_item . ' sub menu item have been deleted.', 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Get The Sub Menu Items Given a menu item id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postMenuItem(Request $request)
    {
        $inputs = $request->all();
        return redirect('/sub-menu-items/index/' . $this->getHashIds()->encode($inputs['menu_item_id']));
    }
}
