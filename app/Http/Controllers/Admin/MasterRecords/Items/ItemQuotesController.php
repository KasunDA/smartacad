<?php

namespace App\Http\Controllers\Admin\MasterRecords\Items;

use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemQuote;
use App\Models\Admin\Items\ItemType;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ItemQuotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param String $encodeId
     * @return Response
     */
    public function getIndex($encodeId=null)
    {
        $academic_year = (empty($encodeId)) ? AcademicYear::activeYear() : AcademicYear::findOrFail($this->getHashIds()->decode($encodeId)[0]);
        $item_quotes = (empty($academic_year)) ? ItemQuote::all() : $academic_year->itemQuotes()->get();
        
        $items = Item::lists('name', 'id')->prepend('- Items -', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('- Class Level -', '');
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');
        
        return view('admin.master-records.items.item-quotes',
            compact('items', 'item_quotes', 'classlevels', 'academic_year', 'academic_years')
        );
    }

    /**
     * Insert or Update the items quotes records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['id']); $i++){
            $item_quote = ($inputs['id'][$i] > 0) ? ItemQuote::find($inputs['id'][$i]) : new ItemQuote();
            $item_quote->item_id = $inputs['item_id'][$i];
            $item_quote->amount = $inputs['amount'][$i];
            $item_quote->classlevel_id = $inputs['classlevel_id'][$i];
            $item_quote->academic_year_id = $inputs['academic_year_id'][$i];
            $count = ($item_quote->save()) ? $count+1 : '';
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Item Quote has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/item-quotes');
    }

    /**
     * Delete a item from the list of items using a given id
     * @param $id
     */
    public function getDelete($id)
    {
        $item_quote = ItemQuote::findOrFail($id);
        //Delete The Warder Record
        $delete = ($item_quote !== null) ? $item_quote->delete() : null;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! '.$item_quote->item->name.' on quote '.$item_quote->amount.' Item Quote have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Get The Class Rooms Given the class level id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postAcademicYears(Request $request)
    {
        $inputs = $request->all();
        return redirect('/item-quotes/index/' . $this->getHashIds()->encode($inputs['academic_year_id']));
    }
}
