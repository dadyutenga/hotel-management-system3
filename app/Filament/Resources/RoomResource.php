<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isSupervisor());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('floor_id')
                ->relationship('floor', 'name')
                ->required(),
            Forms\Components\Select::make('room_type_id')
                ->relationship('roomType', 'name')
                ->required(),
            Forms\Components\TextInput::make('room_number')->required()->maxLength(255),
            Forms\Components\Select::make('status')
                ->options([
                    'available' => 'Available',
                    'reserved' => 'Reserved',
                    'occupied' => 'Occupied',
                    'dirty' => 'Dirty',
                    'out_of_order' => 'Out of Order',
                ])
                ->default('available')
                ->required(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('floor.name')->label('Floor')->sortable(),
                Tables\Columns\TextColumn::make('roomType.name')->label('Type')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'reserved',
                        'danger' => 'occupied',
                        'secondary' => 'dirty',
                        'gray' => 'out_of_order',
                    ]),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('floor')->relationship('floor', 'name'),
                Tables\Filters\SelectFilter::make('roomType')->relationship('roomType', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'reserved' => 'Reserved',
                        'occupied' => 'Occupied',
                        'dirty' => 'Dirty',
                        'out_of_order' => 'Out of Order',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}