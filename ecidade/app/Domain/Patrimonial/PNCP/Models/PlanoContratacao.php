<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn05_codigo
 * @property $pn05_unidade
 * @property $pn05_ano
 * @property $pn05_instituicao
 * @property $pn05_status
 * @property $pn05_itens
 * @property $pn05_datacadastro
 */
class PlanoContratacao extends Model
{
    protected $primaryKey = 'pn05_codigo';
    protected $table = 'planocontratacao';
    public $timestamps = false;
    protected $fillable = [
        'pn05_codigo',
        'pn05_unidade',
        'pn05_ano',
        'pn05_instituicao',
        'pn05_status',
        'pn05_itens',
        'pn05_datacadastro',
    ];
}
