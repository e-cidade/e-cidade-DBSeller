<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegimePrevidencia
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 * @property integer $rh127_sequencial
 * @property integer $rh127_descricao
 */
class RegimePrevidencia extends Model
{
    protected $table = 'pessoal.regimeprevidencia';
    protected $primaryKey = 'rh127_sequencial';
    public $timestamps = false;
}
