<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Fundeb;

use App\Domain\RecursosHumanos\Pessoal\Model\RhFuncao;
use App\Domain\RecursosHumanos\Pessoal\Model\RhCargo;
use App\Domain\RecursosHumanos\Pessoal\Model\RhLocalTrab;
use App\Domain\RecursosHumanos\Pessoal\Model\RhRubricas;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\TipoAsse;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RhParametrosFundeb
 * @property int rh284_sequencial
 * @property int rh284_tipo
 * @property int rh284_cargo
 * @property int rh284_funcao
 * @property int rh284_local_trabalho
 * @property int rh284_assentamento
 * @property int rh284_rubrica_abatimento
 * @property int rh284_instituicao
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Fundeb
 */
class RhParametrosFundeb extends Model
{
    protected $table = 'pessoal.rhparametrosfundeb';
    protected $primaryKey = ['rh284_sequencial'];
    public $incrementing = false;
    public $timestamps = false;

    public function descricaoCargo()
    {
        return $this->hasOne(RhFuncao::class, 'rh37_funcao', 'rh284_cargo');
    }

    public function descricaoFuncao()
    {
        return $this->hasOne(RhCargo::class, 'rh04_codigo', 'rh284_funcao');
    }

    public function descricaoLocal()
    {
        return $this->hasOne(RhLocalTrab::class, 'rh55_codigo', 'rh284_local_trabalho');
    }

    public function descricaoAssentamento()
    {
        return $this->hasOne(TipoAsse::class, 'h12_codigo', 'rh284_assentamento');
    }

    public function descricaoRubricaAbatimento()
    {
        return $this->hasOne(RhRubricas::class, 'rh27_rubric', 'rh284_rubrica_abatimento');
    }
}
