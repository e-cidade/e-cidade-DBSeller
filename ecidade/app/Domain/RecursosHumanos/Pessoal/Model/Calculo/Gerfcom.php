<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Calculo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Gerfcom
 * @property int r48_anousu
 * @property int r48_mesusu
 * @property int r48_regist
 * @property string r48_rubric
 * @property string r48_valor
 * @property string r48_pd
 * @property string r48_quant
 * @property string r48_lotac
 * @property int r48_semest
 * @property int r48_instit
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Calculo
 */
class Gerfcom extends Model
{
    protected $table = 'pessoal.gerfcom';
    
    protected $primaryKey = ['r48_anousu,', 'r48_mesusu', 'r48_regist', 'r48_rubric', 'r48_pd'];
    public $incrementing = false;
    public $timestamps = false;
}
