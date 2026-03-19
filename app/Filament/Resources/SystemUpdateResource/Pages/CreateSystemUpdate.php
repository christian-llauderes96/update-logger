<?php

namespace App\Filament\Resources\SystemUpdateResource\Pages;

use App\Filament\Resources\SystemUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSystemUpdate extends CreateRecord
{
    protected static string $resource = SystemUpdateResource::class;
    /**
     * This function runs right before the data hits MySQL.
     * It adds the logged-in user's ID to the record.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
