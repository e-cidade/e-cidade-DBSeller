<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Models\MotivoAtendimento;
use Illuminate\Database\Eloquent\Collection;

class MotivosAtendimentosService
{
    /**
     * @return MotivoAtendimento[]|Collection
     */
    public function recuperaMotivosAtendimentos()
    {
        return MotivoAtendimento::all();
    }
}
