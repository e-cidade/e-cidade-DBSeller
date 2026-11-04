<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AtestadoFrequencia
 * @package App\Domain\Educacao\Escola\Models
 * TODO: documentar..
 * @property int $j13_codi
 */
class AtestadoFrequencia extends Model
{
    protected $table = 'escola.atestadofrequencia';
    protected $primaryKey = 'ed357_codigo';

    const CREATED_AT = 'ed357_created_at';
    const UPDATED_AT = 'ed357_updated_at';
    public $timestamps = true;

    // TODO: add prefixed nesse campo
    public $hidden = [
        'ed357_mes',
        'ed357_ano',
        'ed357_escola',
        'escola',
        'tableStatus',
    ];

    public $fillable = [
        'escola',
        'mes',
        'ano',
        'status',
    ];

    protected $appends = [
        'period',
        'periodLabel',
        'nomeEscola',
        'status',
        'codeEscola'
    ];

    protected $with = [
        'tableStatus',
        'correcoes',
    ];
}
