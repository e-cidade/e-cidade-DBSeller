<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Services;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\TipoAsse;
use App\Domain\RecursosHumanos\RH\Relatorios\Models\ConfigAssentPeriodoModel;

class ConfigAssentPeriodoService
{
    /**
     * Configuracao atraves da instit
     *
     * @param int $instit
     * @return ConfigAssentPeriodoModel | null
     */
    public function getByInstit($iInstit)
    {
        $oConfig = ConfigAssentPeriodoModel::where('rh512_instit', $iInstit)->first();
        return $oConfig;
    }

    /**
     * Salva as configuracoes
     *
     * @param object $oConfig
     * @param int $iInstit
     * @return bool
     */
    public function save($oData, $iInstit)
    {
        if (empty($oData) && !is_object($oData)) {
            throw new \Exception('Não foram informados os dados de configuracao');
        }

        $oModel = $this->getByInstit($iInstit);

        if (!$oModel) {
            $oModel = new ConfigAssentPeriodoModel;
            $oModel->rh512_instit = $iInstit;
        }

        $oModel->rh512_filtroadicionais = (bool) $oData->rh512_filtroadicionais;
        $oModel->rh512_filtroassentdepart = (bool) $oData->rh512_filtroassentdepart;
        $oModel->rh512_assentferias = json_encode($oData->rh512_assentferias);
        $oModel->save();
    }

    /**
     * Lista dos tipo de assentamentos
     *
     * @return object
     */
    public function getAssentamentos()
    {
        $oAssentamentos = TipoAsse::selectRaw("
            h12_codigo,
            h12_assent || ' - ' || h12_descr as h12_descr
        ")->get();

        return $oAssentamentos;
    }
}
