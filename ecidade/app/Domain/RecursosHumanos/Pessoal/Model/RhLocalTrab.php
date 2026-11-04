<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RhLocalTrab
 * @property int rh55_instit
 * @property int rh55_codigo
 * @property string rh55_estrut
 * @property string rh55_descr
 * @property int rh55_inep
 * @property int rh55_tipolocal
 * @property string rh55_endereco
 * @property int rh55_tipoestabelecimento
 * @property int rh55_tipoinscricao
 * @property string rh55_numeroinscricao
 * @property string rh55_observacaoregistrosambientais
 * @property string rh55_lotacaotributaria
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 */
class RhLocalTrab extends Model
{
    protected $table = 'pessoal.rhlocaltrab';
    protected $primaryKey = ['rh55_codigo', 'rh55_instit'];
    public $incrementing = false;
    public $timestamps = false;
}
