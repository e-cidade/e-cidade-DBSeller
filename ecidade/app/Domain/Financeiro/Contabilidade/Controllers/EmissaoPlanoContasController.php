<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\EmissaoPadraoOrcamentarioService;
use App\Domain\Financeiro\Contabilidade\Services\EmissaoPadraoPcaspService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPcaspService;
use Illuminate\Http\Request;

class EmissaoPlanoContasController
{
    public function pcasp($tipo, $exercicio)
    {
        $service = new EmissaoPadraoPcaspService($tipo, $exercicio);
        return new DBJsonResponse($service->emitir(), 'Plano de contas');
    }

    public function orcamentario($tipoPlano, $origem, $exercicio)
    {
        $service = new EmissaoPadraoOrcamentarioService($tipoPlano, $origem, $exercicio);
        return new DBJsonResponse($service->emitir(), 'Plano de contas');
    }

    public function mapeamento(Request $request)
    {
        $service = new MapeamentoPcaspService();
        $service->filtros($request->all());
        return new DBJsonResponse($service->emitir(), 'Mapeamento das contas vinculadas.');
    }
}
