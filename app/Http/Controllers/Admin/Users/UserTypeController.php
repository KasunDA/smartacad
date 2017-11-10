<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Admin\Users\UserType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user_types = UserType::all();

        return view('admin.users.user-types', compact('user_types'));
    }

    /**
     * Insert or Update the user type records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['user_type_id']); $i++){
            $user_type = ($inputs['user_type_id'][$i] > 0)
                ? UserType::find($inputs['user_type_id'][$i])
                : new UserType();
            $user_type->user_type = $inputs['user_type'][$i];
            $user_type->type = $inputs['type'][$i];

            $count = ($user_type->save()) ? $count+1 : '';
        }

        if($count > 0) $this->setFlashMessage($count . ' User Type has been successfully updated.', 1);

        return redirect('/user-types');
    }

    /**
     * Delete a User type from the list of user Types using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $user_type = UserType::findOrFail($id);

        ($user_type->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$user_type->user_type.' User Type have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
