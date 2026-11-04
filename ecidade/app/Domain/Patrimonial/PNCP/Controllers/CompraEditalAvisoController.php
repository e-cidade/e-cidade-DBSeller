<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Compras\Services\SolicitacaoService;
use App\Domain\Patrimonial\PNCP\Enum\AmparoLegalEnum;
use App\Domain\Patrimonial\PNCP\Enum\InstrumentoConvocatorioEnum;
use App\Domain\Patrimonial\PNCP\Enum\ModoDisputaEnum;
use App\Domain\Patrimonial\PNCP\Models\ComprasPncp;
use App\Domain\Patrimonial\PNCP\Requests\AlteracaoCEARequest;
use App\Domain\Patrimonial\PNCP\Requests\BuscaCompraRequest;
use App\Domain\Patrimonial\PNCP\Requests\ImportacaoCEARequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoCEARequest;
use App\Domain\Patrimonial\PNCP\Services\CompraEditalAvisoService;
use App\Domain\Patrimonial\PNCP\Services\ItensLicitacaoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class CompraEditalAvisoController extends Controller
{
    /**
     * @param Request $request
     * @param CompraEditalAvisoService $service
     * @return void
     */
    public function incluirDocumento(Request $request, CompraEditalAvisoService $service)
    {
        $service->inserirDocumento($request);
    }
    public function buscarAmparosLegais(Request $request)
    {
        $modalidade = $request->get('modalidadeCompra');
        $instrumentoConvocatorio = $request->get('instrumentoConvocatorio');
        $amparosLegais = AmparoLegalEnum::getAmparosLegais($modalidade, $instrumentoConvocatorio);
        return new DBJsonResponse($amparosLegais);
    }

    public function buscarInstrumentoConvocatorio(Request $request)
    {
        $intrumentoConvocatorio = InstrumentoConvocatorioEnum::getInstrumentoConvocatorio(
            $request->get('modalidadeCompra')
        );
        return new DBJsonResponse($intrumentoConvocatorio);
    }

    public function buscarModoDisputa(Request $request)
    {
        $modosDisputas = ModoDisputaEnum::getModoDisputa($request->get('instrumentoConvocatorio'));
        return new DBJsonResponse($modosDisputas);
    }

    /**
     * @param Request $request
     * @param ItensLicitacaoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarLicitacao(Request $request, ItensLicitacaoService $service)
    {
        $licitacao = $service->buscarLicitacao($request);
        return new DBJsonResponse($licitacao);
    }

    /**
     * @param Request $request
     * @param SolicitacaoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarSolicitacao(Request $request, SolicitacaoService $service)
    {
        if (empty($request->pc10_numero)) {
            throw new Exception('Número da solicitação não informado.');
        }

        $solicitacao = $service->buscar($request)->first();
        return new DBJsonResponse($solicitacao);
    }

    public function incluirCompraEditalAviso(InclusaoCEARequest $request, CompraEditalAvisoService $service)
    {
        $response = $service->incluirCompra($request);
        return new DBJsonResponse($response);
    }

    public function alterarCompraEditalAviso(AlteracaoCEARequest $request, CompraEditalAvisoService $service)
    {
        $service->alterarCompra($request);
        return new DBJsonResponse([], '');
    }

    public function importarCompraEditalAviso(ImportacaoCEARequest $request, CompraEditalAvisoService $service)
    {
        $service->importarCompra($request);
        return new DBJsonResponse([], 'Compra Importada com sucesso!');
    }

    public function buscarEditais(Request $request, CompraEditalAvisoService $service)
    {
        $response = $service->buscarEditais($request->get('licitacao'));
        return new DBJsonResponse($response);
    }

    public function incluirRespostaItem(Request $request, CompraEditalAvisoService $service)
    {
        $response = $service->incluirResultadoItem($request, $request->cnpj, $request->itensCompra);
        return new DBJsonResponse([], $response);
    }

    public function buscarCompras(Request $request)
    {
        $response = ComprasPncp::where('pn03_instituicao', $request->get('DB_instit'))
            ->join('unidadespncp', 'pn02_unidade', 'pn03_unidade')
            ->leftJoin('liclicita', 'l20_codigo', 'pn03_liclicita')
            ->leftJoin('cflicita', 'l20_codtipocom', 'l03_codigo')
            ->leftjoin('licsituacao', 'l08_sequencial', 'l20_licsituacao')
            ->get();
        return new DBJsonResponse($response);
    }

    public function buscarCompra(BuscaCompraRequest $request, CompraEditalAvisoService $service)
    {
        $response = $service->buscarCompra($request);
        return new DBJsonResponse($response);
    }

    public static function buscarDocumentos(Request $request, CompraEditalAvisoService $service)
    {
        $response = $service->buscarDocumentos($request);
        return new DBJsonResponse($response);
    }
    public function excluirCompra(Request $request, CompraEditalAvisoService $service)
    {
        $response = $service->excluirCompra($request->get('cnpj'), $request->get('codigoCompra'));
        return new DBJsonResponse([], $response);
    }

    /**
     * @param Request $request
     * @param CompraEditalAvisoService $service
     * @return void
     */
    public function excluirDocumento(Request $request, CompraEditalAvisoService $service)
    {
        $service->excluirDocumentoCompra($request);
    }
}
