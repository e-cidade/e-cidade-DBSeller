<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssBaseNumeracao;
use Carbon\Carbon;

class IssBaseService
{
    /**
     * @throws \Throwable
     */
    public function save(Cgm $cgm, $data)
    {
        $issBase = new IssBase();
        $issBase->q02_inscr = $this->nextSequence();
        $issBase->q02_numcgm = $cgm->z01_numcgm;
        $issBase->q02_memo = isset($data->q02_memo) ? $data->q02_memo : null;
        $issBase->q02_tiplic = isset($data->q02_tiplic) ? $data->q02_tiplic : null;
        $issBase->q02_regjuc = isset($data->q02_regjuc) ? $data->q02_regjuc : null;
        $issBase->q02_inscmu = isset($data->q02_inscmu) ? $data->q02_inscmu : null;
        $issBase->q02_obs = isset($data->q02_obs) ? $data->q02_obs : null;
        $issBase->q02_dtcada = Carbon::now()->format("Y-m-d");
        $issBase->q02_dtinic = isset($data->q02_dtinic) ? $data->q02_dtinic : null;
        $issBase->q02_dtbaix = isset($data->q02_dtbaix) ? $data->q02_dtbaix : null;
        $issBase->q02_capit = isset($data->q02_capit) ? $data->q02_capit : null;
        $issBase->q02_cep = isset($data->q02_cep) ? $data->q02_cep : $cgm->z01_cep;
        $issBase->q02_dtjunta = isset($data->q02_dtjunta) ? $data->q02_dtjunta : null;
        $issBase->q02_ultalt = Carbon::now()->format("Y-m-d");
        $issBase->q02_dtalt = Carbon::now()->format("Y-m-d");
        $issBase->q02_formalocalvara = isset($data->q02_formalocalvara) ? $data->q02_formalocalvara : null;
        $issBase->q02_protocolojuntacomercial = isset($data->q02_protocolojuntacomercial)
                                                    ?
                                                $data->q02_protocolojuntacomercial
                                                    :
                                                null;
        $issBase->saveOrFail();

        return $issBase;
    }



    /**
     * @throws \Throwable
     */
    private function nextSequence()
    {
        $issBaseNumeracao = IssBaseNumeracao::first();
        $issBaseNumeracao->q133_numeracaoatual = $issBaseNumeracao->q133_numeracaoatual + 1;
        $issBaseNumeracao->saveOrFail();

        return $issBaseNumeracao->q133_numeracaoatual;
    }
}
