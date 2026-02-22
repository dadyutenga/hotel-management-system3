<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemIngredient;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    /**
     * GET /restaurant/menu
     */
    public function index(Request $request): View
    {
        $categories = MenuCategory::with(['menuItems' => function ($q) {
            $q->where('is_active', true)->with('ingredients.product');
        }, 'location'])
            ->where('is_active', true)
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->get();

        return view('restaurant.menu.items.index', compact('categories'));
    }

    /**
     * GET /restaurant/menu/create
     */
    public function create(): View
    {
        $categories = MenuCategory::with('location')->where('is_active', true)->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();

        return view('restaurant.menu.items.create', compact('categories', 'products'));
    }

    /**
     * POST /restaurant/menu
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id'              => 'required|uuid|exists:menu_categories,id',
            'name'                     => 'required|string|max:150',
            'description'              => 'nullable|string',
            'selling_price'            => 'required|numeric|min:0.01',
            'ingredients'              => 'nullable|array',
            'ingredients.*.product_id' => 'required_with:ingredients|uuid|exists:products,id',
            'ingredients.*.quantity'   => 'required_with:ingredients|numeric|min:0.0001',
            'ingredients.*.unit'       => 'required_with:ingredients|string|max:30',
        ]);

        DB::transaction(function () use ($data) {
            $item = MenuItem::create([
                'category_id'   => $data['category_id'],
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'selling_price' => $data['selling_price'],
                'is_available'  => true,
                'is_active'     => true,
                'created_by'    => auth()->id(),
            ]);

            if (!empty($data['ingredients'])) {
                foreach ($data['ingredients'] as $ing) {
                    MenuItemIngredient::create([
                        'menu_item_id' => $item->id,
                        'product_id'   => $ing['product_id'],
                        'quantity'     => $ing['quantity'],
                        'unit'         => $ing['unit'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('restaurant.menu.index')
            ->with('success', "Menu item '{$data['name']}' created.");
    }

    /**
     * GET /restaurant/menu/{menuItem}/edit
     */
    public function edit(MenuItem $menuItem): View
    {
        $categories = MenuCategory::with('location')->where('is_active', true)->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $menuItem->load('ingredients.product');

        return view('restaurant.menu.items.edit', compact('menuItem', 'categories', 'products'));
    }

    /**
     * PUT /restaurant/menu/{menuItem}
     */
    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validate([
            'name'                     => 'sometimes|string|max:150',
            'description'              => 'sometimes|nullable|string',
            'selling_price'            => 'sometimes|numeric|min:0.01',
            'is_available'             => 'sometimes|boolean',
            'ingredients'              => 'nullable|array',
            'ingredients.*.product_id' => 'required_with:ingredients|uuid|exists:products,id',
            'ingredients.*.quantity'   => 'required_with:ingredients|numeric|min:0.0001',
            'ingredients.*.unit'       => 'required_with:ingredients|string|max:30',
        ]);

        DB::transaction(function () use ($data, $menuItem) {
            $menuItem->update([
                'name'          => $data['name']          ?? $menuItem->name,
                'description'   => $data['description']   ?? $menuItem->description,
                'selling_price' => $data['selling_price'] ?? $menuItem->selling_price,
                'is_available'  => $data['is_available']  ?? $menuItem->is_available,
            ]);

            // Replace ingredients entirely if provided
            if (isset($data['ingredients'])) {
                $menuItem->ingredients()->delete();
                foreach ($data['ingredients'] as $ing) {
                    MenuItemIngredient::create([
                        'menu_item_id' => $menuItem->id,
                        'product_id'   => $ing['product_id'],
                        'quantity'     => $ing['quantity'],
                        'unit'         => $ing['unit'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('restaurant.menu.index')
            ->with('success', 'Menu item updated.');
    }

    /**
     * DELETE /restaurant/menu/{menuItem}
     */
    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['is_active' => false]);

        return redirect()
            ->route('restaurant.menu.index')
            ->with('success', 'Menu item removed from menu.');
    }
}
