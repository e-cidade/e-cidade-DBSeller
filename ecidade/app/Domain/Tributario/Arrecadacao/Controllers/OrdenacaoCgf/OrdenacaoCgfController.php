<?php

namespace App\Domain\Tributario\Arrecadacao\Controllers\OrdenacaoCgf;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Requests\AdicionarOrdenacaoCgfRequest;
use App\Domain\Tributario\Arrecadacao\Requests\ApagarOrdenacaoCgfRequest;
use App\Domain\Tributario\Arrecadacao\Requests\BuscarOrdenacoesCgfRequest;
use App\Domain\Tributario\Arrecadacao\Services\OrdenacaoCgfService;
use App\Http\Controllers\Controller;
use ECidade\Tributario\Arrecadacao\Model\OrdenacaoCgf;

class OrdenacaoCgfController extends Controller
{
    /** @var OrdenacaoCgfService */
    private $ordenacaoCgfService;

    public function __construct(OrdenacaoCgfService $ordenacaoCgfService)
    {
        $this->ordenacaoCgfService = $ordenacaoCgfService;
    }

    public function getOrdenacoesPorGrupo(BuscarOrdenacoesCgfRequest $request)
    {
        return new DBJsonResponse(
            $this->ordenacaoCgfService->getOrdenacoesPorGrupo(
                $request->cadtipo
            )
        );
    }

    public function apagarOrdenacoesPorGrupo(
        ApagarOrdenacaoCgfRequest $request
    ) {
        $this->ordenacaoCgfService->apagarOrdenacoesPorGrupo(
            $request->cadtipo
        );
    }

    public function adicionarOrdenacoes(
        AdicionarOrdenacaoCgfRequest $request
    ) {
        $this->ordenacaoCgfService->apagarOrdenacoesPorGrupo($request->cadtipo);
        foreach ($request->ordenacoes as $sequencialTipoOrdenacao) {
            $entity = new OrdenacaoCgf();
            $entity->setGrupo($request->cadtipo);
            $entity->setTipo($sequencialTipoOrdenacao);
            $this->ordenacaoCgfService->adicionarOrdenacao(
                $entity
            );
        }
    }
}
