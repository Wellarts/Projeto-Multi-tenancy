<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoVenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        'valor_venda',
        'qtd',
        'acres_desc',
        'sub_total',
        'valor_custo_atual',
        'total_custo_atual'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    
}
