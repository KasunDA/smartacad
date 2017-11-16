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
     * Display a listing of the Permissions for Master Records.
     * @return Response
     */
    public function index()
    {
        $controllers = [];
        $temp = [];

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $object = new stdClass();
            $action = $route->getAction();

            if (array_key_exists('controller', $action)) {

                $method = explode('@', $action['controller'])[1];
                if ($method == 'missingMethod' || $method == 'getHashIds' || substr($method, 0, 4) == 'post') {
                    continue;
                }
                // You can also use explode('@', $action['controller']); here
                // to separate the class name from the method
                $pos = strpos($route->getUri(), '{');
                $uri = ($pos) ? substr($route->getUri(), 0, $pos) : $route->getUri();
                $name = substr(strrchr($action['controller'], '\\'), 1);

                if (in_array($uri, $temp)) {
                    continue;
                }

                $object->name = (!in_array($name, $temp)) ? $name : $name . ucfirst($uri);
                $object->uri = $uri;

                $temp[] = $uri;
                $temp[] = $name;
                $controllers[] = $object;
            }
        }
        
        $permissions = Permission::all();
        usort($controllers, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return view('admin.roles-permissions.permission', compact('permissions', 'controllers'));
    }

    /**
     * Insert or Update the menu items records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['permission_id']); $i++) {
            $permission = (Permission::find($inputs['permission_id'][$i]))
                ? Permission::find($inputs['permission_id'][$i])
                : new Permission();
            
            $permission->name = $inputs['name'][$i];
            $permission->uri = $inputs['uri'][$i];
            $permission->display_name = $inputs['display_name'][$i];
            $permission->description = $inputs['description'][$i];
            
            $count = ($permission->save()) ? $count+1 : '';
        }
        
        if($count > 0) $this->setFlashMessage($count . ' Permission has been successfully updated.', 1);
        
        return redirect('/permissions');
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @param $encodeId
     * @return Response
     */
    public function rolesPermissions($encodeId=null){
        $decodeId = ($encodeId == null) ? Role::DEFAULT_ROLE : $this->decode($encodeId);
        $role = Role::findorFail($decodeId);
        
        $roles = Role::orderBy('display_name')->pluck('display_name','role_id');
        $permissions = Permission::all();

        return view('admin.roles-permissions.roles-permissions', compact('roles','permissions', 'role'));
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @param Request $request
     * @return Response
     */
    public function saveRolesPermissions(Request $request){
        $inputs = $request->all();
        $role = Role::findorFail($inputs['role_id']);

        if ($role) {
            (isset($inputs['permission_id']))
                ? $role->perms()->sync($inputs['permission_id']) 
                : $role->perms()->sync([]);
            
            $this->setFlashMessage(' Permissions has been successfully modified to the role.', 1);
        }

        return redirect('/permissions/roles-permissions/' . $this->encode($inputs['role_id']));
    }

    /**
     * Get The Permissions Given a role id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function roles(Request $request)
    {
        $inputs = $request->all();
        
        return redirect('/permissions/roles-permissions/' . $this->encode($inputs['role_id']));
    }

}
