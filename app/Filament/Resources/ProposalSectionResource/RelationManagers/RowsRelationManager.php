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
        return $form->schema($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        $sectionKey = $this->getOwnerRecord()?->key;
        $base = [
            Forms\Components\Hidden::make('section_id')->default($this->getOwnerRecord()?->id),
        ];
        if ($sectionKey === 'terms_payment') {
            $specific = [
                Forms\Components\TextInput::make('values.percentage')->label('Percentage (%)')->numeric()->required(),
                Forms\Components\Textarea::make('values.description')->label('Description')->rows(3)->required(),
                Forms\Components\TextInput::make('values.total')->label('Total')->numeric()->required(),
            ];
        } elseif ($sectionKey === 'terms_conditions') {
            $specific = [
                Forms\Components\Textarea::make('values.term')->label('Term')->rows(4)->required(),
            ];
        } else {
            $specific = [
                Forms\Components\KeyValue::make('values')
                    ->label('Values (by column key)')
                    ->reorderable()
                    ->addActionLabel('Add field')
                    ->columnSpanFull(),
            ];
        }
        return array_merge($base, $specific, [
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        $sectionKey = $this->getOwnerRecord()?->key;
        $columns = [
            Tables\Columns\TextColumn::make('id'),
        ];
        if ($sectionKey === 'terms_payment') {
            $columns[] = Tables\Columns\TextColumn::make('values.percentage')->label('Percentage');
            $columns[] = Tables\Columns\TextColumn::make('values.description')->label('Description')->limit(60);
            $columns[] = Tables\Columns\TextColumn::make('values.total')->label('Total');
        } elseif ($sectionKey === 'terms_conditions') {
            $columns[] = Tables\Columns\TextColumn::make('values.term')->label('Term')->limit(80);
        } else {
            $columns[] = Tables\Columns\TextColumn::make('values.platform')->label('Platform')->limit(30);
        }
        $columns[] = Tables\Columns\TextColumn::make('sort_order')->sortable();
        $columns[] = Tables\Columns\IconColumn::make('is_active')->boolean();

        return $table
            ->reorderable('sort_order')
            ->columns($columns)
            ->headerActions([
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
