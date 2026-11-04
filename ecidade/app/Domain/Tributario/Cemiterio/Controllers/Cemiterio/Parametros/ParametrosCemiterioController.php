<?php

namespace App\Domain\Tributario\Cemiterio\Controllers\Cemiterio\Parametros;

use App\Domain\Tributario\Cemiterio\Requests\Cemiterio\Parametros\AlteraParametrosCemiterioRequest;
use App\Domain\Tributario\Cemiterio\Requests\Cemiterio\Parametros\BuscaParametrosCemiterioRequest;
use App\Domain\Tributario\Cemiterio\Services\Cemiterio\Parametros\ParametrosCemiterioService;
use App\Http\Controllers\Controller;

class ParametrosCemiterioController extends Controller
{
    /**
     * @var ParametrosCemiterioService
     */
    private $parametrosCemiterioService;

    public function __construct(ParametrosCemiterioService $parametrosCemiterioService)
    {
        $this->parametrosCemiterioService = $parametrosCemiterioService;
    }

    public function buscaParametros(BuscaParametrosCemiterioRequest $request)
    {
        $ano = $request['ano'];
        return $this->parametrosCemiterioService->buscaParametros($ano);
    }

    public function alteraParametros(AlteraParametrosCemiterioRequest $request)
    {
        $ano = $request['ano'];
        $obrigatoriedadeTaxaSepultamento = $request['obrigatoriedadeTaxaSepultamento'];

        return $this->parametrosCemiterioService->alteraParametros($obrigatoriedadeTaxaSepultamento, $ano);
    }
}
