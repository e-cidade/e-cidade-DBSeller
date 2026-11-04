<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Calculo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Gerfsal
 * @property int r14_instit
 * @property integer r09_anousu
 * @property integer r09_mesusu
 * @property string r09_base
 * @property string r09_rubric
 * @property integer r09_instit

 * @package App\Domain\RecursosHumanos\Pessoal\Model\Calculo
 */
class Basesr extends Model
{
    protected $table = 'pessoal.basesr';
    protected $primaryKey = ['r09_anousu', 'r09_mesusu', 'r09_base', 'r09_rubric', 'r09_instit'];
    public $incrementing = false;
    public $timestamps = false;
}
