<?php

namespace App\Domain\Tributario\Notificacoes\Model;

use Illuminate\Database\Eloquent\Model;

class ListaDeb extends Model
{
    protected $table = 'caixa.listadeb';
    protected $primaryKey = 'k61_codigo';
    public $timestamps = false;
    protected $fillable = [
        'k61_numpre',
        'k61_numpar'
    ];
}
