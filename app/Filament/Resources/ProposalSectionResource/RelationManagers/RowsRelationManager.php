<?php

namespace App\Filament\Resources\ProposalSectionResource\RelationManagers;

use App\Models\ProposalSectionRow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class RowsRelationManager extends RelationManager
{
    protected static string $relationship = 'rows';
    protected static ?string $title = 'Rows';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\KeyValue::make('values')
                ->label('Values (by column key)')
                ->reorderable()
                ->addActionLabel('Add field')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('values.platform')->label('Platform')->limit(30),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\Action::make('copy')
                ->label('Copy')
                ->icon('heroicon-m-document-duplicate')
                ->requiresConfirmation()
                ->action(function (ProposalSectionRow $record): void {
                    $clone = $record->replicate(['sort_order', 'created_at', 'updated_at']);
                    $clone->sort_order = (int) (ProposalSectionRow::where('section_id', $record->section_id)->max('sort_order') ?? 0) + 1;
                    $vals = $record->values ?? [];
                    if (is_array($vals) && ! empty($vals['platform'])) {
                        $vals['platform'] = (string) $vals['platform'] . ' (Copy)';
                    }
                    $clone->values = $vals;
                    $clone->is_active = true;
                    $clone->save();

                    Notification::make()
                        ->title('Row copied')
                        ->success()
                        ->send();
                }),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('sort_order');
    }
}
