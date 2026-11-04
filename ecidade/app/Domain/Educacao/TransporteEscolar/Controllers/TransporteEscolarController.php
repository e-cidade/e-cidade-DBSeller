<?php

namespace App\Domain\Educacao\TransporteEscolar\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\TransporteEscolar\Services\CarteiraTransporteService;
use App\Domain\Educacao\TransporteEscolar\Services\EmissorCarteiraTransportePdf;
use App\Http\Controllers\Controller;
use BusinessException;
use Illuminate\Http\Request;
use ParameterException;

class TransporteEscolarController extends Controller
{
    public function getLinhas($escola, CarteiraTransporteService $service)
    {
        $linhasTransporte = $service->getLinhas($escola);
        return new DBJsonResponse($linhasTransporte);
    }

    /**
     * @throws ParameterException
     * @throws BusinessException
     */
    public function getAlunosVinculados(Request $request, CarteiraTransporteService $service)
    {
        $oParams = $request->all();
        $oRetorno = $service->getAlunosVinculados($oParams);
        return new DBJsonResponse($oRetorno);
    }

    public function emitirCarteira(Request $request, CarteiraTransporteService $service)
    {
        $retorno = [];
        $dados = $service->getDados($request->all());
        $dados = $service->insereMascaraCPF($dados);
        $dados = $service->formataDataNascimento($dados);
        $dados = $service->formataContatos($dados);
        $retorno[] = (new EmissorCarteiraTransportePdf($dados))->emitir();
        return new DBJsonResponse($retorno);
    }
}
