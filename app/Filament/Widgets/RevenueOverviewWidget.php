<?php
namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueOverviewWidget extends BaseWidget {
    protected static ?int $sort = 8;

    public static function canView(): bool {
        return auth()->user()->isAdmin() || auth()->user()->isSupervisor();
    }

    protected function getStats(): array {
        $todayRevenue = Reservation::whereDate('check_in_date', today())
            ->where('status', 'checked_in')
            ->sum('total_amount');

        $weekRevenue = Reservation::whereBetween('check_in_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->sum('total_amount');

        $monthRevenue = Reservation::whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->sum('total_amount');

        $pendingRevenue = Reservation::whereIn('status', ['pending', 'confirmed'])
            ->sum('total_amount');

        return [
            Stat::make('Today Revenue', '$' . number_format($todayRevenue, 2))
                ->description('Revenue from today\'s check-ins')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Week Revenue', '$' . number_format($weekRevenue, 2))
                ->description('This week\'s earnings')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make('Month Revenue', '$' . number_format($monthRevenue, 2))
                ->description('Current month earnings')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info'),

            Stat::make('Pending Revenue', '$' . number_format($pendingRevenue, 2))
                ->description('From upcoming reservations')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}