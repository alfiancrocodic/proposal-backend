<?php

namespace App\Filament\Resources\ProposalContentResource\Pages;

use App\Filament\Resources\ProposalContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProposalContents extends ListRecords
{
    protected static string $resource = ProposalContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
