<?php
namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget {
    protected function getStats(): array {
        $totalRooms = Room::where('is_active', true)->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->where('is_active', true)->count();
        $reservedRooms = Room::where('status', 'reserved')->count();
        
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        
        $todayCheckIns = Reservation::whereDate('check_in_date', today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();
            
        $todayCheckOuts = Reservation::whereDate('check_out_date', today())
            ->where('status', 'checked_in')
            ->count();

        return [
            Stat::make('Total Rooms', $totalRooms)
                ->description('Active rooms')
                ->descriptionIcon('heroicon-o-home')
                ->color('primary'),
                
            Stat::make('Occupied Rooms', $occupiedRooms)
                ->description("{$occupancyRate}% occupancy rate")
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),
                
            Stat::make('Available Rooms', $availableRooms)
                ->description('Ready for booking')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('info'),
                
            Stat::make('Reserved Rooms', $reservedRooms)
                ->description('Upcoming arrivals')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),
                
            Stat::make('Today Check-Ins', $todayCheckIns)
                ->description('Expected arrivals')
                ->descriptionIcon('heroicon-o-arrow-right-on-rectangle')
                ->color('success'),
                
            Stat::make('Today Check-Outs', $todayCheckOuts)
                ->description('Expected departures')
                ->descriptionIcon('heroicon-o-arrow-left-on-rectangle')
                ->color('danger'),
        ];
    }
}