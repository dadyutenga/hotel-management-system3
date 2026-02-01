<?php
namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;

class ReservationStatusChartWidget extends ChartWidget {
    protected static ?string $heading = 'Reservation Status';
    protected static ?int $sort = 3;

    protected function getData(): array {
        $statusCounts = Reservation::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Reservations',
                    'data' => [
                        $statusCounts['pending'] ?? 0,
                        $statusCounts['confirmed'] ?? 0,
                        $statusCounts['checked_in'] ?? 0,
                        $statusCounts['checked_out'] ?? 0,
                        $statusCounts['cancelled'] ?? 0,
                        $statusCounts['no_show'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#f59e0b',
                        '#10b981',
                        '#3b82f6',
                        '#6b7280',
                        '#ef4444',
                        '#1f2937',
                    ],
                ],
            ],
            'labels' => ['Pending', 'Confirmed', 'Checked In', 'Checked Out', 'Cancelled', 'No Show'],
        ];
    }

    protected function getType(): string {
        return 'pie';
    }
}