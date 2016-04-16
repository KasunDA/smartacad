<?php

namespace App\Http\Controllers\Admin\Menus;

use App\Models\Admin\Menus\MenuHeader;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class MenuHeaderController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $menu_headers = MenuHeader::orderBy('sequence')->get();
        $roles = Role::orderBy('name')->get();
        return view('admin.menus.menu-header', compact('menu_headers','roles'));
    }


    /**
     * Insert or Update the menu_header records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['menu_header_id']); $i++){
            $menu_header = ($inputs['menu_header_id'][$i] > 0) ? MenuHeader::find($inputs['menu_header_id'][$i]) : new MenuHeader();
            $menu_header->menu_header = Str::upper($inputs['menu_header'][$i]);
            $menu_header->active = $inputs['active'][$i];
            $menu_header->type = $inputs['type'][$i];
            $menu_header->sequence = $inputs['sequence'][$i];
            if($menu_header->save()){
                $count = $count+1;
                (isset($inputs['role_id'][$i + 1])) ? $menu_header->roles()->sync($inputs['role_id'][$i + 1]) : $menu_header->roles()->sync([]);
            }
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Menu headers has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/menu-headers');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu_header id
     * @param $id
     */
    public function getDelete($id)
    {
        $menu_header = MenuHeader::findOrFail($id);
        //Delete The Warder Record
        $delete = ($menu_header !== null) ? $menu_header->delete() : null;

        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$menu_header->menu_header.' menu_header have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

}
