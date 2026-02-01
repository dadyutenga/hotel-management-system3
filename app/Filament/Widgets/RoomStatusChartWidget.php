<?php
namespace App\Filament\Widgets;

use App\Models\Room;
use Filament\Widgets\ChartWidget;

class RoomStatusChartWidget extends ChartWidget {
    protected static ?string $heading = 'Room Status Distribution';
    protected static ?int $sort = 2;

    protected function getData(): array {
        $statusCounts = Room::where('is_active', true)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Rooms',
                    'data' => [
                        $statusCounts['available'] ?? 0,
                        $statusCounts['reserved'] ?? 0,
                        $statusCounts['occupied'] ?? 0,
                        $statusCounts['dirty'] ?? 0,
                        $statusCounts['out_of_order'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#6b7280',
                        '#1f2937',
                    ],
                ],
            ],
            'labels' => ['Available', 'Reserved', 'Occupied', 'Dirty', 'Out of Order'],
        ];
    }

    protected function getType(): string {
        return 'doughnut';
    }
}