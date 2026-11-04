<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller\Fundeb;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\ParametrosFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Services\Fundeb\FundebService;
use App\Http\Controllers\Controller;

/**
 * Class RhParametrosFundebController
 * @package App\Domain\RecursosHumanos\Pessoal\Controller
 */

class RhParametrosFundebController extends Controller
{
    private $service;

    public function __construct(FundebService $service)
    {
        $this->service = $service;
    }

    /**
     * @return DBJsonResponse
     */
    public function showCargos()
    {
        return new DBJsonResponse($this->service->findParametrosCargos(), 'Parâmetros Fundeb encontrado.');
    }

    /**
     * @return DBJsonResponse
     */
    public function showFuncoes()
    {
        return new DBJsonResponse($this->service->findParametrosFuncoes(), 'Parâmetros Fundeb encontrado!');
    }

    /**
     * @return DBJsonResponse
     */
    public function showLocais()
    {
        return new DBJsonResponse($this->service->findParametrosLocais(), 'Parâmetros Fundeb encontrado!');
    }

    /**
     * @return DBJsonResponse
     */
    public function showAssentamentos()
    {
        return new DBJsonResponse($this->service->findParametrosAssentamentos(), 'Parâmetros Fundeb encontrado!');
    }

    /**
     * @return DBJsonResponse
     */
    public function showRubricaAbatimento()
    {
        return new DBJsonResponse($this->service->findParametroRubricaAbatimento(), 'Parâmetro Fundeb encontrado!');
    }

    /**
     * @param ParametrosFundebRequest $request
     * @return DBJsonResponse
     */
    public function save(ParametrosFundebRequest $request)
    {
        return new DBJsonResponse($this->service->salvarParametrosFundeb($request), 'Parâmetro salvo com sucesso!');
    }

    public function deleteCargo($id)
    {
        $this->service->removerParametroCargo($id);
        return new DBJsonResponse([], 'Parâmetro Cargo removido com sucesso!');
    }

    public function deleteFuncao($id)
    {
        $this->service->removerParametroFuncao($id);
        return new DBJsonResponse([], 'Parâmetro Função removido com sucesso!');
    }

    public function deleteLocal($local)
    {
        $this->service->removerParametroLocal($local);
        return new DBJsonResponse([], 'Parâmetro Local de Trabalho removido com sucesso!');
    }

    public function deleteAssentamento($assentamento)
    {
        $this->service->removerParametroAssentamento($assentamento);
        return new DBJsonResponse([], 'Parâmetro Assentamento removido com sucesso!');
    }

    public function deleteRubricaAbatimento($rubrica)
    {
        $this->service->removerParametroRubricaAbatimento($rubrica);
        return new DBJsonResponse([], 'Parâmetro Rubrica de Abatimento removido com sucesso!');
    }
}
