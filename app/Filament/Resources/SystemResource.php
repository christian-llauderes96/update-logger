<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemResource\Pages;
use App\Filament\Resources\SystemResource\RelationManagers;
use App\Models\System;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemResource extends Resource
{
    protected static ?string $model = System::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery(); 
        // ->where('user_id', auth()->id()); //<-- REMOVE OR COMMENT THIS LINE if you want filter own system
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('System Information')
                ->description('Identify the webapp or service you are tracking.')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Internal CRM'),
                    
                    TextInput::make('url')
                        ->label('System URL')
                        ->url()
                        ->placeholder('https://crm.company.com'),
                    DatePicker::make('developed_at')
                        ->label('Development Started')
                        ->native(false) // Gives a nice modern calendar popup
                        ->displayFormat('d/m/Y'),

                    Textarea::make('description')
                        ->label('Purpose of this System')
                        ->placeholder('Briefly describe what this app does...')
                        ->rows(3)
                        ->columnSpanFull(), // Makes it take the full width of the form
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                // Static date from the System table
                Tables\Columns\TextColumn::make('developed_at')
                    ->label('Dev Started')
                    ->date()
                    ->sortable(),

                // Dynamic date pulled from the LATEST System Update
                Tables\Columns\TextColumn::make('latestUpdate.created_at')
                    ->label('Last Update')
                    ->since()
                    ->color(fn ($state) => $state && $state->diffInDays(now()) > 30 ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->filters([
                // THE OWNERSHIP FILTER
                Tables\Filters\TernaryFilter::make('my_systems')
                    ->label('System Ownership')
                    ->placeholder('All Systems')
                    ->trueLabel('My Systems Only')
                    ->falseLabel('Others')
                    ->queries(
                        true: fn ($query) => $query->where('user_id', auth()->id()),
                        false: fn ($query) => $query->where('user_id', '!=', auth()->id()),
                        blank: fn ($query) => $query, // Shows everything by default
                    ),
            ])
            ->filtersFormColumns(1); // Keeps the filter dropdown clean
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
            'index' => Pages\ListSystems::route('/'),
            'create' => Pages\CreateSystem::route('/create'),
            'edit' => Pages\EditSystem::route('/{record}/edit'),
        ];
    }
}
