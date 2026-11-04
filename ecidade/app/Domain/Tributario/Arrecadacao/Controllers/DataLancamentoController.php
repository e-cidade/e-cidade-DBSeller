<?php

namespace App\Domain\Tributario\Arrecadacao\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Requests\DeletaDataLancamentoDebitoRequest;
use App\Domain\Tributario\Arrecadacao\Requests\PesquisaCgmRequest;
use App\Domain\Tributario\Arrecadacao\Requests\PesquisaInscricaoMunicipalRequest;
use App\Domain\Tributario\Arrecadacao\Requests\PesquisaMatriculaRequest;
use App\Domain\Tributario\Arrecadacao\Requests\RegistroDataLancamentoDebitoRequest;
use App\Domain\Tributario\Arrecadacao\Services\DataLancamentoService;
use App\Http\Controllers\Controller;

/**
 * Classe para cuidar das datas de lancamento tributario
 * da tabela informacaodebito
 */
class DataLancamentoController extends Controller
{
    /**
     * @type DataLancamentoService
     */
    private $service;

    /**
     * Construtor da classe
     */
    public function __construct(DataLancamentoService $dataLancamentoService)
    {
        $this->service = $dataLancamentoService;
    }

    /**
     * Retorna debitos de acordo com cgm passado
     */
    public function pesquisaCgm(PesquisaCgmRequest $request)
    {
        $resultados = $this->service->pesquisaCgm($request);

        return new DBJsonResponse($resultados);
    }

    /**
     * Retorna debitos de acordo com matricula passada
     */
    public function pesquisaMatricula(PesquisaMatriculaRequest $request)
    {
        $resultados = $this->service->pesquisaMatricula($request);

        return new DBJsonResponse($resultados);
    }

    /**
     * Retorna debitos de acordo com inscricao municipal passada
     */
    public function pesquisaInscricaoMunicipal(PesquisaInscricaoMunicipalRequest $request)
    {
        $resultado = $this->service->pesquisaInscricaoMunicipal($request);

        return new DBJsonResponse($resultado);
    }

    /**
     * Edita lancamentos tributarios na tabela informacaodebito
     */
    public function editaRegistro(RegistroDataLancamentoDebitoRequest $request)
    {
        $resultado = $this->service->editaRegistro($request);

        return new DBJsonResponse($resultado);
    }

    /**
     * Inclui lancamentos tributarios na tabela informacaodebito
     */
    public function incluiRegistro(RegistroDataLancamentoDebitoRequest $request)
    {
        $resultado = $this->service->incluiRegistro($request);

        return new DBJsonResponse($resultado);
    }

    /**
     * Exclui lancamentos tributarios na tabela informacaodebito
     */
    public function excluiRegistro(DeletaDataLancamentoDebitoRequest $request)
    {
        $numpreRegistro = $request->numpre;
        $resultado = $this->service->excluiRegistro($numpreRegistro);

        return new DBJsonResponse($resultado);
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     */
    public function getLabelsLancamentoDebito()
    {
        $resultados = $this->service->getLabelsLancamentoDebito();

        return new DBJsonResponse($resultados);
    }
}
