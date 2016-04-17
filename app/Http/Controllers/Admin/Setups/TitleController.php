<?php

namespace App\Http\Controllers\Admin\Setups;

use App\Models\Admin\Setups\Title;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $titles = Title::all();
        return view('admin.setups.titles', compact('titles'));
    }

    /**
     * Insert or Update the user type records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['title_id']); $i++){
            $titles = ($inputs['title_id'][$i] > 0) ? Title::find($inputs['title_id'][$i]) : new Title();
            $titles->title = $inputs['title'][$i];
            $titles->title_abbr = $inputs['title_abbr'][$i];
            $count = ($titles->save()) ? $count+1 : '';
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' User Type has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/titles');
    }

    /**
     * Delete a User type from the list of user Types using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $titles = Title::findOrFail($id);
        //Delete The Warder Record
        $delete = ($titles !== null) ? $titles->delete() : null;

        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$titles->title.' Title have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
