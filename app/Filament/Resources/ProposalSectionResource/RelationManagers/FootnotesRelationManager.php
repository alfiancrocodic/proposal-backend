<?php

namespace App\Filament\Resources\ProposalSectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FootnotesRelationManager extends RelationManager
{
    protected static string $relationship = 'footnotes';
    protected static ?string $title = 'Footnotes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('text')->rows(2)->columnSpanFull()->required(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('text')->limit(80),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('sort_order');
    }
}

