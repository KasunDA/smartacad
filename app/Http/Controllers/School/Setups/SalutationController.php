<?php

namespace App\Http\Controllers\School\Setups;

use App\Models\School\Setups\Salutation;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SalutationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $salutations = Salutation::all();
        
        return view('school.setups.salutations', compact('salutations'));
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

        for($i = 0; $i < count($inputs['salutation_id']); $i++){
            $salutation = ($inputs['salutation_id'][$i] > 0) 
                ? Salutation::find($inputs['salutation_id'][$i]) 
                : new Salutation();
            $salutation->salutation = $inputs['salutation'][$i];
            $salutation->salutation_abbr = $inputs['salutation_abbr'][$i];
            
            $count = ($salutation->save()) ? $count+1 : '';
        }
        if($count > 0) $this->setFlashMessage($count . ' User Type has been successfully updated.', 1);
        
        return redirect('/salutations');
    }

    /**
     * Delete a User type from the list of user Types using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $salutations = Salutation::findOrFail($id);

        ($salutations->delete())
            ? $this->setFlashMessage(
                'Deleted!!! ' . $salutations->salutation . ' Salutation have been deleted.', 1
            )
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
