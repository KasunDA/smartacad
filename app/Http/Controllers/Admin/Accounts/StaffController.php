<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $staffs = User::where('user_type_id', Staff::USER_TYPE)->get();
        dd($staffs);
        return view('admin.accounts.staffs.index', compact('staffs'));
    }
}
