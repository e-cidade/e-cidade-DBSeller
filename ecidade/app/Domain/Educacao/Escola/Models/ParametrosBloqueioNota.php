<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ParametrosBloqueioNota
 * @package App\Domain\Educacao\Escola\Models
 * @property int $ed362_codigo
 * @property int $ed362_tipobloqueio
 * @property int $ed362_prazodias
 */
class ParametrosBloqueioNota extends Model
{
    const SEM_BLOQUEIO = 1;
    const BLOQUEIO_NOTAS_PARCIAIS = 2;
    const BLOQUEIO_NOTAS_DIARIO = 3;
    const BLOQUEIO_TUDO = 4;

    protected $table = 'escola.parametrosbloqueionota';
    protected $primaryKey = 'ed362_codigo';
}
