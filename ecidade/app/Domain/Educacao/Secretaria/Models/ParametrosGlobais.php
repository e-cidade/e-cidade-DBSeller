<?php

namespace App\Domain\Educacao\Secretaria\Models;

use ECidade\Enum\Educacao\BNCC\TipoBaseCurricularEnum;
use Illuminate\Database\Eloquent\Model;

class ParametrosGlobais extends Model
{
    protected $table = "secretariadeeducacao.sec_parametros";
    protected $primaryKey = 'ed290_sequencial';

    protected $appends = ['isReferencialCurricularEstadual', 'tipoBaseCurricular'];

    public function getTipoBaseCurricularAttribute()
    {
        return new TipoBaseCurricularEnum($this->ed290_bncc);
    }

    public function getIsReferencialCurricularEstadualAttribute()
    {
        return $this->tipoBaseCurricular->value() === TipoBaseCurricularEnum::REFERENCIAL_CURRICULAR_ESTADUAL;
    }
}
