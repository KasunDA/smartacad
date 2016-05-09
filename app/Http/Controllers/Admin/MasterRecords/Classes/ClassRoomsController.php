<?php

namespace App\Http\Controllers\Admin\MasterRecords\Classes;

use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClassRoomsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function getIndex($encodeId=null)
    {
        $classlevel = ($encodeId === null) ? null : ClassLevel::findOrFail($this->getHashIds()->decode($encodeId)[0]);
        $classrooms = ($classlevel === null) ? ClassRoom::all() : $classlevel->classRooms()->get();
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('All Class Level', '');
        return view('admin.master-records.classes.class-rooms', compact('classlevels', 'classrooms', 'classlevel'));
    }


    /**
     * Insert or Update the class room records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

       for($i = 0; $i < count($inputs['classroom_id']); $i++){
            $classroom = ($inputs['classroom_id'][$i] > 0) ? ClassRoom::find($inputs['classroom_id'][$i]) : new ClassRoom();
            $classroom->classroom = $inputs['classroom'][$i];
            $classroom->class_size = ($inputs['class_size'][$i] != '') ? $inputs['class_size'][$i] : null;
            $classroom->classlevel_id = $inputs['classlevel_id'][$i];
            if($classroom->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/class-rooms');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $classroom = ClassRoom::findOrFail($id);
        //Delete The Record
        $delete = ($classroom !== null) ? $classroom->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$classroom->classroom.' Class Room have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Get The Class Rooms Given the class level id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLevels(Request $request)
    {
        $inputs = $request->all();
        return redirect('/class-rooms/index/' . $this->getHashIds()->encode($inputs['classlevel_id']));
    }
}
