<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AtestadoFrequenciaStatus
 * @package App\Domain\Educacao\Escola\Models
 * TODO: documentar..
 * @property int $j13_codi
 */
class AtestadoFrequenciaStatus extends Model
{
    protected $table = 'escola.atestadofrequenciastatus';
    protected $primaryKey = 'ed356_codigo';

    public $timestamps = false;
}
