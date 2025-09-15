<?php

namespace App\Filament\Resources\MainModuleResource\Pages;

use App\Filament\Resources\MainModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMainModules extends ListRecords
{
    protected static string $resource = MainModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
