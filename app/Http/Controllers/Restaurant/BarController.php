<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\BarTicket;
use App\Models\Order;
use App\Services\BuildingContext;
use App\Services\BuildingModuleGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarController extends Controller
{
    public function queue(Request $request): View
    {
        $buildingId = BuildingContext::buildingId();
        BuildingModuleGate::ensureBar($buildingId);

        $tickets = BarTicket::with(['order', 'table'])
            ->forUserBuilding()
            ->whereIn('status', ['pending', 'preparing'])
            ->latest()
            ->get();

        return view('restaurant.bar.queue', compact('tickets'));
    }

    public function tabs(Request $request): View
    {
        $buildingId = BuildingContext::buildingId();
        BuildingModuleGate::ensureBar($buildingId);

        $tabs = Order::with(['items.menuItem', 'table'])
            ->forUserBuilding()
            ->where('order_type', 'bar_tab')
            ->whereIn('status', ['open', 'sent', 'ready'])
            ->latest()
            ->get();

        return view('restaurant.bar.tabs', compact('tabs'));
    }

    public function markPreparing(BarTicket $ticket): RedirectResponse
    {
        $buildingId = BuildingContext::buildingId();
        BuildingModuleGate::ensureBar($buildingId);
        BuildingContext::enforce($ticket->building_id);

        $ticket->markPreparing();

        return redirect()->route('restaurant.bar.queue')
            ->with('success', 'Ticket marked as preparing.');
    }

    public function markReady(BarTicket $ticket): RedirectResponse
    {
        $buildingId = BuildingContext::buildingId();
        BuildingModuleGate::ensureBar($buildingId);
        BuildingContext::enforce($ticket->building_id);

        $ticket->markReady();

        return redirect()->route('restaurant.bar.queue')
            ->with('success', 'Ticket marked as ready.');
    }
}
