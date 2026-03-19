<?php

namespace App\Filament\Resources\SystemUpdateResource\Pages;

use App\Filament\Resources\SystemUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemUpdate extends EditRecord
{
    protected static string $resource = SystemUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
