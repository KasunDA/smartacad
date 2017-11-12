<?php

namespace App\Http\Controllers\Admin\MasterRecords\Items;

use App\Models\Admin\Items\ItemType;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ItemTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $item_types = ItemType::all();

        return view('admin.master-records.items.item-types', compact('item_types'));
    }

    /**
     * Insert or Update the item type records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['id']); $i++) {
            $item_type = ($inputs['id'][$i] > 0) ? ItemType::find($inputs['id'][$i]) : new ItemType();
            $item_type->item_type = $inputs['item_type'][$i];
            $count = ($item_type->save()) ? $count + 1 : $count;
        }

        if ($count > 0) $this->setFlashMessage("{$count} Item Type has been successfully updated.", 1);

        return redirect('/item-types');
    }

    /**
     * Delete a item type from the list of item Types using a given id
     * @param $id
     */
    public function delete($id)
    {
        $item_type = ItemType::findOrFail($id);

        (!empty($item_type) && $item_type->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$item_type->item_type.' Item Type have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
