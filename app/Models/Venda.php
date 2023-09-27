<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'venda_id_pdv',
        'data_venda',
        'forma_pgmto',
        'valor_total',
        'obs',

    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pdv()
    {
        return $this->hasMany(PDV::class);
    }
}
