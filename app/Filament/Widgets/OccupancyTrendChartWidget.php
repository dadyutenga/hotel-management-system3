<?php
namespace App\Filament\Widgets;

use App\Models\Reservation;
use App\Models\Room;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OccupancyTrendChartWidget extends ChartWidget {
    protected static ?string $heading = '7-Day Occupancy Trend';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array {
        $totalRooms = Room::where('is_active', true)->count();
        $days = [];
        $occupancyData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('M d');
            
            $occupied = Reservation::where('status', 'checked_in')
                ->where('check_in_date', '<=', $date)
                ->where('check_out_date', '>', $date)
                ->count();
                
            $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 1) : 0;
            $occupancyData[] = $occupancyRate;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Occupancy Rate (%)',
                    'data' => $occupancyData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string {
        return 'line';
    }
}