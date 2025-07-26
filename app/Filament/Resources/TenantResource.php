<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
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
        return 'SAAS Configration';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(6)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(3),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->columnSpan(3),
                        TextInput::make('password')
                            ->password()
                            ->helperText('(Leave empty if don\'t change the password!)')
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (?Tenant $record) => $record === null)
                            ->maxLength(255)
                            ->columnSpan(3),
                        TextInput::make('domain')
                            ->required()
                            ->helperText('(This domain name should be your url in future to access you panel!)')
                            ->prefix('https://')
                            ->suffix(app()->environment('production') ? '.m-dev.io' : '.localhost')
                            ->columnSpan(3)
                            ->unique('domains', 'name'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([ 
                    Tables\Columns\TextColumn::make('id')->label('Tenant')->searchable(),
                    Tables\Columns\TextColumn::make('tenant_domain.domain')->label('Domain')->searchable(),
                    Tables\Columns\TextColumn::make('tenant_user.name')->label('Name')->searchable(),
                    Tables\Columns\TextColumn::make('tenant_user.email')->label('Email')->searchable(),
                ]),
            ])
            ->contentGrid([
                'md' => 4,
                'xl' => 4,
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
    public static function getRecordRouteKeyName(): ?string
    {
        return 'slug';
    }
}
