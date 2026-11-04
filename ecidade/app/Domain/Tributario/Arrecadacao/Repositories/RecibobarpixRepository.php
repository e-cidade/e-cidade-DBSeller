<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Arrecadacao\Models\Recibobarpix;

class RecibobarpixRepository
{
    /**
     * Busca os dados com base codigo arrecadacao
     * @param $numpre
     * @param $numpar
     * @return Recibobarpix|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function getByNumpreNumpar($numpre, $numpar)
    {
        $oRecibobarpix = $this->recibobarpix->where(
            ["k00_numpre","=",$numpre],
            ["k00_numpar","=",$numpar]
        )->first();

        if (empty($oRecibobarpix)) {
            throw new \Exception("Erro ao buscar os dados na tabela recibobarpix.");
        }

        return $oRecibobarpix;
    }

    /**
     * Busca os dados com base codigo arrecadacao
     * @param $codigobarras string
     * @return Recibobarpix|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function getByCodBar($codigobarras)
    {
        $oRecibobarpix = Recibobarpix::query()
            ->where("k00_codbar", "=", $codigobarras)->first();

        if (!$oRecibobarpix) {
            return false;
        }
 
        return $oRecibobarpix;
    }
}
