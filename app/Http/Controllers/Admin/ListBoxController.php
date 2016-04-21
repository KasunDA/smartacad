<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\School\Setups\Lga;

class ListBoxController extends Controller
{
    /**
     * Get the local government areas based on the state id
     * @param int $id
     * @return Response
     */
    public function lga($id)
    {
        $lgas = Lga::where('state_id', $id)->orderBy('lga')->get();

        return view('admin.partials.lga')->with([
            'lgas' => $lgas
        ]);
    }
}
