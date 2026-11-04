<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Educacao\Escola\Repositories\HabilidadeBNCCReferencialCurricularRepository;
use Illuminate\Database\Eloquent\Model;

class HabilidadesBNCCInfantil extends Model
{
    protected $table = "escola.bncceducacaoinfantil";
    protected $primaryKey = 'ed147_sequencial';

    public function getReferenciaisCurricularEstadualAttribute()
    {
        $referencial = new HabilidadeBNCCReferencialCurricularRepository();
        $filters = [
            'habilidade' => $this->ed147_codigo,
            'ano' => $this->ed147_ano
        ];

        return $referencial->getByFilters($filters);
    }
}
