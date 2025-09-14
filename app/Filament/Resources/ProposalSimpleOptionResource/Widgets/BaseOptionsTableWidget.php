<?php

namespace App\Filament\Resources\ProposalSimpleOptionResource\Widgets;

use App\Models\ProposalSimpleOption;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

 

abstract class BaseOptionsTableWidget extends BaseWidget
{
    protected static ?string $heading = null;
    protected static string $sectionKey;

    public function table(Table $table): Table
    {
        return $table
            ->query(ProposalSimpleOption::query()->where('section_key', static::$sectionKey)->orderBy('sort_order'))
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable()->label('Label'),
                Tables\Columns\IconColumn::make('is_other')->boolean()->label('Other'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('sort_order')->sortable()->label('Order'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->model(ProposalSimpleOption::class)
                    ->label('New option')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['section_key'] = static::$sectionKey;
                        return $data;
                    })
                    ->form([
                        Forms\Components\TextInput::make('label')->required(),
                        Forms\Components\Toggle::make('is_other')->label('Other option?')->default(false),
                        Forms\Components\Toggle::make('is_active')->default(true),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('label')->required(),
                        Forms\Components\Toggle::make('is_other')->label('Other option?'),
                        Forms\Components\Toggle::make('is_active'),
                        Forms\Components\TextInput::make('sort_order')->numeric(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated(false);
    }
}
