<?php
namespace App\Filament\Widgets;

use App\Models\SystemUpdate;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
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
            ->query(
                SystemUpdate::query()
                    ->with(['system', 'user'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('system.name')
                    ->label('System')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Developer'),
                TextColumn::make('title')
                    ->label('Update Title')
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y'),
            ])
            ->filters([
                SelectFilter::make('system_id')
                    ->label('Filter by System')
                    ->relationship('system', 'name')
                    ->preload(),

                TernaryFilter::make('user_id')
                    ->label('Ownership')
                    ->placeholder('All Updates')
                    ->trueLabel('My Updates Only')
                    ->falseLabel('Other Developers')
                    ->queries(
                        true: fn ($query) => $query->where('user_id', auth()->id()),
                        false: fn ($query) => $query->where('user_id', '!=', auth()->id()),
                        blank: fn ($query) => $query,
                    ),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->headerActions([
                // NEW: DOCUMENT GENERATOR MODAL
                Action::make('generate_doc')
                    ->label('Generate Document')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->modalHeading('Report Preview')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false) // We added our own buttons in the view
                    ->modalContent(fn ($livewire) => view('filament.pages.report-modal', [
                        'records' => $livewire->getFilteredTableQuery()->get(),
                    ])),
            ])
            ->filtersFormColumns(3);
    }
}