<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
            'codbar',
            'nome',
            'estoque',
            'valor_compra',
            'lucratividade',
            'valor_venda',
            'total_compra',
            'total_venda',
            'total_lucratividade'
    ];

    public function PDV() {
        return $this->hasMany(PDV::class);
    }
    
}
