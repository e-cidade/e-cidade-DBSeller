<?php

namespace App\Domain\Financeiro\Planejamento\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseCalculoProjecao
 * @package App\Domain\Financeiro\Planejamento\Models
 * @property integer $pl31_codigo
 * @property integer $pl31_planejamento_id
 * @property integer $pl31_origem
 * @property string $pl31_basecalculo
 */
class BaseCalculoProjecao extends Model
{
    protected $table = 'planejamento.basecalculoprojecao';

    protected $primaryKey = 'pl31_codigo';
}
