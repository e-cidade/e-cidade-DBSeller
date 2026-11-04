<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Models\SetorAmbulatorial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class SetoresAmbulatoriaisService
{

    /**
     * @return SetorAmbulatorial[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    public function recuperaSetores()
    {
        $unidade = $_SESSION['DB_coddepto'];
        $setor = new SetorAmbulatorial();
        return $setor->where('sd91_unidades', $unidade)->orderBy('sd91_descricao')->get();
    }
}
