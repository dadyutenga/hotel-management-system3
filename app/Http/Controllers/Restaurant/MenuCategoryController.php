<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\StockLocation;
use App\Services\BuildingContext;
use App\Services\BuildingModuleGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuCategoryController extends Controller
{
    public function index(): View
    {
        $buildingId = BuildingContext::buildingId();

        $categories = MenuCategory::with('location')
            ->forBuilding($buildingId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])
            ->when($buildingId, fn ($q) => $q->where('building_id', $buildingId))
            ->get();

        return view('restaurant.menu.categories.index', compact('categories', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $buildingId = BuildingContext::buildingId();
        BuildingModuleGate::ensureRestaurant($buildingId);

        $locationRule = Rule::exists('stock_locations', 'id');
        if ($buildingId) {
            $locationRule->where('building_id', $buildingId);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'location_id' => ['required', 'uuid', $locationRule],
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        MenuCategory::create([
            'building_id' => $buildingId,
            'name' => $request->name,
            'location_id' => $request->location_id,
            'description' => $request->description,
            'sort_order' => (int) ($request->sort_order ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('general.restaurant.messages.category_created'));
    }

    public function update(Request $request, MenuCategory $menuCategory): RedirectResponse
    {
        $buildingId = BuildingContext::buildingId();
        BuildingContext::enforce($menuCategory->building_id);

        $locationRule = Rule::exists('stock_locations', 'id');
        if ($buildingId) {
            $locationRule->where('building_id', $buildingId);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'location_id' => ['required', 'uuid', $locationRule],
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $menuCategory->update([
            'name' => $request->name,
            'location_id' => $request->location_id,
            'description' => $request->description,
            'sort_order' => (int) ($request->sort_order ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('general.restaurant.messages.category_updated'));
    }

    public function destroy(MenuCategory $menuCategory): RedirectResponse
    {
        BuildingContext::enforce($menuCategory->building_id);

        $menuCategory->update(['is_active' => false]);
        $this->softDelete($menuCategory);

        return back()->with('success', __('general.restaurant.messages.category_deactivated'));
    }
}
