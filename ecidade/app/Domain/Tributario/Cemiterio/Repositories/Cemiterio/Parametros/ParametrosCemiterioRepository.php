<?php

namespace App\Domain\Tributario\Cemiterio\Repositories\Cemiterio\Parametros;

use App\Domain\Tributario\Cemiterio\Models\Cemiterio\Parametros\ParametrosCemiterioModel;

class ParametrosCemiterioRepository
{
    /**
     * Busca parametros de configuracao do modulo cemiterio
     * @param integer $iAno
     */
    public function buscaParametros($iAno)
    {
        $parametrosCemiterio = new ParametrosCemiterioModel();

        return $parametrosCemiterio::query()->where('cem36_ano', '=', $iAno)->get();
    }

    /**
     * Altera parametros de configuracao do modulo cemiterio
     * @param boolean $bOrigatoriedadeTaxaSepultamento
     * @param integer $iAno
     */
    public function alteraParametros($bOrigatoriedadeTaxaSepultamento, $iAno)
    {
        $parametrosCemiterio = new ParametrosCemiterioModel();

        $parametrosCemiterio->where('cem36_ano', '=', $iAno);
        $aAtualizacoes = [];

        $aAtualizacoes['cem36_ano'] = $iAno;

        if (isset($bOrigatoriedadeTaxaSepultamento)) {
            $aAtualizacoes['cem36_obrigatoriedadetaxasepultamento'] = $bOrigatoriedadeTaxaSepultamento;
        }

        $parametrosCemiterio->query()->where('cem36_ano', '=', $iAno)->update($aAtualizacoes);

        return;
    }
}
