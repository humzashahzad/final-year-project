<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationMenuResource\Pages;
use App\Filament\Resources\NavigationMenuResource\RelationManagers;
use App\Models\NavigationGroup;
use App\Models\NavigationMenu;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NavigationMenuResource extends Resource
{
    protected static ?string $model = NavigationMenu::class;


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
                Select::make('name')
                    ->label('Menu')
                    ->options(function () {
                        
                        $resources = [];
                        
                        $MenuItems = NavigationMenu::select('page_path', 'encryption_salt')->get()->toArray();
                        $existingMenuItems = array_column($MenuItems, 'page_path');

                        $resourcePath = app_path('Filament/Pages');
                        if (is_dir($resourcePath)) {
                            $files = glob($resourcePath . '/*.php');
                            foreach ($files as $file) {
                                $filename = basename($file, '.php');

                                if (!in_array($filename.'.php', $existingMenuItems)) {
                                    $formattedName = preg_replace('/(?<!^)([A-Z])/', ' $1', $filename);
                                    $resources[$formattedName] = $formattedName;
                                }
                            }
                        }

                        $resourcePath = app_path('Filament/Resources');
                        if (is_dir($resourcePath)) {
                            $files = glob($resourcePath . '/*.php');
                            foreach ($files as $file) {
                                $filename = basename($file, '.php');
                                if (!in_array($filename.'.php', $existingMenuItems)) {
                                    $formattedName = preg_replace('/(?<!^)([A-Z])/', ' $1', $filename);
                                    $resources[$formattedName] = str_replace('Resource', '', $formattedName);
                                }
                            }
                        }

                        return $resources;
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {

                            $className = class_basename(class: $state);

                            $set('page_path', str_replace(' ', '', $className).'.php');

                        }
                    })
                    ->columnSpan(3),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(3)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('page_path')->required()->maxLength(255)->columnSpan(2),
                TextInput::make('order')->columnSpan(2),
                TextInput::make('icon')->columnSpan(2),
                ColorPicker::make('color')->columnSpan(2), 
                Select::make('group_id')
                    ->options(NavigationGroup::where('status', 1)->pluck('name', 'id')->toArray())
                    ->rules(['required'])
                    ->label('Group')
                    ->columnSpan(2)
                    ->searchable(),
                Repeater::make('objects')
                    ->schema([
                        TextInput::make('object')->required(),
                    ])
                    ->reorderable(false)
                    ->deletable(false)
                    ->columnSpanFull()
                    ->default([
                        [ 'object' => 'all' ],
                        [ 'object' => 'create' ],
                        [ 'object' => 'read' ],
                        [ 'object' => 'edit' ],
                        [ 'object' => 'delete' ],
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group.name')->label('Name')->searchable(),
                TextColumn::make('name')->label('Name')->searchable(),
                TextInputColumn::make('icon')->label('Page Icon')->searchable()->width('50px'),
                ColorColumn::make('color')->label('Icon Color')->searchable()->width('50px'),
                TextInputColumn::make('order')->label('Order')->searchable()->width('50px'),
                ToggleColumn::make('status')->label('Status')->width('50px')
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
            'index' => Pages\ListNavigationMenus::route('/'),
            // 'create' => Pages\CreateNavigationMenu::route('/create'),
            // 'edit' => Pages\EditNavigationMenu::route('/{record}/edit'),
        ];
    }
}
