<?php

namespace App\Filament\Store\Resources;

use App\Filament\Store\Resources\RoleResource\Pages;
use App\Filament\Store\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()->default('hamza'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->default(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('description')->searchable(),
                ToggleColumn::make('status')->width('100px'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('xs')->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            // 'create' => Pages\CreateRole::route('/create'),
            // 'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
