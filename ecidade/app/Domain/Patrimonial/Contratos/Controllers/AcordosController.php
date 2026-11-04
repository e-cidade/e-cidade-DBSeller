<?php

namespace App\Domain\Patrimonial\Contratos\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Contratos\Models\Acordo;
use App\Domain\Patrimonial\Contratos\Services\AlterarDotacaoAcordoService;
use App\Domain\Patrimonial\Contratos\Services\AcordoService;
use App\Domain\Patrimonial\Contratos\Services\EnviarEventoAcordoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcordosController extends Controller
{

    public function buscarAcordos(Request $request, AcordoService $service)
    {
        $acordos = $service->buscarAcordos($request);
        return new DBJsonResponse($acordos);
    }

    public function buscarTodosAcordos(Request $request, AcordoService $service)
    {
        $acordos = $service->buscarTodosAcordos($request);
        return new DBJsonResponse($acordos, '', 200, true, [], JSON_UNESCAPED_SLASHES);
    }

    public function buscarAcordo(Request $request, AcordoService $service)
    {
        $acordo = $service->buscarAcordo($request);
        return  new DBJsonResponse($acordo);
    }

    public function buscarPosicoes($acordo, AlterarDotacaoAcordoService $service)
    {
        $posicoes = $service->buscarPosicoes($acordo);

        return new DBJsonResponse($posicoes);
    }

    public function getItensPosicao($acordoPosicao, AlterarDotacaoAcordoService $service)
    {
        $itens = $service->getItensPosicao($acordoPosicao);
        return new DBJsonResponse($itens);
    }

    public function getDescricaoOrigens(Request $request, AlterarDotacaoAcordoService $service)
    {
        $origens = $service->getDescricaoOrigens();
        return new DBJsonResponse($origens);
    }


    public function realizarAlteracoes(Request $request, AlterarDotacaoAcordoService $service)
    {
        return new DBJsonResponse($service->realizarAlteracoes($request));
    }

    public function enviarEventoAutomatico(Request $request, EnviarEventoAcordoService $service)
    {
        return new DBJsonResponse($service->enviarEventoAutomatico($request));
    }
}
