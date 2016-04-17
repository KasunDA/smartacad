<?php

namespace App\Http\Controllers\School;

use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SchoolController extends Controller
{
    public function getIndex(){
        $schools= School::connection('admin_mysql')->all();
        return view('school.index', compact('schools'));
    }

    public function getCreate(){
        return view('school.create');
    }

    public function postCreate(Request $request){
        $data = $request->all();
        dd($data);
    }

    public function getEdit(){
        return view('school.edit');
    }
}
