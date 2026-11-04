<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\RegenciaPeriodo;

class RegenciaPeriodoRepository extends BaseRepository
{
    protected $modelClass = RegenciaPeriodo::class;

    public function salvar($regencia, $procAvaliacao, $aulasDadas)
    {
        $regenciaPeriodo = $this->newQuery()
            ->where('ed78_i_regencia', $regencia)->where('ed78_i_procavaliacao', $procAvaliacao)->first();
        if (is_null($regenciaPeriodo)) {
            return $this->newQuery()->create([
                'ed78_i_regencia' => $regencia,
                'ed78_i_procavaliacao' => $procAvaliacao,
                'ed78_i_aulasdadas' => $aulasDadas
            ]);
        } else {
            $regenciaPeriodo->ed78_i_aulasdadas = $aulasDadas;
            return $regenciaPeriodo->save();
        }
    }
}
