<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\Relatorios\BalanceteRequest;
use App\Domain\Financeiro\Contabilidade\Requests\Relatorios\BalanceteVerificacaoRequest;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteVerificacaoInformacaoComplementarService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\RelatorioBalanceteVerificacaoService;
use App\Http\Controllers\Controller;

class BalanceteVerificacaoController extends Controller
{
    public function emitirPorComplemento(BalanceteVerificacaoRequest $request)
    {
        $service = new RelatorioBalanceteVerificacaoService();
        $service->setFiltrosRequest($request);
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete de verificação.');
    }

    public function emitirInformacaoComplementar(BalanceteRequest $request)
    {
        $service = new BalanceteVerificacaoInformacaoComplementarService();

        $service->setFiltrosArray($request->all());
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete de verificação.');
    }
}
