<?php

namespace App\Filament\Resources\MainModuleResource\Pages;

use App\Filament\Resources\MainModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainModule extends EditRecord
{
    protected static string $resource = MainModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
