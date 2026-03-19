<?php

namespace App\Filament\Resources\SystemUpdateResource\Pages;

use App\Filament\Resources\SystemUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemUpdates extends ListRecords
{
    protected static string $resource = SystemUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
