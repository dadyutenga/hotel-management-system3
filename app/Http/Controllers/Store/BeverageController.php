<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Beverage;
use App\Models\BeverageCategory;
use App\Models\BeverageInventory;
use App\Services\BuildingContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeverageController extends Controller
{
    public function index(Request $request)
    {
        $beverages = Beverage::with('category', 'inventory')
            ->forUserBuilding()
            ->active()
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->barcode, fn ($q) => $q->where('barcode', $request->barcode))
            ->latest()
            ->paginate(20);

        $categories = BeverageCategory::forUserBuilding()->active()->orderBy('name')->get();

        return view('store.beverages.index', compact('beverages', 'categories'));
    }

    public function create()
    {
        $categories = BeverageCategory::forUserBuilding()->active()->orderBy('name')->get();

        return view('store.beverages.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string|max:100|unique:beverages,barcode',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|uuid|exists:beverage_categories,id',
            'unit' => 'required|in:bottle,can,crate,pack',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $data['is_active'] = true;
            $data['building_id'] = BuildingContext::buildingId();

            if (! empty($data['category_id'])) {
                $category = BeverageCategory::findOrFail($data['category_id']);
                BuildingContext::enforce($category->building_id);
            }

            if (isset($data['image'])) {
                $path = $data['image']->store('beverages', 'public');
                $data['image'] = $path;
            }

            $beverage = Beverage::create($data);

            BeverageInventory::create([
                'beverage_id' => $beverage->id,
                'quantity_on_hand' => 0,
            ]);
        });

        return redirect()->route('store.beverages.index')
            ->with('success', 'Beverage registered successfully.');
    }

    public function show(Beverage $beverage)
    {
        BuildingContext::enforce($beverage->building_id);

        $beverage->load('category', 'inventory', 'creator', 'stockMovements.performer');

        return view('store.beverages.show', compact('beverage'));
    }

    public function edit(Beverage $beverage)
    {
        BuildingContext::enforce($beverage->building_id);

        $categories = BeverageCategory::forUserBuilding()->active()->orderBy('name')->get();

        return view('store.beverages.edit', compact('beverage', 'categories'));
    }

    public function update(Request $request, Beverage $beverage)
    {
        BuildingContext::enforce($beverage->building_id);

        $data = $request->validate([
            'barcode' => 'required|string|max:100|unique:beverages,barcode,'.$beverage->id,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|uuid|exists:beverage_categories,id',
            'unit' => 'required|in:bottle,can,crate,pack',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if (! empty($data['category_id'])) {
            $category = BeverageCategory::findOrFail($data['category_id']);
            BuildingContext::enforce($category->building_id);
        }

        if (isset($data['image'])) {
            $path = $data['image']->store('beverages', 'public');
            $data['image'] = $path;
        } else {
            unset($data['image']);
        }

        $beverage->update($data);

        return redirect()->route('store.beverages.show', $beverage)
            ->with('success', 'Beverage updated successfully.');
    }

    public function destroy(Beverage $beverage)
    {
        BuildingContext::enforce($beverage->building_id);

        $beverage->update(['is_active' => false]);
        $this->softDelete($beverage);

        return redirect()->route('store.beverages.index')
            ->with('success', 'Beverage deleted.');
    }

    public function manageCategories()
    {
        $categories = BeverageCategory::forUserBuilding()->orderBy('name')->withCount('beverages')->get();

        return view('store.beverages.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:beverage_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        $data['building_id'] = BuildingContext::buildingId();

        BeverageCategory::create($data);

        return redirect()->route('store.beverages.categories')
            ->with('success', 'Category created.');
    }
}
