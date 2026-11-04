<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RhRubricas
 * @property string rh27_rubric
 * @property string rh27_descr
 * @property double rh27_quant
 * @property string rh27_cond2
 * @property string rh27_cond3
 * @property string rh27_form
 * @property string rh27_form2
 * @property string rh27_form3
 * @property string rh27_formq
 * @property int rh27_calc1
 * @property int rh27_calc2
 * @property bool rh27_calc3
 * @property int rh27_tipo
 * @property bool rh27_limdat
 * @property bool rh27_presta
 * @property bool rh27_calcp
 * @property bool rh27_propq
 * @property bool rh27_propi
 * @property string rh27_obs
 * @property int rh27_instit
 * @property bool rh27_ativo
 * @property int rh27_pd
 * @property double rh27_valorpadrao
 * @property double rh27_quantidadepadrao
 * @property bool rh27_complementarautomatica
 * @property int rh27_rhfundamentacaolegal
 * @property double rh27_valorlimite
 * @property double rh27_quantidadelimite
 * @property string rh27_tipobloqueio
 * @property bool rh27_periodolancamento
 * @property int rh27_previdenciacomplementar
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 */
class RhRubricas extends Model
{
    protected $table = 'pessoal.rhrubricas';
    protected $primaryKey = ['rh27_rubric', 'rh27_instit'];
    public $incrementing = false;
    public $timestamps = false;
}
