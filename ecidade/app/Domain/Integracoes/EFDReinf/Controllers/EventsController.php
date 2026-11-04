<?php

namespace App\Domain\Integracoes\EFDReinf\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Integracoes\EFDReinf\DTO\PeriodIsClosedInputDTO;
use App\Domain\Integracoes\EFDReinf\Request\CheckPeriodIsClosedRequest;
use App\Domain\Integracoes\EFDReinf\UseCases\CheckPeriodIsClosed;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Illuminate\Support\Facades\Log;

class EventsController extends Controller
{
    /**
     * @api {get} integracoes/efd-reinf/events/checkPeriodIsClosed 01 - Checar se periodo esta fechado
     * @apiName ChecarSePeriodoEstaFechado
     * @apiGroup EFDReinf-Eventos
     * @apiQuery {string} event R-2099 / R-4099
     * @apiQuery {string} competence yyyy-mm
     * @apiQuery {integer} DB_instit=session instit Codigo instituicao
     * @apiSuccess {Object} data
     * @apiSuccess {Boolean} data.closed Periodo fechado
     * @apiUse Error
     */
    public function checkPeriodIsClosed(CheckPeriodIsClosedRequest $request, CheckPeriodIsClosed $useCase)
    {
        try {
            $input = new PeriodIsClosedInputDTO($request->event, $request->competence, $request->DB_instit);
            $response = $useCase->execute($input);
            return new DBJsonResponse($response->toArray());
        } catch (BusinessException $e) {
            return new DBJsonResponse('', $e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro interno.', 500);
        }
    }
}
