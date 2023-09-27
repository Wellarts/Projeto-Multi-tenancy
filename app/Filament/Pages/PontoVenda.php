<?php

namespace App\Filament\Pages;

//use App\Models\PontoVenda;
use App\Models\Produto;
use Filament\Pages\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;


class PontoVenda extends Component implements HasForms, HasTable
{

    use InteractsWithTable;

    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.ponto-venda';

    public ?array $data = [];

    public $produto_id;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('produto_id')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state) {
                        $this->updated($state, $state);
                    }),

            ]);
    }

    public function updated($name, $value): void
    {
        

        if ($name === 'produto_id') {

            $produto = Produto::where('codbar', '=', $value)->first();

            $addProduto = [
                'produto_id' => $value,
                'valor_venda' => $produto->valor_venda,
                'qtd' => 1,
                'sub_total' => $produto->valor_venda * 1,
            ];

            PontoVenda::create($addProduto);
        }
    } 
  /*  
    protected function getTableQuery(): Builder
    {

        return PontoVendas::query();
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('produto.nome'),
            TextColumn::make('qtd')
                ->label('Quantidade'),
            TextColumn::make('valor_venda')
                ->label('Valor Unitário')
                ->money('BRL'),
            TextColumn::make('sub_total')
                ->label('Sub-Total')
                ->money('BRL'),
        ];
    }   */
 
    public function table(Table $table): Table
    {
        return $table
            ->query(PontoVenda::query())
            ->columns([
                TextColumn::make('produto_id'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    } 

    
}
