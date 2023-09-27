<?php

namespace App\Filament\Resources\VendaResource\RelationManagers;

use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\PDV as PDVs;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PdvRelationManager extends RelationManager
{
    public $produto_id;

    public $qtd;

    protected static string $relationship = 'pdv';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('venda_id')
                    ->required()
                    ->maxLength(255),
                Section::make('Código do Produto')
                    ->columns(4)
                    ->schema([
                        /*   Select::make('cliente_id')
                            ->options(Cliente::all()->pluck('nome', 'id'))
                            ->searchable()
                            ->label('Cliente'),
                        
                        */
                        TextInput::make('produto_id')
                            ->numeric()
                            ->label('Produto')
                            ->autocomplete()
                            ->autofocus()
                            ->extraInputAttributes(['tabindex' => 1])
                            ->live(debounce: 300)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $this->updated($state, $state);
                            }),

                    ]),
            ]);
    }

    public function updated($name, $value): void
    {

        if ($name === 'produto_id') {

            $produto = Produto::where('codbar', '=', $value)->first();


            $addProduto = [
                'produto_id' => $produto->id,
                'valor_venda' => $produto->valor_venda,
                'pdv_id' => '',
                'acres_desc' => 0,
                'qtd' => 1,
                'sub_total' => $produto->valor_venda * 1,
                'valor_custo_atual' => $produto->valor_compra,
            ];

            PDVs::create($addProduto);
            $this->produto_id = '';
            
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('venda_id')
            ->columns([
                Tables\Columns\TextColumn::make('venda_id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
