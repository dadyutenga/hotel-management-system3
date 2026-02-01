<?php
namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingArrivalsWidget extends BaseWidget {
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table {
        return $table
            ->heading('Upcoming Arrivals (Next 7 Days)')
            ->query(
                Reservation::query()
                    ->whereBetween('check_in_date', [today(), today()->addDays(7)])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('check_in_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('reservation_number')
                    ->label('Reservation #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_phone'),
                Tables\Columns\TextColumn::make('check_in_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_date')
                    ->date(),
                Tables\Columns\TextColumn::make('room.room_number')
                    ->label('Room')
                    ->default('Not Assigned'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Reservation $record): string => route('filament.admin.resources.reservations.edit', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}