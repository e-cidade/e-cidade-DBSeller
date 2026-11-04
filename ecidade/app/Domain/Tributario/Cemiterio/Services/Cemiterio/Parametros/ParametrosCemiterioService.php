<?php

namespace App\Domain\Tributario\Cemiterio\Services\Cemiterio\Parametros;

use App\Domain\Tributario\Cemiterio\Repositories\Cemiterio\Parametros\ParametrosCemiterioRepository;

class ParametrosCemiterioService
{
    /**
     * @var ParametrosCemiterioRepository
     */
    private $parametrosCemiterioRepository;

    public function __construct(ParametrosCemiterioRepository $parametrosCemiterioRepository)
    {
        $this->parametrosCemiterioRepository = $parametrosCemiterioRepository;
    }

    /**
     * Busca parametros de configuracao do modulo cemiterio
     * @param integer $iAno
     */
    public function buscaParametros($iAno)
    {
        return $this->parametrosCemiterioRepository->buscaParametros($iAno);
    }

    /**
     * Altera parametros de configuracao do modulo cemiterio
     * @param boolean $bOrigatoriedadeTaxaSepultamento
     * @param integer $iAno
     */
    public function alteraParametros($obrigatoriedadeTaxaSepultamento, $iAno)
    {
        return $this->parametrosCemiterioRepository->alteraParametros($obrigatoriedadeTaxaSepultamento, $iAno);
    }
}
