<?php

namespace App\Filament\Resources\ProposalContentResource\Pages;

use App\Filament\Resources\ProposalContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProposalContent extends EditRecord
{
    protected static string $resource = ProposalContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
