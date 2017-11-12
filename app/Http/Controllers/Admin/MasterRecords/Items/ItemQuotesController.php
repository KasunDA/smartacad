<?php

namespace App\Http\Controllers\Admin\MasterRecords\Items;

use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemQuote;
use App\Models\Admin\Items\ItemType;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ItemQuotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Boolean $year_id
     * @param Boolean $item_id
     * @return Response
     */
    public function index($year_id = false, $item_id = false)
    {
        $items = Item::orderBy('name')
            ->pluck('name', 'id')
            ->prepend('- Items -', '');

        if (count($items) == 1) {
            $this->setFlashMessage('Kindly Set up Items before proceeding to Item Quotes ', 3);
            
            return redirect('/items');
        }

        $academic_year = ($year_id)
            ? $academic_year = AcademicYear::findOrFail($this->decode($year_id))
            : AcademicYear::activeYear();

        $item_quotes = $academic_year->itemQuotes()->get();
        if ($year_id) {
            $item = Item::find($this->decode($item_id));
            $item_quotes = ($item_id)
                ? $academic_year->itemQuotes()->where('item_id', $this->decode($item_id))->get()
                : $item_quotes;
        }

//        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')->prepend('- Class Level -', '');
        $classgroups = ClassGroup::pluck('classgroup', 'classgroup_id')
            ->prepend('- Class Group -', '');
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id');
        
        return view('admin.master-records.items.item-quotes',
            compact('items', 'item_quotes', 'classgroups', 'academic_year', 'item', 'academic_years')
        );
    }

    /**
     * Insert or Update the items quotes records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['id']); $i++) {
            $item_quote = ($inputs['id'][$i] > 0) ? ItemQuote::find($inputs['id'][$i]) : new ItemQuote();
            $item_quote->item_id = $inputs['item_id'][$i];
            $item_quote->amount = $inputs['amount'][$i];
            $item_quote->classgroup_id = $inputs['classgroup_id'][$i];
            $item_quote->academic_year_id = $inputs['academic_year_id'][$i];
            $count = ($item_quote->save()) ? $count + 1 : $count;
        }
        
        if ($count > 0) $this->setFlashMessage($count . ' Item Quote has been successfully updated.', 1);
        
        return redirect()->back();
    }
    
    /**
     * Delete a item from the list of items using a given id
     * @param $id
     */
    public function delete($id)
    {
        $item_quote = ItemQuote::findOrFail($id);
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
    public function academicYears(Request $request)
    {
        $inputs = $request->all();
        $year = $this->encode($inputs['academic_year_id']);
        $item = $this->encode($inputs['item_id']);

        return redirect('/item-quotes/' . $year . '/' . $item);
    }
}
