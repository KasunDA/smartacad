<?php

namespace App\Http\Controllers\Admin\MasterRecords\Items;

use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $items = Item::orderBy('name')->get();
        $item_types = ItemType::pluck('item_type', 'id')
            ->prepend('- Item Type -', '');

        if (count($item_types) == 1) {
            $this->setFlashMessage('Kindly Set up Item Types before proceeding to Items', 3);
            
            return redirect('/item-types');
        }
        
        return view('admin.master-records.items.items', compact('items', 'item_types'));
    }

    /**
     * Insert or Update the items records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['id']); $i++){
            $item = ($inputs['id'][$i] > 0) ? Item::find($inputs['id'][$i]) : new Item();
            $item->name = $inputs['name'][$i];
            $item->description = $inputs['description'][$i] ?: null;
            $item->status = $inputs['status'][$i];
            $item->item_type_id = $inputs['item_type_id'][$i];
            $count = ($item->save()) ? $count+1 : '';
        }
        
        if ($count > 0) $this->setFlashMessage($count . ' Item has been successfully updated.', 1);

        return redirect('/items');
    }

    /**
     * Delete a item from the list of items using a given id
     * @param $id
     */
    public function delete($id)
    {
        $item = Item::findOrFail($id);

        (!empty($item) && $item->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$item->item.' Item have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
