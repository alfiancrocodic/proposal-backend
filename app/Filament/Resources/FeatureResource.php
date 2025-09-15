<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureResource\Pages;
use App\Filament\Resources\FeatureResource\RelationManagers;
use App\Models\Feature;
use App\Models\SubModule;
use App\Models\MainModule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationGroup = 'Feature Management';
    
    protected static ?string $navigationLabel = 'Features';
    
    protected static ?string $modelLabel = 'Feature';
    
    protected static ?string $pluralModelLabel = 'Features';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sub_module_id')
                    ->label('Sub Module')
                    ->options(function () {
                        return SubModule::with('mainModule')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($subModule) {
                                return [$subModule->id => $subModule->mainModule->name . ' - ' . $subModule->name];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\TextInput::make('mandays')
                    ->label('Man Days')
                    ->required()
                    ->numeric()
                    ->step(0.5)
                    ->minValue(0)
                    ->default(0.00)
                    ->suffix('hari'),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subModule.mainModule.name')
                    ->label('Main Module')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subModule.name')
                    ->label('Sub Module')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Feature'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->label('Deskripsi'),
                Tables\Columns\TextColumn::make('mandays')
                    ->numeric()
                    ->sortable()
                    ->label('Man Days')
                    ->suffix(' hari'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status Aktif'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable()
                    ->label('Urutan'),
                Tables\Columns\TextColumn::make('conditions_count')
                    ->counts('conditions')
                    ->label('Jumlah Kondisi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sub_module_id')
                    ->label('Sub Module')
                    ->options(function () {
                        return SubModule::with('mainModule')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($subModule) {
                                return [$subModule->id => $subModule->mainModule->name . ' - ' . $subModule->name];
                            });
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->defaultSort('sort_order', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
