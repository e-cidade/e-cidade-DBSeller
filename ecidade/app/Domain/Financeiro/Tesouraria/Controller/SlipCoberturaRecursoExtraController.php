<?php

namespace App\Domain\Financeiro\Tesouraria\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Tesouraria\Requests\PeriodoSlipRequest;
use App\Domain\Financeiro\Tesouraria\Services\RelatorioListaOperacoesService;
use App\Domain\Financeiro\Tesouraria\Services\SlipCoberturaRecursoExtraService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlipCoberturaRecursoExtraController extends Controller
{
    /**
     * @var SlipCoberturaRecursoExtraService
     */
    protected $service;

    /**
     * @param SlipCoberturaRecursoExtraService $service
     */
    public function __construct(SlipCoberturaRecursoExtraService $service)
    {
        $this->service = $service;
    }
    /**
     * Apropriação de retenção
     * @param PeriodoSlipRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function apropriacaoRetencao(PeriodoSlipRequest $request)
    {
        $dados = $this->service->getAproriacoesRetencoes(
            session('DB_instit'),
            $request->get('tipo'),
            $request->get('situacao'),
            $request->get('dataInicial'),
            $request->get('dataFinal')
        );
        return new DBJsonResponse($dados, 'Registros encontrados');
    }

    public function apropriacaoReceitaExtra(PeriodoSlipRequest $request)
    {
        $dados = $this->service->getapropriacaoReceitaExtra(
            session('DB_instit'),
            $request->get('dataInicial'),
            $request->get('dataFinal')
        );
        return new DBJsonResponse($dados, 'Registros encontrados');
    }

    public function preparados(Request $request)
    {
        $service = new RelatorioListaOperacoesService();

        $service->setPeriodo($request->get('dataInicial'), $request->get('dataFinal'));
        return new DBJsonResponse([], 'Registros encontrados');
    }

    public function gerar(Request $request)
    {
        $service = new SlipCoberturaRecursoExtraService();

        $slipsGerados = $service->gerarSlips(
            $request->get('dados'),
            $request->get('dataInicial'),
            $request->get('dataFinal')
        );

        $msg = sprintf('Slips gerados com sucesso. Slips: %s', implode(', ', $slipsGerados));
        return new DBJsonResponse($slipsGerados, $msg);
    }
}
