<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SponsorController extends Controller
{
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $sponsors = User::where('user_type_id', Sponsor::USER_TYPE)->get();
        dd($sponsors);
        return view('admin.accounts.sponsors.index', compact('sponsors'));
    }
}
