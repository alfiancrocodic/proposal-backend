<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalSectionResource\Pages;
use App\Filament\Resources\ProposalSectionResource\RelationManagers\ColumnsRelationManager;
use App\Filament\Resources\ProposalSectionResource\RelationManagers\RowsRelationManager;
use App\Filament\Resources\ProposalSectionResource\RelationManagers\FootnotesRelationManager;
use App\Models\ProposalSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalSectionResource extends Resource
{
    protected static ?string $model = ProposalSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'Proposal Templates';
    protected static ?string $navigationLabel = 'Sections';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'proposal-sections';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Select::make('type')->options([
                'complex' => 'Complex (table)',
                'simple' => 'Simple (list)',
            ])->required()->default('complex'),
            Forms\Components\Textarea::make('description')->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('key')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            ColumnsRelationManager::class,
            RowsRelationManager::class,
            FootnotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposalSections::route('/'),
            'create' => Pages\CreateProposalSection::route('/create'),
            'edit' => Pages\EditProposalSection::route('/{record}/edit'),
        ];
    }
}

