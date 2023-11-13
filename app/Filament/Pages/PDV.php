<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\PDV as PDVs;
use App\Models\Venda;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Tables\Actions\DeleteAction;


class PDV extends  page implements HasForms, HasTable
{

    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.p-d-v';

    protected static ?string $title = 'PDV';

    public ?array $data = [];

    public $produto_id;
    public $qtd;
    public $pdv;
    public $venda;

    public function mount(): void
    {
        $this->form->fill();
        $this->venda =  random_int(0000000000, 9999999999);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ponto de Venda')
                    ->columns(4)
                    ->schema([
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
                'venda_id' => $this->venda,
                'valor_venda' => $produto->valor_venda,
                'pdv_id' => '',
                'acres_desc' => 0,
                'qtd' => 1,
                'sub_total' => $produto->valor_venda * 1,
                'valor_custo_atual' => $produto->valor_compra,
            ];

            PDVs::create($addProduto);
            $this->produto_id = '';
            $this->qtd = '';
        }
    }

    protected function getTableQuery(): Builder
    {

        return PDVs::query()->where('venda_id', $this->venda);
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('produto.nome'),
            TextInputColumn::make('qtd')
                ->updateStateUsing(function (Model $record, $state) {
                    $record->sub_total = ($state * $record->valor_venda);
                    $record->qtd = $state;
                    $record->save();
                })

                ->label('Quantidade'),
            TextColumn::make('valor_venda')
                ->label('Valor Unitário')
                ->money('BRL'),
            TextInputColumn::make('acres_desc')
                ->label('Acréscimo/Desconto')
                ->updateStateUsing(function (Model $record, $state) {
                    $record->sub_total = (($record->valor_venda + $state) * $record->qtd);
                    $record->acres_desc = $state;
                    $record->save();
                })
                ->label('Acres/Desc'),
            TextColumn::make('sub_total')
                ->label('Sub-Total')
                ->money('BRL')
                ->summarize(Sum::make()->label('TOTAL')->money('BRL')),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Finalizar Venda')
                ->model(Venda::class)
                ->createAnother(false)
                ->successNotificationTitle('Venda em PDV finalizada com sucesso!')
                ->form([
                    Grid::make('3')
                        ->schema([
                            TextInput::make('venda_id_pdv')
                                ->label('Código da Venda')
                                ->disabled()
                                ->default($this->venda),
                            Select::make('cliente_id')
                                ->label('Cliente')
                                ->options(Cliente::all()->pluck('nome', 'id')->toArray()),
                            Select::make('forma_pgmto')
                                ->label('Forma de Pagamento')
                                ->options([
                                    '1' => 'Dinheiro',
                                    '2' => 'Pix',
                                    
                                ])
                                ->required(),
                            DatePicker::make('data_venda')
                                ->label('Data da Venda')
                                ->default(now()),
                            TextInput::make('valor_total')
                                ->label('Valor Total')
                                ->disabled()
                                ->default(function () {
                                    $valorTotal = PDVs::where('venda_id', $this->venda)->sum('sub_total');
                                    return $valorTotal;
                                }),
                            TextInput::make('valor_pago')
                                ->label('Valor Pago')
                                ->live(onBlur: true)
                                ->afterStateUpdated( function(Set $set, $state, $get) {
                                    $set('troco', ($state - $get('valor_total')));
                                })
                                ->autofocus(),
                            TextInput::make('troco')
                                ->disabled()
                                ->label('Troco'),   
                            Radio::make('financeiro')
                                ->columnSpan('2')
                                ->label('Lançamento Financeiro')
                                                             
                                ->options([
                                    '1' => 'Direto no Caixa',
                                    '2' => 'Conta a Receber'
                                ])->default('1')
                                
                                
                        ])

                ]) 
                    ->after(function () {
                       
                        $itensPDV = PDVs::where('venda_id', $this->venda)->get();
                        
                        foreach($itensPDV as $itens) {
                              $updProduto = Produto::find($itens->produto_id);
                              $updProduto->estoque -= $itens->qtd;
                              $updProduto->save();

                           
                        }

                        
                    })->successRedirectUrl( function() {
                        $var = 2;
                            if($var == 1) {
                               return route('filament.admin.pages.p-d-v');
                            }
                            else {
                              return  route('filament.admin.resources.produtos.index'); 
                            }
                        
                        }
                    
                    )
                        
        ];
    }

    protected function getTableActions(): array
    {
        return [
            
           DeleteAction::make('Excluir'),
  
        ];
        
    }
   
}
