<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Staff;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $sponsors_count = Sponsor::count();
        $staff_count = Staff::count();
        return view('admin.dashboard', compact('sponsors_count','staff_count'));
    }
}
