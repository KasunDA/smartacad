<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Staff;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $sponsors_count = User::where('user_type_id', Sponsor::USER_TYPE)->count();
        $staff_count = User::where('user_type_id', Staff::USER_TYPE)->count();
        return view('admin.dashboard', compact('sponsors_count','staff_count'));
    }
}
