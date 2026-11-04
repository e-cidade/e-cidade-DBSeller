<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\Etapa;

class EtapaRepository extends BaseRepository
{
    protected $modelClass = Etapa::class;

    public function get($codigo)
    {
        return $this->newQuery()->find($codigo);
    }
    public function getByRegimeMatricula($regime)
    {
        return $this->newQuery()
            ->join('serieregimemat', 'ed223_i_serie', '=', 'ed11_i_codigo')
            ->join('regimemat', 'ed218_i_codigo', '=', 'ed223_i_regimemat')
            ->where('ed223_i_regimemat', $regime)->get();
    }

    public function getEtapasNoIntervalo($etapaI, $etapaF)
    {
        $inicial = $this->newQuery()->find($etapaI);
        $final = $this->newQuery()->find($etapaF);
        $etapasNoIntervalo = $this->newQuery()->where('ed11_i_ensino', $inicial->ed11_i_ensino)
            ->whereBetween('ed11_i_sequencia', [$inicial->ed11_i_sequencia, $final->ed11_i_sequencia])->get();

        return $etapasNoIntervalo;
    }

    public function getEtapasFaseAberta($codigoFase)
    {
        return $this->newQuery()
            ->join('ensino', 'ed10_i_codigo', '=', 'ed11_i_ensino')
            ->join('plugins.ciclosensino', 'mo14_ensino', '=', 'ed10_i_codigo')
            ->join('plugins.ciclos', 'mo09_codigo', '=', 'mo14_ciclo')
            ->join('plugins.fase', 'mo04_ciclo', '=', 'mo09_codigo')
            ->where('mo04_encerrada', false)
            ->when(!empty($codigoFase), function ($query) use ($codigoFase) {
                return $query->where('mo04_codigo', $codigoFase);
            })
            ->orderByRaw('mo04_codigo, ed10_c_descr, ed11_i_sequencia')
            ->get();
    }
}
