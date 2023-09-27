<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PDV extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        'venda_id',
        'valor_venda',
        'qtd',
        'acres_desc',
        'sub_total',
        'valor_custo_atual',
        'total_custo_atual'
    ];

    public function Produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function Venda() {
        return $this->belongsTo(Venda::class);
    }
}
