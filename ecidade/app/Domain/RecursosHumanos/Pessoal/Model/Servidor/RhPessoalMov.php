<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Servidor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Gerfres
 * @property integer rh02_instit
 * @property integer rh02_seqpes
 * @property integer rh02_anousu
 * @property integer rh02_mesusu
 * @property integer rh02_regist
 * @property integer rh02_codreg
 * @property string rh02_tipsal
 * @property string rh02_folha
 * @property integer rh02_fpagto
 * @property integer rh02_tbprev
 * @property double rh02_hrsmen
 * @property double rh02_hrssem
 * @property string rh02_ocorre
 * @property boolean rh02_equip
 * @property integer rh02_tpcont
 * @property integer rh02_vincrais
 * @property double rh02_salari
 * @property integer rh02_lota
 * @property integer rh02_funcao
 * @property string rh02_rhtipoapos
 * @property date rh02_validadepensao
 * @property boolean rh02_deficientefisico
 * @property boolean rh02_portadormolestia
 * @property date rh02_datalaudomolestia
 * @property integer rh02_tipodeficiencia
 * @property boolean rh02_abonopermanencia
 * @property integer rh02_diasgozoferias
 * @property integer rh02_horasdiarias
 * @property string rh02_cedencia
 * @property string rh02_onus
 * @property string rh02_ressarcimento
 * @property date rh02_datacedencia
 * @property string rh02_cnpjcedencia
 * @property integer rh02_regimejornadatrabalho
 * @property date rh02_dataabonopermanencia
 * @property string rh02_descinstrumento
 * @property boolean rh02_sitpagbeneficio
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Servidor
 */
class RhPessoalMov extends Model
{
    protected $table = 'pessoal.rhpessoalmov';
    protected $primaryKey = ['rh02_seqpes', 'rh02_instit'];
    public $incrementing = false;
    public $timestamps = false;
}
