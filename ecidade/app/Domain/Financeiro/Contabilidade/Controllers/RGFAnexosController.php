<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoDoisRgfFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoUmRgfFactory;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\RREO\AnexosRGFRequest;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoCinco\AnexoCincoService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm\AnexoUmInRsService;
use App\Http\Controllers\Controller;

class RGFAnexosController extends Controller
{
    public function anexoUmInRs(AnexosRGFRequest $request)
    {
        $relatorio = new AnexoUmInRsService($request->all());
        $files = $relatorio->emitir();
        return new DBJsonResponse($files, 'Anexo I - Demonstrativo da Despesa com Pessoal - IN RS');
    }

    public function anexoUmMdf(AnexosRGFRequest $request)
    {
        $relatorio = AnexoUmRgfFactory::getServiceMdf($request->get('DB_anousu'), $request->all());
        $files = $relatorio->emitir();
        return new DBJsonResponse($files, 'Anexo I - Demonstrativo da Despesa com Pessoal - MDF');
    }

    public function anexoDois(AnexosRGFRequest $request)
    {
        $relatorio = AnexoDoisRgfFactory::getService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->emitir();
        return new DBJsonResponse($files, 'Anexo II - Demonstrativo da Dívida Consolidada Líquida');
    }

    public function anexoCinco(AnexosRGFRequest $request)
    {
        $relatorio = new AnexoCincoService($request->all());
        $files = $relatorio->emitir();
        return new DBJsonResponse($files, 'Anexo V - Demonstrativo da Disponibilidade de Caixa e dos Restos a Pagar');
    }
}
