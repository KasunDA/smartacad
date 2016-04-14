<?php

namespace App\Http\Controllers\Admin\Menus;

use App\Models\Admin\Menus\SubMenuItem;
use App\Models\Admin\Menus\SubMostMenuItem;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubMostMenuItemController extends Controller
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
        $sub_menu_item_id = '';
        if($encodeId === null) {
            $sub_most_menu_items = SubMostMenuItem::orderBy('sub_menu_item_id', 'sequence')->get();
        }else{
            $sub_most_menu_item_id = $this->getHashIds()->decode($encodeId)[0];
            $sub_most_menu_items = SubMostMenuItem::where('sub_menu_item_id', $sub_most_menu_item_id)->orderBy('sub_menu_item_id', 'sequence')->get();
        }
        $sub_menu_item_lists = SubMenuItem::orderBy('sub_menu_item')->lists('sub_menu_item', 'sub_menu_item_id')->prepend('Select Sub Menu Item','');
        $roles = Role::orderBy('name')->get();

        return view('admin.menus.sub-most-menu-item', compact('sub_most_menu_items', 'sub_menu_item_lists', 'roles', 'sub_menu_item_id'));
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

        for ($i = 0; $i < count($inputs['sub_most_menu_item_id']); $i++) {
            $sub_most_menu_item = ($inputs['sub_most_menu_item_id'][$i] > 0) ? SubMostMenuItem::find($inputs['sub_most_menu_item_id'][$i]) : new SubMostMenuItem();
            $sub_most_menu_item->sub_most_menu_item = $inputs['sub_most_menu_item'][$i];
            $sub_most_menu_item->sub_most_menu_item_url = $inputs['sub_most_menu_item_url'][$i];
            $sub_most_menu_item->sub_most_menu_item_icon = $inputs['sub_most_menu_item_icon'][$i];
            $sub_most_menu_item->active = $inputs['active'][$i];
            $sub_most_menu_item->sequence = $inputs['sequence'][$i];
            $sub_most_menu_item->sub_menu_item_id = $inputs['sub_menu_item_id'][$i];
//            $count = ($sub_most_menu_item->save()) ? $count + 1 : '';

            if($sub_most_menu_item->save()){
                $count = $count+1;
                (isset($inputs['role_id'][$i + 1])) ? $sub_most_menu_item->roles()->sync($inputs['role_id'][$i + 1]) : $sub_most_menu_item->roles()->sync([]);
            }
        }
        // Set the flash message
        if ($count > 0)
            $this->setFlashMessage($count . ' Sub Menu Items has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/sub-most-menu-items');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $sub_most_menu_item = SubMostMenuItem::findOrFail($id);
        //Delete The Warder Record
        $delete = ($sub_most_menu_item !== null) ? $sub_most_menu_item->delete() : null;

        if ($delete) {
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! ' . $sub_most_menu_item->sub_menu_item . ' sub menu item have been deleted.', 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Get The Sub Menu Items Given a menu item id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSubMenuItem(Request $request)
    {
        $inputs = $request->all();
        return redirect('/sub-most-menu-items/index/' . $this->getHashIds()->encode($inputs['sub_menu_item_id']));
    }
}
