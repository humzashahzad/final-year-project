<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationGroupResource\Pages;
use App\Filament\Resources\NavigationGroupResource\RelationManagers;
use App\Models\NavigationGroup;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NavigationGroupResource extends Resource
{
    protected static ?string $model = NavigationGroup::class;
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rectangle-stack';
    }
    public static function canCreate(): bool
    {  
        return true;
    }
    public static function canViewAny(): bool
    {
        return true;
    }
    public static function canEdit($record): bool
    {
        return true;
    }
    public static function canDelete($record): bool
    {   
        return true;
    }
    public static function shouldRegisterNavigation(): bool
    {   
        return true;
    }
    public static function getNavigationSort(): int
    {
        return 1;
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Menu Bar Settings';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('icon')->columnSpanFull(),
                ColorPicker::make('color')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Icon')->searchable(),
                TextInputColumn::make('icon')->label('Icon')->searchable()->width('100px'),
                ColorColumn::make('color')->label('Color')->searchable()->width('100px'),
                ToggleColumn::make('status')->label('Status')->width('100px')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListNavigationGroups::route('/'),
            // 'create' => Pages\CreateNavigationGroup::route('/create'),
            // 'edit' => Pages\EditNavigationGroup::route('/{record}/edit'),
        ];
    }
}
