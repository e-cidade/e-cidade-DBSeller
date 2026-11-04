<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Rubrica;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ObservacaoRubrica
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Rubrica
 * @property int rh315_sequencial
 * @property int rh315_matricula
 * @property string rh315_rubrica
 * @property int rh315_anousu
 * @property int rh315_mesusu
 * @property string rh315_observacao
 * @property string rh315_tipo
 * @property integer rh315_instit
 */
class ObservacaoRubrica extends Model
{
    protected $table = "pessoal.rhobservacaorubricaservidor";
    protected $primaryKey = 'rh315_sequencial';
    public $timestamps = false;
    public $incrementing = true;
}
