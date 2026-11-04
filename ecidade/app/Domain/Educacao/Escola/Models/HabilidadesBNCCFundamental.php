<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Educacao\Escola\Repositories\HabilidadeBNCCReferencialCurricularRepository;
use Illuminate\Database\Eloquent\Model;

class HabilidadesBNCCFundamental extends Model
{
    protected $table = "escola.bnccensinofundamental";
    protected $primaryKey = 'ed148_sequencial';

    protected $appends = ['referenciaisCurricularEstadual'];
    public function getReferenciaisCurricularEstadualAttribute()
    {
        $referencial = new HabilidadeBNCCReferencialCurricularRepository();
        $filters = [
            'etapa' => explode(',', $this->ed148_etapa),
            'habilidade' => $this->ed148_codigo,
            'ano' => $this->ed148_ano,
            'objetoConhecimento' => $this->ed148_objeto_conhecimento
        ];

        return $referencial->getByFilters($filters);
    }
}
