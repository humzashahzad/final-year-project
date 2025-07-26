<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Filament\Resources\DomainResource\RelationManagers;
use App\Models\Domain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rectangle-stack';
    }
    public static function canCreate(): bool
    {  
        return false;
    }
    public static function canViewAny(): bool
    {
        return true;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
    public static function canDelete($record): bool
    {   
        return false;
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
        return 'SAAS Configration';
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')->label('Domain')->searchable(),
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
