<?php
namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayActivityWidget extends BaseWidget {
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table {
        return $table
            ->heading('Today\'s Activity')
            ->query(
                Reservation::query()
                    ->where(function ($query) {
                        $query->whereDate('check_in_date', today())
                              ->orWhereDate('check_out_date', today());
                    })
                    ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
                    ->orderBy('check_in_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('reservation_number')
                    ->label('Reservation #'),
                Tables\Columns\TextColumn::make('guest_name'),
                Tables\Columns\BadgeColumn::make('activity')
                    ->getStateUsing(function (Reservation $record) {
                        if ($record->check_in_date->isToday()) {
                            return 'Check-In';
                        }
                        return 'Check-Out';
                    })
                    ->colors([
                        'success' => 'Check-In',
                        'danger' => 'Check-Out',
                    ]),
                Tables\Columns\TextColumn::make('room.room_number')
                    ->label('Room'),
                Tables\Columns\TextColumn::make('number_of_guests')
                    ->label('Guests'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'checked_in',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('checkIn')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->visible(fn (Reservation $record) => $record->status === 'confirmed' && $record->check_in_date->isToday())
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'checked_in']);
                        $record->room?->update(['status' => 'occupied']);
                    }),
                Tables\Actions\Action::make('checkOut')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('danger')
                    ->visible(fn (Reservation $record) => $record->status === 'checked_in' && $record->check_out_date->isToday())
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'checked_out']);
                        $record->room?->update(['status' => 'dirty']);
                    }),
            ]);
    }
}