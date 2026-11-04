<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\Socios;

class SociosService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, Cgm $cgm, $data)
    {
        $socios = new Socios();
        $socios->q95_cgmpri = $issBase->cgm->z01_numcgm;
        $socios->q95_numcgm = $cgm->z01_numcgm;
        $socios->q95_perc = isset($data->q95_perc) ? $data->q95_perc : null;
        $socios->q95_tipo = isset($data->q95_tipo) ? $data->q95_tipo : null;
        $socios->q95_qualificacaosocio = isset($data->q95_qualificacaosocio) ? $data->q95_qualificacaosocio : null;
        $socios->saveOrFail();

        return $socios;
    }

    /**
     * @throws \Exception
     */
    public function update(Socios $socio, $data)
    {
        $partnerInfoUpdated = Socios::where("q95_cgmpri", $socio->q95_cgmpri)
                                    ->where("q95_numcgm", $socio->q95_numcgm)
                                    ->update($data);

        if (!$partnerInfoUpdated) {
            throw new \Exception("Não foi possível atualizar as infos do sócio");
        }
    }
}
