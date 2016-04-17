<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Setups\Title;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SponsorController extends Controller
{
    public function getIndex(){
        $guardians =  [];
        return view('admin.accounts.sponsors.index', compact('guardians'));
    }

    public function getCreate(){
        $titles = Title::all();
        return view('admin.accounts.sponsors.create', compact('titles'));
    }

    public function postCreate(Request $request){
        $data = $request->all();
        dd($data);
    }
}
