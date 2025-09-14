<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalSimpleOptionResource\Pages;
use App\Models\ProposalSimpleOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalSimpleOptionResource extends Resource
{
    protected static ?string $model = ProposalSimpleOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Proposal Templates';
    protected static ?string $navigationLabel = 'Simple Options';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'proposal-simple-options';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('section_key')
                ->options([
                    'frontend_lang' => 'Frontend Interface Language',
                    'app_info' => 'Application Information',
                    'account_availability' => 'Account Availability',
                    'db_availability' => 'Database Availability',
                    'db_info' => 'Database Information',
                ])->required(),
            Forms\Components\TextInput::make('label')->required(),
            Forms\Components\Toggle::make('is_other')->label('Other option?')->default(false),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('section_key')->label('Section'),
                Tables\Columns\TextColumn::make('label')->searchable(),
                Tables\Columns\IconColumn::make('is_other')->boolean()->label('Other'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section_key')
                    ->options([
                        'frontend_lang' => 'Frontend Interface Language',
                        'app_info' => 'Application Information',
                        'account_availability' => 'Account Availability',
                        'db_availability' => 'Database Availability',
                        'db_info' => 'Database Information',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('section_key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposalSimpleOptions::route('/'),
            'create' => Pages\CreateProposalSimpleOption::route('/create'),
            'edit' => Pages\EditProposalSimpleOption::route('/{record}/edit'),
        ];
    }
}

