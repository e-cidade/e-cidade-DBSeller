<?php

namespace App\Domain\Financeiro\Tesouraria\Controller;

use App\Domain\Financeiro\Tesouraria\Services\ImplantacaoConciliacaoBancariaService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use stdClass;

class ImplantacaoConciliacaoBancariaController extends Controller
{

    private $service;

    public function __construct(ImplantacaoConciliacaoBancariaService $service)
    {
        $this->service = $service;
    }

    public function contasPendentes(Request $request)
    {
        $this->service->setAno($request->DB_anousu);
        $this->service->setInstituicao($request->DB_instit);
        $aRegistros = $this->service->getRegistrosImplantar();
        return new DBJsonResponse($aRegistros);
    }

    public function processarImplantacao(Request $request)
    {
        $dados = \JSON::create()->parse(str_replace('\\', "", $request->linhasContas));
        $this->service->setAno($request->DB_anousu);
        $this->service->setInstituicao($request->DB_instit);
        $this->service->setUsuario($request->DB_id_usuario);
        $this->service->processarImplantacao($dados);
        return new DBJsonResponse([], "Processamento Realizado Com Sucesso.");
    }
}
