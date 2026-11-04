<?php

namespace App\Domain\Tributario\Notificacoes\Model;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    protected $table = 'caixa.lista';
    protected $primaryKey = 'k60_codigo';
    public $timestamps = false;
    protected $fillable = [
        'k60_descr',
        'k60_tipo',
        'k60_datadeb',
        'k60_filtros',
        'k60_usuario',
        'k60_instit'
    ];
}
