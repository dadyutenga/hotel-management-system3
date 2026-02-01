<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationResource extends Resource {
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool {
        return true;
    }

    public static function form(Form $form): Form {
        return $form->schema([
            Forms\Components\Section::make('Guest Information')->schema([
                Forms\Components\TextInput::make('guest_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('guest_phone')->required()->tel()->maxLength(255),
                Forms\Components\TextInput::make('guest_email')->email()->maxLength(255),
                Forms\Components\TextInput::make('number_of_guests')->required()->numeric()->minValue(1),
            ])->columns(2),

            Forms\Components\Section::make('Reservation Details')->schema([
                Forms\Components\DatePicker::make('check_in_date')->required()->native(false),
                Forms\Components\DatePicker::make('check_out_date')
                    ->required()
                    ->native(false)
                    ->after('check_in_date'),
                Forms\Components\Select::make('room_id')
                    ->label('Room')
                    ->options(function (callable $get) {
                        $checkIn = $get('check_in_date');
                        $checkOut = $get('check_out_date');
                        
                        if (!$checkIn || !$checkOut) {
                            return Room::where('is_active', true)->pluck('room_number', 'id');
                        }

                        return Room::where('is_active', true)
                            ->whereDoesntHave('reservations', function (Builder $query) use ($checkIn, $checkOut) {
                                $query->where('status', '!=', 'cancelled')
                                    ->where(function ($q) use ($checkIn, $checkOut) {
                                        $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                          ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                                              $q2->where('check_in_date', '<=', $checkIn)
                                                 ->where('check_out_date', '>=', $checkOut);
                                          });
                                    });
                            })
                            ->pluck('room_number', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ])
                    ->default('pending')
                    ->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reservation_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('guest_name')->searchable(),
                Tables\Columns\TextColumn::make('room.room_number')->label('Room')->sortable(),
                Tables\Columns\TextColumn::make('check_in_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('check_out_date')->date()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'checked_in',
                        'secondary' => 'checked_out',
                        'danger' => 'cancelled',
                        'gray' => 'no_show',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')->money('USD')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ]),
                Tables\Filters\Filter::make('check_in_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('check_in_date', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('check_in_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('checkIn')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => $record->status === 'confirmed')
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'checked_in']);
                        $record->room?->update(['status' => 'occupied']);
                    }),
                Tables\Actions\Action::make('checkOut')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => $record->status === 'checked_in')
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'checked_out']);
                        $record->room?->update(['status' => 'dirty']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}