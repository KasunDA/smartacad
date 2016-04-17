<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Admin\Accounts\Guardian;
use App\Models\Admin\Setups\Title;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GuardianController extends Controller
{
    public function getIndex(){
        $guardians =  [];
        return view('admin.accounts.guardians.index', compact('guardians'));
    }

    public function getCreate(){
        $titles = Title::all();
        return view('admin.accounts.guardians.create', compact('titles'));
    }

    public function postCreate(Request $request){
        $data = $request->all();
        dd($data);
    }
}
