<?php

namespace App\Filament\Resources\SubModuleResource\Pages;

use App\Filament\Resources\SubModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubModule extends EditRecord
{
    protected static string $resource = SubModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
