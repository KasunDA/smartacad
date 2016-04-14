<?php

namespace App\Http\Controllers\Admin\RolesAndPermission;

use App\Models\Admin\RolesAndPermissions\Permission;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use stdClass;

class PermissionsController extends Controller
{
    /**
     * Make sure the user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @return Response
     */
    public function getIndex()
    {
        $controllers = [];
        $temp = [];

        foreach (Route::getRoutes()->getRoutes() as $route)
        {
            $object = new stdClass();
            $action = $route->getAction();

            if (array_key_exists('controller', $action))
            {

                $method = explode('@', $action['controller'])[1];
                if($method === 'missingMethod' || $method === 'getHashIds' || substr($method, 0, 4) === 'post')
                    continue;
                // You can also use explode('@', $action['controller']); here
                // to separate the class name from the method

                $pos = strpos($route->getUri(), '{');
                $uri = ($pos) ? substr($route->getUri(), 0, $pos) : $route->getUri();
                $name = substr(strrchr($action['controller'], '\\'), 1);

                if(in_array($uri, $temp))
                    continue;

                $object->name = (!in_array($name, $temp)) ? $name : $name . ucfirst($uri);
                $object->uri = $uri;

                $temp[] = $uri;
                $temp[] = $name;

                $controllers[] = $object;
            }
        }
        $permissions = Permission::all();
        usort($controllers, function($a, $b)
        {
            return strcmp($a->name, $b->name);
        });

        return view('admin.roles-permissions.permission', compact('permissions', 'controllers'));
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

        for($i = 0; $i < count($inputs['permission_id']); $i++){
            $permission = (Permission::find($inputs['permission_id'][$i])) ? Permission::find($inputs['permission_id'][$i]) : new Permission();
            $permission->name = $inputs['name'][$i];
            $permission->uri = $inputs['uri'][$i];
            $permission->display_name = $inputs['display_name'][$i];
            $permission->description = $inputs['description'][$i];
            $count = ($permission->save()) ? $count+1 : '';
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Permission has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/permissions');
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @param $encodeId
     * @return Response
     */
    public function getRolesPermissions($encodeId=null){
        $decodeId = ($encodeId === null) ? Role::DEFAULT_ROLE : $this->getHashIds()->decode($encodeId)[0];
        $role = Role::findorFail($decodeId);

        $roles = Role::orderBy('display_name')->lists('display_name','role_id');

        $permissions = Permission::all();

        return view('admin.roles-permissions.roles-permissions', compact('roles','permissions', 'role'));
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @param Request $request
     * @return Response
     */
    public function postRolesPermissions(Request $request){
        $inputs = $request->all();
        $role = Role::findorFail($inputs['role_id']);

        if($role){
            (isset($inputs['permission_id']))
                ? $role->perms()->sync($inputs['permission_id']) : $role->perms()->sync([]);
            // Set the flash message
            $this->setFlashMessage(' Permissions has been successfully modified to the role.', 1);
        }

        return redirect('/permissions/roles-permissions/' . $this->getHashIds()->encode($inputs['role_id']));
    }

    /**
     * Get The Permissions Given a role id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRoles(Request $request)
    {
        $inputs = $request->all();
        return redirect('/permissions/roles-permissions/' . $this->getHashIds()->encode($inputs['role_id']));
    }

}
