<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdutoResource\Pages;
use App\Filament\Resources\ProdutoResource\RelationManagers;
use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdutoResource extends Resource
{
    protected static ?string $model = Produto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->live(onBlur: true)
                    ->afterStateUpdated( function($state, Set $set) {
                       dd('teste');
                    })
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('estoque')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('venda_id')
                    ->numeric()
                    ->default( function() {
                        return random_int(1000, 9999);
                    })
                    ->label('Venda'),
                Forms\Components\TextInput::make('valor_compra')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('lucratividade')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('valor_venda')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estoque')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valor_compra')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lucratividade')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_venda')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProdutos::route('/'),
        ];
    }    
}
