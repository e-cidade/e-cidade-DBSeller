<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CriterioTransposicao
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed351_codigo
 * @property integer $ed351_descricao
 */
class CriterioTransposicao extends Model
{
    protected $table = "escola.criteriostransposicao";
    protected $primaryKey = 'ed351_codigo';
    public $timestamps = false;
}
