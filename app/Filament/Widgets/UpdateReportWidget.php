<?php
namespace App\Filament\Widgets;

use App\Models\SystemUpdate;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpdateReportWidget extends BaseWidget
{
    // Make the table span the full width of the report page
    protected int | string | array $columnSpan = 'full';
    public static function canView(): bool
    {
        // Only show if the current URL contains 'reports'
        return str_contains(request()->url(), 'reports');
    }

    public function table(Table $table): Table
    {
        return $table
            // 1. The Query: Eager load relationships for speed
            ->query(
                SystemUpdate::query()
                    ->with(['system', 'user']) 
                    ->latest() // Show newest updates first by default
            )
            // 2. The Columns
            ->columns([
                Tables\Columns\TextColumn::make('system.name')
                    ->label('System')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Developer')
                    ->sortable()
                    ->hidden(fn () => !auth()->user()->role === 'admin'), // Optional: hide if not admin

                Tables\Columns\TextColumn::make('title')
                    ->label('Update Title')
                    ->searchable()
                    ->wrap(), // Good for long descriptions

                Tables\Columns\TextColumn::make('version')
                    ->label('Ver.')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            // 3. The Filters (Ownership + Date Range)
            ->filters([
                // FILTER A: The "Just Me" Toggle (Only for Admins)
                TernaryFilter::make('user_id')
                    ->label('Ownership')
                    ->placeholder('All Team Updates')
                    ->trueLabel('My Updates Only')
                    ->falseLabel('Other Developers')
                    ->queries(
                        true: fn ($query) => $query->where('user_id', auth()->id()),
                        false: fn ($query) => $query->where('user_id', '!=', auth()->id()),
                        blank: fn ($query) => $query, // Show all
                    )
                    ->visible(fn () => auth()->user()->role === 'admin'),

                // FILTER B: The Date Range
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->placeholder('Start'),
                        DatePicker::make('until')
                            ->label('Until Date')
                            ->placeholder('End'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'From ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Until ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
            ])
            // 4. UI Layout Settings
            ->filtersFormColumns(2) // Puts date pickers side-by-side
            ->actions([
                // Quick view button if you want to see full description
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'admin'),
                ]),
            ]);
    }
}