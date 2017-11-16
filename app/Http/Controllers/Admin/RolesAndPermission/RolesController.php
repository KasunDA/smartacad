<?php

namespace App\Http\Controllers\Admin\RolesAndPermission;

use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\RolesAndPermissions\RoleUser;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    /**
     * Display a listing of the Roles for Master Records.
     *
     * @return Response
     */
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $user_types = UserType::orderBy('user_type')
            ->pluck('user_type', 'user_type_id')
            ->toArray();

        return view('admin.roles-permissions.roles', compact('roles', 'user_types'));
    }

    /**
     * Insert or Update the Roles items records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['role_id']); $i++) {
            $role = ($inputs['role_id'][$i] > 0) ? Role::find($inputs['role_id'][$i]) : new Role();
            $role->name = $inputs['name'][$i];
            $role->display_name = $inputs['display_name'][$i];
            $role->description = $inputs['description'][$i];
            $role->user_type_id = $inputs['user_type_id'][$i];
            $role->save();
            $count++;
        }
        if ($count > 0) $this->setFlashMessage($count . ' Roles has been successfully updated.', 1);
        
        return redirect('/roles');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu_header id
     * @param $id
     */
    public function delete($id)
    {
        $roles = Role::findOrFail($id);

        (($roles !== null) && $roles->delete())
            ? $this->setFlashMessage('  Deleted!!! ' . $roles->role . ' role have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Display a listing of the Users using Ajax Datatable.
     * @return Response
     */
    public function data()
    {
        $iTotalRecords = User::orderBy('first_name')
            ->whereIn('user_type_id',
                UserType::where('type', 2)
                    ->get(['user_type_id'])
                    ->toArray()
            )
            ->count();

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $q = @$_REQUEST['sSearch'];
        $role_id = @$_REQUEST['search']['role_id'];
        $user_type_id = @$_REQUEST['search']['user_type_id'];

        $users = User::orderBy('first_name')
            ->where(
                function ($query) use ($q, $role_id, $user_type_id) {
                    if (!Auth::user()->hasRole('developer')) {
                        $query->whereIn('user_type_id',
                            UserType::where('type', 2)
                                ->get(['user_type_id'])
                                ->toArray()
                        );
                    }
                    //Filter by either email, name or phone number
                    if (!empty($q)){
                        $query->orWhere('first_name', 'like', '%'.$q.'%')
                            ->orWhere('last_name', 'like', '%'.$q.'%')
                            ->orWhere('email', 'like', '%'.$q.'%')
                            ->orWhere('phone_no', 'like', '%'.$q.'%');
                    }
                    //Filter by Role
                    if (!empty($role_id)) {
                        $query->whereIn('user_id',
                            RoleUser::where('role_id', $role_id)
                                ->get(['user_id'])
                                ->toArray()
                        );
                    }
                    //Filter by User Type
                    if (!empty($user_type_id)) {
                        $query->where('user_type_id', $user_type_id);
                    }
                }
            );

        $iTotalDisplayRecords = $users->count();
        $records["data"] = $records = [];

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (Auth::user()->hasRole('developer')) {
            $roles = Role::orderBy('display_name')->get();
        } else {
            $roles = Role::whereIn('user_type_id',
                    UserType::where('type', 2)
                        ->get(['user_type_id'])
                        ->toArray()
                )
                ->get();
        }

        $i = $iDisplayStart;
        $allUsers = $users->skip($iDisplayStart)->take($iDisplayLength)->get();

        foreach ($allUsers as $user) {
            $select = '';

            foreach ($roles as $ro) {
                $selected = ($user->roles()
                    && in_array($ro->role_id,$user->roles()->get()->pluck('role_id')->toArray())
                )
                    ? 'selected' : '';
                $select .= '<option ' . $selected . ' value="' . $ro->role_id . '">' . $ro->display_name . '</option>';
            }
            $role = '<select class="form-control selectpicker" multiple name="role_id[role'.$user->user_id.'][]">
                        '.$select.'
                    </select>
                    <input name="user_id[]" type="hidden" value="'.$user->user_id.'">';

            $records["data"][] = [
                ($i++ + 1),
                $user->fullNames(),
                $user->email,
                ($user->gender) ? $user->gender : '<span class="label label-danger">nil</span>',
                $user->userType()->first()->user_type,
                $role,
                '<a target="_blank" href="/users/view/' . $this->encode($user->user_id)
                        . '" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span>
                 </a>',
                '<a target="_blank" href="/users/edit/' . $this->encode($user->user_id)
                        . '" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-edit"></span>
                 </a>',
            ];
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = isset($iTotalDisplayRecords) ? $iTotalDisplayRecords :$iTotalRecords;

        echo json_encode($records);
    }

    /**
     * Display a listing of the Users and Roles for Master Records.
     * @return Response
     */
    public function users()
    {
        if (Auth::user()->hasRole('developer')) {
            $roles = Role::orderBy('display_name')
                ->pluck('display_name', 'role_id')
                ->prepend('- Select Role -', '');
            $user_types = UserType::orderBy('user_type')
                ->pluck('user_type', 'user_type_id')
                ->prepend('- User Type -', '');
        } else {
            $user_types = UserType::orderBy('user_type')
                ->where('type', 2)
                ->pluck('user_type', 'user_type_id')
                ->prepend('- User Type -', '');
            $roles = Role::whereIn('user_type_id',
                    UserType::where('type', 2)
                        ->get(['user_type_id'])
                        ->toArray()
                )
                ->pluck('display_name', 'role_id')
                ->prepend('- Select Role -', '');
        }
        
        return view('admin.roles-permissions.users', compact('roles', 'user_types'));
    }

    /**
     * Display a listing of the Permissions for Master Records.
     * @param Request $request
     * @return Response
     */
    public function saveUsers(Request $request)
    {
        $inputs = $request->all();
        for ($i = 0; $i < count($inputs['user_id']); $i++) {
            $user = ($inputs['user_id'][$i] > 0) ? User::find($inputs['user_id'][$i]) : null;

            if (isset($inputs['role_id']['role' . $inputs['user_id'][$i]])) {
                $user->roles()->sync($inputs['role_id']['role' . $inputs['user_id'][$i]]);
            }
        }

        $this->setFlashMessage(' User Roles has been successfully added Modified.', 1);

        return redirect($request->fullUrl());
    }
}
