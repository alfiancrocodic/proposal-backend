<?php

namespace App\Filament\Resources\SubModuleResource\Pages;

use App\Filament\Resources\SubModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubModules extends ListRecords
{
    protected static string $resource = SubModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
