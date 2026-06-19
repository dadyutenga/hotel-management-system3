<?php

namespace App\Http\Controllers;

use App\Models\LaundryItem;

class LaundryItemController extends Controller
{
    public function archived()
    {
        $records = LaundryItem::onlyDeleted()->latest('deleted_at')->paginate(20);

        return view('laundry-items.archived', compact('records'));
    }

    public function restore(LaundryItem $laundryItem)
    {
        $this->restoreModel($laundryItem);

        return redirect()->back()->with('success', 'Laundry item restored successfully.');
    }
}
