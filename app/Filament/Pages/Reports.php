<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.pages.reports';
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\UpdateReportWidget::class,
        ];
    }
}
