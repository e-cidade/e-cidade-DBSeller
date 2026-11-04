<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Calculo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Gerfres
 * @property int r20_anousu
 * @property int r20_mesusu
 * @property int r20_regist
 * @property string r20_rubric
 * @property string r20_valor
 * @property string r20_pd
 * @property string r20_quant
 * @property string r20_lotac
 * @property int r20_semest
 * @property int r20_instit
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Calculo
 */
class Gerfres extends Model
{
    protected $table = 'pessoal.gerfres';
    protected $primaryKey = ['r20_anousu,', 'r20_mesusu', 'r20_regist', 'r20_rubric', 'r20_pd'];
    public $incrementing = false;
    public $timestamps = false;
}
