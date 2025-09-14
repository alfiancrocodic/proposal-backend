<?php

namespace App\Filament\Resources\ProposalSimpleOptionResource\Pages;

use App\Filament\Resources\ProposalSimpleOptionResource;
use App\Filament\Resources\ProposalSimpleOptionResource\Widgets\AccountAvailabilityOptionsTable;
use App\Filament\Resources\ProposalSimpleOptionResource\Widgets\DbAvailabilityOptionsTable;
use App\Filament\Resources\ProposalSimpleOptionResource\Widgets\DbInfoOptionsTable;
use App\Filament\Resources\ProposalSimpleOptionResource\Widgets\FrontendLangOptionsTable;
use App\Filament\Resources\ProposalSimpleOptionResource\Widgets\AppInfoOptionsTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables; 
use Filament\Tables\Table;

class ListProposalSimpleOptions extends ListRecords
{
    protected static string $resource = ProposalSimpleOptionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            FrontendLangOptionsTable::class,
            AppInfoOptionsTable::class,
            AccountAvailabilityOptionsTable::class,
            DbAvailabilityOptionsTable::class,
            DbInfoOptionsTable::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2; // tampil 2 kolom grid
    }

    public function table(Table $table): Table
    {
        // Kosongkan tabel utama; arahkan user menggunakan tabel per section di atas
        return $table
            ->columns([])
            ->emptyStateHeading('Use the section tables below to manage options')
            ->paginated(false);
    }
}
