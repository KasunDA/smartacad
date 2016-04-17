<?php

namespace App\Http\Controllers\School;

use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'name.required' => 'School Short Name is Required!',
            'full_name.required' => 'School Complete Name is Required!',
            'address.required' => 'School Address is Required!',
            'logo.mimes' => 'School Logo must be either jpeg, jpg, bmp or png!',
            'logo.max' => 'School Logo must be a less than 200KB!',
        ];
        return Validator::make($data, [
            'name' => 'required',
            'full_name' => 'required',
            'address' => 'required',
            'logo' => 'mimes:jpeg,bmp,png,jpg|max:200',
        ], $messages);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function getIndex(){
        $schools= School::all();
        return view('school.index', compact('schools'));
    }

    /**
     * Display form for creating a new school
     * @return Response
     */
    public function getCreate(){
        return view('school.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request  $request
     * @return Response
     */
    public function postCreate(Request $request)
    {
        $inputs = $request->all();
        $inputs['address'] = trim($inputs['address']);
        //Validate Request Inputs
        if ($this->validator($inputs)->fails())
        {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/schools/create')->withErrors($this->validator($inputs))->withInput();
        }

        // Store the School...
        $school = School::create($inputs);

        if($school->schools_id) {
            if ($request->file('logo')) {
                $file = $request->file('logo');
                $filename = $file->getClientOriginalName();
                $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                $school->logo = $school->schools_id . '_logo.' . $img_ext;
                Input::file('logo')->move($school->logo_path, $school->logo);
                $school->save();
            }
            // Set the flash message
            $this->setFlashMessage($school->name . ' has been successfully created.', 1);
        }else
            $this->setFlashMessage('Error!!! Unable to save the record.', 2);
        // redirect to the list of schools page
        return redirect('/schools');
    }

    /**
     * Activate or Deactivate a School. Activate : 1, Deactivate : 0
     * @param  int  $school_id
     * @param  int  $status
     * @return Response
     */
    public function getStatus($school_id, $status)
    {
        $school = School::findOrFail($school_id);
        if($school !== null) {
            $school->status_id = $status;
            //Save The Project
            $school->save();
            ($status === '1')
                ? $this->setFlashMessage(' Activated!!! '.$school->full_name.' have been activated.', 1)
                : $this->setFlashMessage(' Deactivated!!! '.$school->full_name.' have been deactivated.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to perform task try again.', 2);
        }
    }

    /**
     * Show the form for editing a new resource.
     * @param String $encodeId
     * @return Response
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $school = (empty($decodeId)) ? abort(403) : School::findOrFail($decodeId[0]);
        return view('school.edit', compact('school'));
    }

    /**
     * Store the form for modifying an existing resource.
     * @param  Request $request
     * @param  String $encodeId
     * @return Response
     */
    public function patchEdit(Request $request, $encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $school = (empty($decodeId)) ? abort(403) : School::findOrFail($decodeId[0]);
        $inputs = $request->all();
        //Validate Request Inputs
        $validator = $this->validator($inputs);

        if ($validator->fails()) {
            $this->setFlashMessage('  Error!!! You have error(s) while filling the form.', 2);
            return redirect('/schools/edit/' . $encodeId)->withErrors($validator)->withInput();
        }
        // Update the School...
        if ($school->update($inputs)) {
            if ($request->file('logo')) {
                $file = $request->file('logo');
                $filename = $file->getClientOriginalName();
                $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                $school->logo = $school->schools_id . '_logo.' . $img_ext;
                Input::file('logo')->move($school->logo_path, $school->logo);
                $school->save();
            }
            // Set the flash message
            $this->setFlashMessage($school->name . ' have successfully been updated.', 1);
            // redirect to the create bill page and enable the take roll call link
        }
        return redirect('/schools');
    }
}
