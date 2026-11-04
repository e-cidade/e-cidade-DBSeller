<?php


namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Factories\BalanceteReceitaFactory;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaEcidadeService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaPlanoPadraoComplementoService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaComplementoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceteReceitaController extends Controller
{
    public function emitirBalancete(Request $request)
    {
        $service = BalanceteReceitaFactory::getService($request->get('ementario'));
        $service->setFiltrosRequest($request->all());
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete da receita.');
    }

    public function emitirPorComplemento(Request $request)
    {
        $service = new BalanceteReceitaComplementoService();
        $service->setFiltrosRequest($request->all());
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete da receita.');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function emitirPlanoPadrao(Request $request)
    {
        $service = new BalanceteReceitaPlanoPadraoComplementoService();
        $service->setFiltrosRequest($request->all());
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete da receita.');
    }
}
