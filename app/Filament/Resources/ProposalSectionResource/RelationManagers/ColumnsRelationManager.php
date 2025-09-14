<?php

namespace App\Filament\Resources\ProposalSectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ColumnsRelationManager extends RelationManager
{
    protected static string $relationship = 'columns';
    protected static ?string $title = 'Columns';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required(),
            Forms\Components\TextInput::make('label')->required(),
            Forms\Components\Select::make('input_type')->options([
                'text' => 'Text',
                'textarea' => 'Textarea',
                'chips' => 'Chips',
                'multiselect' => 'Multi Select',
                'checkbox' => 'Checkbox',
            ])->default('text')->required(),
            Forms\Components\Toggle::make('is_checkable')->label('Render as checklist')->default(false),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('key'),
            Tables\Columns\TextColumn::make('label'),
            Tables\Columns\BadgeColumn::make('input_type'),
            Tables\Columns\IconColumn::make('is_checkable')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('sort_order');
    }
}

