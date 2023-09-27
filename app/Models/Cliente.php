<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Return_;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome'
    ];

    public function PDV() {
       return  $this->hasMany(PDV::class);
    }
}
