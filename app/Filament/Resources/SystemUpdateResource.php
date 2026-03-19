<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemUpdateResource\Pages;
use App\Filament\Resources\SystemUpdateResource\RelationManagers;
use App\Models\SystemUpdate;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemUpdateResource extends Resource
{
    protected static ?string $model = SystemUpdate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Grid::make(3) // 3-column layout
                ->schema([
                    // Main Content (Left side, 2 columns wide)
                    Section::make('Log Entry')
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->placeholder('e.g., Fixed Login Session Timeout'),
                            RichEditor::make('description')
                                ->required()
                                ->columnSpanFull(),
                        ])->columnSpan(2),

                    // Metadata (Right side, 1 column wide)
                    Section::make('Details')
                        ->schema([
                            Select::make('system_id')
                                ->relationship('system', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('version')
                                ->placeholder('v1.0.1')
                                ->prefixIcon('heroicon-m-tag'),
                            Select::make('type')
                                ->options([
                                    'update' => '🚀 New Update',
                                    'feature' => '🚀 New Feature',
                                    'bugfix' => '🐛 Bug Fix',
                                    'improvement' => '⚡ Improvement',
                                    'security' => '🔒 Security Patch',
                                ])
                                ->native(false)
                                ->required(),
                        ])->columnSpan(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
       return $table
        ->columns([
            TextColumn::make('system.name')
                ->badge()
                ->icon('heroicon-m-computer-desktop'), // Icon for the system name

            TextColumn::make('title')
                ->searchable()
                ->weight('bold'),

            TextColumn::make('version')
                ->icon('heroicon-m-tag') // Icon for the version
                ->copyable() // Bonus: Clicking the version copies it to clipboard
                ->copyMessage('Version copied!'),

            TextColumn::make('type')
                ->badge()
                ->icon(fn (string $state): string => match ($state) {
                    'feature' => 'heroicon-m-rocket-launch',
                    'bugfix' => 'heroicon-m-bug-ant',
                    'improvement' => 'heroicon-m-sparkles',
                    'security' => 'heroicon-m-shield-check',
                    default => 'heroicon-m-question-mark-circle',
                })
                ->color(fn (string $state): string => match ($state) {
                    'feature' => 'success',
                    'bugfix' => 'danger',
                    'improvement' => 'info',
                    'security' => 'warning',
                    default => 'gray',
                }),
        ])
        ->filters([
            // PASTE THE FILTER HERE
            Filter::make('authored_by_me')
                ->label('My Updates Only')
                ->query(fn (Builder $query): Builder => $query->where('user_id', auth()->id())),
            
            // You can keep your other filters here too, like:
            SelectFilter::make('system')
                ->relationship('system', 'name'),
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
            'index' => Pages\ListSystemUpdates::route('/'),
            'create' => Pages\CreateSystemUpdate::route('/create'),
            'edit' => Pages\EditSystemUpdate::route('/{record}/edit'),
        ];
    }
}
