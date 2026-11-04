<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller\Fundeb;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhProcessamentoFundeb;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\ProcessamentoFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\CalculoFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Services\Fundeb\FundebService;
use App\Http\Controllers\Controller;
use COM;
use Exception;
use Illuminate\Http\Request;

/**
 * Class RhProcessamentoFundebController
 * @package App\Domain\RecursosHumanos\Pessoal\Controller
 */
class RhProcessamentoFundebController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listProcessamentoFundeb(Request $request)
    {
        try {
            $campos = [
                'rh286_sequencial',
                'rh286_matricula',
                'rh286_ano',
                'rh286_mes',
                'rh286_cargo',
                'rh286_local_trabalho',
                'rh286_rubrica',
                'rh286_valor',
                'rh286_instituicao'
            ];

            $rhprocessamentofundeb = RhProcessamentoFundeb::where(
                'rh284_instituicao',
                db_getsession('DB_instit')
            );
            if ($request->rh286_sequencial) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_sequencial',
                    $request->rh286_sequencial
                );
            }
            if ($request->rh286_matricula) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_matricula',
                    $request->rh286_matricula
                );
            }
            if ($request->rh286_ano) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_ano',
                    $request->rh286_ano
                );
            }
            if ($request->rh286_mes) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_mes',
                    $request->rh286_mes
                );
            }
            if ($request->rh286_cargo) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_cargo',
                    $request->rh286_cargo
                );
            }
            if ($request->rh286_local_trabalho) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_local_trabalho',
                    $request->rh286_local_trabalho
                );
            }
            if ($request->rh286_rubrica) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_rubrica',
                    $request->rh286_rubrica
                );
            }
            if ($request->rh286_valor) {
                $rhprocessamentofundeb = $rhprocessamentofundeb->where(
                    'rh286_valor',
                    $request->rh286_valor
                );
            }
            return new DBJsonResponse($rhprocessamentofundeb->get($campos));
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    private $service;

    public function __construct(FundebService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ProcessamentoFundebRequest $request
     * @return DBJsonResponse
     */
    public function save(ProcessamentoFundebRequest $request)
    {
        return new DBJsonResponse(
            $this->service->salvarProcessamentoFundeb($request),
            'Processamento realizado com sucesso.'
        );
    }

    /**
     * @param RhProcessamentoFundebRequest $request
     * @return DBJsonResponse
     */
    public function calcularCota(ProcessamentoFundebRequest $request)
    {
        return new DBJsonResponse(
            $this->service->calcularCota($request),
            'Processamento realizado com sucesso.'
        );
    }
}
