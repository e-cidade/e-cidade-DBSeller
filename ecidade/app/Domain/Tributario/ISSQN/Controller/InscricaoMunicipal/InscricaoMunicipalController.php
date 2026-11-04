<?php

namespace App\Domain\Tributario\ISSQN\Controller\InscricaoMunicipal;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal\BuscarInfoSalaoParceiroRequest;
use App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal\BuscarInscricoesMunicipaisRequest;
use App\Domain\Tributario\ISSQN\Services\InscricaoMunicipal\InscricaoMunicipalService;
use App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal\BuscaRiscoInscricaoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InscricaoMunicipalController extends Controller
{
    private $service;

    /**
     * Construtor da classe
     *
     * @return Void
     */
    public function __construct(InscricaoMunicipalService $ordemServicoService)
    {
        $this->service = $ordemServicoService;
    }

    /**
     * Busca uma lista de inscricoes municipais de acordo com os parametros passados
     *
     * @return \Illuminate\Http\Response
     */
    public function getInscricoes(BuscarInscricoesMunicipaisRequest $request)
    {
        $resultados = $this->service->getInscricoes($request);

        return new DBJsonResponse($resultados);
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     */
    public function getLabelsInscricoes()
    {
        $resultados = $this->service->getLabelsInscricoes();

        return new DBJsonResponse($resultados);
    }

    public function getHistRiscoInscr(BuscaRiscoInscricaoRequest $request)
    {
        return $this->service->getHistRiscoInscr($request->q201_inscr);
    }

    public function getInscrDispSalaoParc(BuscarInfoSalaoParceiroRequest $request)
    {
        $resultados = $this->service->getInscrDispSalaoParc($request);

        return new DBJsonResponse($resultados);
    }

    public function getDispParc(BuscarInfoSalaoParceiroRequest $request)
    {
        $resultados = $this->service->getDispParc($request);

        return new DBJsonResponse($resultados);
    }

    public function getSetorFiscal(Request $request)
    {
        $this->validate($request, [
            "descricao" => "string",
            "sequencial" => "integer"
        ]);

        $dados = (object) $request->all();

        try {
            $setorFiscalFiltrados = $this->service->getSetorFiscal($dados);
            return new DBJsonResponse($setorFiscalFiltrados);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno ao buscar setores fiscais.', 500);
        }
    }
}
