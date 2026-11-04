<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Ponto;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PontoFs
 * @property int r10_anousu
 * @property int r10_mesusu
 * @property int r10_regist
 * @property string r10_rubric
 * @property string r10_valor
 * @property string r10_quant
 * @property string r10_lotac
 * @property string r10_datlim
 * @property int r10_instit
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Ponto
 */
class PontoFs extends Model
{
    protected $table = 'pessoal.pontofs';
    protected $primaryKey = ['r10_anousu,', 'r10_mesusu', 'r10_regist', 'r10_rubric'];
    public $incrementing = false;
    public $timestamps = false;
}
