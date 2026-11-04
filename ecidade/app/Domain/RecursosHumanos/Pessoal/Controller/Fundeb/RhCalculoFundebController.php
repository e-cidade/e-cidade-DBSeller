<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller\Fundeb;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhCalculoFundeb;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\CalculoFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Services\Fundeb\FundebService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

/**
 * Class RhCalculoFundebController
 * @package App\Domain\RecursosHumanos\Pessoal\Controller
 */

class RhCalculoFundebController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listCalculoFundeb(Request $request)
    {
        try {
            $campos = ['rh285_ano', 'rh285_mes', 'rh285_valor', 'rh285_instituicao'];
            if ($request->rh285_ano) {
                $rhcalculofundeb = RhCalculoFundeb::where(
                    'rh285_ano',
                    $request->rh285_ano
                )->get($campos);
            } elseif ($request->rh285_mes) {
                $rhcalculofundeb = RhCalculoFundeb::where(
                    'rh285_mes',
                    $request->rh285_mes
                )->get($campos);
            } else {
                $rhcalculofundeb = RhCalculoFundeb::all($campos);
            }
            $rhcalculofundeb = RhCalculoFundeb::where(
                'rh285_instituicao',
                db_getsession('DB_instit')
            )->get($campos);
            return new DBJsonResponse($rhcalculofundeb);
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
     * @param CalculoFundebRequest $request
     * @return DBJsonResponse
     */
    public function save(CalculoFundebRequest $request)
    {
        return new DBJsonResponse($this->service->salvarValoresFundeb($request), 'Valor Fundeb salvo com sucesso!');
    }
}
