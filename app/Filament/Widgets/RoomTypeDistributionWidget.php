<?php
namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\RoomType;
use Filament\Widgets\ChartWidget;

class RoomTypeDistributionWidget extends ChartWidget {
    protected static ?string $heading = 'Room Type Distribution';
    protected static ?int $sort = 7;

    protected function getData(): array {
        $roomTypes = RoomType::withCount(['rooms' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        return [
            'datasets' => [
                [
                    'label' => 'Rooms by Type',
                    'data' => $roomTypes->pluck('rooms_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => $roomTypes->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string {
        return 'bar';
    }
}