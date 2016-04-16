<?php

namespace App\Http\Controllers\Admin\RolesAndPermission;

use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    /**
     * Display a listing of the Roles for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $roles = Role::orderBy('name')->get();
        $user_types = UserType::orderBy('user_type')->lists('user_type', 'user_type_id')->toArray();;

        return view('admin.roles-permissions.roles', compact('roles', 'user_types'));
    }

    /**
     * Insert or Update the Roles items records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['role_id']); $i++){
            $role = ($inputs['role_id'][$i] > 0) ? Role::find($inputs['role_id'][$i]) : new Role();
            $role->name = $inputs['name'][$i];
            $role->display_name = $inputs['display_name'][$i];
            $role->description = $inputs['description'][$i];
            $role->user_type_id = $inputs['user_type_id'][$i];
            $role->save();
            $count++;
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Roles has been successfully updated.', 1);
        // redirect to the modify a new user role page
        return redirect('/roles');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu_header id
     * @param $id
     */
    public function getDelete($id)
    {
        $roles = Role::findOrFail($id);
        //Delete The Warder Record
        $delete = ($roles !== null) ? $roles->delete() : null;

        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$roles->menu_header.' role have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Display a listing of the Users and Roles for Master Records.
     * @param $encodeId
     * @return Response
     */
    public function getUsersRoles($encodeId=null){
        $decodeId = ($encodeId === null) ? Role::DEFAULT_ROLE : $this->getHashIds()->decode($encodeId)[0];
        $role = Role::findorFail($decodeId);

        $roles = Role::orderBy('display_name')->lists('display_name', 'role_id');
        $users = ($encodeId === null) ? User::orderBy('email')->get() : $users = $role->users()->orderBy('email')->get();;
        return view('admin.roles-permissions.users', compact('users', 'roles', 'role', 'encodeId'));
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @param Request $request
     * @return Response
     */
    public function postUsersRoles(Request $request){
        $inputs = $request->all();
        for($i = 0; $i < count($inputs['user_id']); $i++){
            $user = ($inputs['user_id'][$i] > 0) ? User::find($inputs['user_id'][$i]) : null;

            (isset($inputs['role_id']['role'.$inputs['user_id'][$i]]))
                ? $user->roles()->sync($inputs['role_id']['role'.$inputs['user_id'][$i]]) : $user->roles()->sync([]);
        }

        // Set the flash message
        $this->setFlashMessage(' User Roles has been successfully added Modified.', 1);

        return redirect($request->fullUrl());
    }

    /**
     * Get The Users Given a role id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRoles(Request $request)
    {
        $inputs = $request->all();
        return redirect('/roles/users-roles/' . $this->getHashIds()->encode($inputs['role_id']));
    }
}
