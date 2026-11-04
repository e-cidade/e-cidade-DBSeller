<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Configuracao\RelarorioLegal\Resources\RelatoriosLegaisResource;
use App\Domain\Configuracao\RelarorioLegal\Services\RelatoriosLegaisService;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Factories\VersoesRelatoriosLegaisMscFactory;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\LrfEmitirRequest;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\EmiteControleVersaoService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\LinhasRelatorioLegal;
use App\Http\Controllers\Controller;
use App\Jobs\Financeiro\Contabilidade\EmissaoLrfJob;

class AnexosLrfController extends Controller
{
    public function versoesAnexo($anexo, $tipo)
    {
        $versoes = VersoesRelatoriosLegaisMscFactory::getVersoes($tipo, $anexo);

        $service = new RelatoriosLegaisService();
        $dados = $service->getByFilters(['codigos' => $versoes, 'with' => ['periodos']]);

        return new DBJsonResponse(RelatoriosLegaisResource::toArray($dados), 'Versões disponíveis');
    }

    public function linhasManuais($codigo, $tipo)
    {
        $servico = new LinhasRelatorioLegal($codigo, $tipo);
        return new DBJsonResponse($servico->getLinhasManuais(), 'Linhas Manuais');
    }

    public function emitir(LrfEmitirRequest $request)
    {
        $service = new EmiteControleVersaoService($request->all());
        $service->execute();

        $msg = 'A emissão do relatório esta sendo processada. ';
        $msg .= 'Você recebera uma notificação quando o relatório estiver pronto.';
        return new DBJsonResponse([], $msg);
    }

    public function emitirAgora(LrfEmitirRequest $request)
    {
        $service = VersoesRelatoriosLegaisMscFactory::getService($request->all());
        $arquivos = $service->emitir();

        return new DBJsonResponse($arquivos, $service->getNome());
    }

    /**
     * @param string $tipo // RREO / RGF
     * @param integer $codigo código do anexo
     * @param integer $consolida 1 se consolida, 0 se não consolida
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function viewAnexo($tipo, $codigo, $consolida)
    {
        return view(
            'financeiro.contabilidade.relatorios.lrf.emissao',
            [
                'tipo' => strtoupper($tipo), // RREO / RGF
                'anexo' => $codigo,
                'instituicao' => session('DB_instit'),
                'exercicio' => session('DB_anousu'),
                'login' => session('DB_login'),
                'consolidado' => $consolida
            ]
        );
    }
}
