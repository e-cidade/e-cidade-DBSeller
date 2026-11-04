<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn07_codigo
 * @property $pn07_habilitado
 * @property $pn07_instit
 * @property $pn07_usuario
 */
class VerificaUnidadeRequisitante extends Model
{
    protected $primaryKey = 'pn07_codigo';
    protected $table = 'verificaunidaderequisitante';
    public $timestamps = false;
    protected $fillable = [
        'pn07_codigo',
        'pn07_habilitado',
        'pn07_instit',
        'pn07_usuario'
    ];
}
