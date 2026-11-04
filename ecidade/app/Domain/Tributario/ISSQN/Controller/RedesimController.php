<?php

namespace App\Domain\Tributario\ISSQN\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Ouvidoria\Services\AtendimentoProcessoService;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\Arrecadacao\Services\ComprovanteDesfazimentoTefService;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentData;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentEvent;
use App\Domain\Tributario\ISSQN\Services\Redesim\RedesimService;
use App\Domain\Tributario\ISSQN\Services\Redesim\RelatorioInscricoesService;
use App\Domain\Tributario\ISSQN\Strategies\Redesim\RedesimInscricaoEventStrategy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Tributario\ISSQN\Services\Redesim\RedesimBalcaoUnicoService;
use Illuminate\Support\Facades\Log;

class RedesimController extends Controller
{
    private $redesimBalcaoUnicoService;

    public function __construct(RedesimBalcaoUnicoService $redesimBalcaoUnicoService)
    {
        $this->redesimBalcaoUnicoService = $redesimBalcaoUnicoService;
    }

    public function incluirInscricao(Request $request, AtendimentoProcessoService $atendimentoProcessoService)
    {
        try {
            $codigoInscricao = $this->redesimBalcaoUnicoService->incluirInscricaoBalcaoUnico(
                $atendimentoProcessoService,
                (object) $request->all()
            );

            return response()->json(["status" => "OK", "numeroInscricao" => $codigoInscricao, "arquivoPDF" => ""]);
        } catch (\Exception $exception) {
            Log::error($exception);

            $aErrorMessage = [$exception->getMessage(), $exception->getFile(), $exception->getLine()];
            return response()->json(
                ["status" => "NOK", "descricao" => utf8_encode(implode(" - ", $aErrorMessage))],
                500
            );
        }
    }

    public function relatorioInscricoes(Request $request)
    {
        $service = new RelatorioInscricoesService();
        $service->setDataInicio($request->dataInicio);
        $service->setDataFim($request->dataFim);
        $service->setMostraArquivo(false);
        $service->setRetornaBase64(false);
        $service->gerar();

        return new DBJsonResponse([
            "arquivo" => $service->getArquivo()
        ]);
    }

    public function inscricoesCgm(Request $request)
    {
        $issBaseQuery = IssBase::query()->where("q02_numcgm", $request->cgm);

        if (toBoolean($request->input("onlyActivated"))) {
            $issBaseQuery->whereNull("q02_dtbaix");
        }

        $issbaseList = $issBaseQuery->get();

        $data = $issbaseList->map(function (IssBase $issBase) {
            return ["id" => $issBase->q02_inscr, "name" => $issBase->cgm->z01_nome];
        });

        return new DBJsonResponse($data);
    }

    /**
     * @throws \BusinessException
     * @throws \Throwable
     */
    public function processarEventoInclusaoInscricao(
        RedesimInscricaoEventStrategy $redesimInscricaoEventStrategy,
        Request $request
    ) {
        $cgm = Cgm::query()->where("z01_numcgm", $request->cgm)->first();

        if (!$cgm) {
            throw new \BusinessException("CGM não encontrado.");
        }

        $processedEstablishment = ProcessedEstablishment::whereProcessId($request->processo)->first();

        if (!$processedEstablishment) {
            throw new \BusinessException("Estabelecimento para ser processado não encontrado.");
        }

        if ($processedEstablishment->q190_processed) {
            throw new \BusinessException("Estabelecimento já processado.");
        }

        if ($processedEstablishment->q190_replied) {
            throw new \BusinessException("Resposta já enviada.");
        }

        $redesimInscricaoEventStrategy->runEvents($processedEstablishment, $cgm, null);

        return new DBJsonResponse(
            null,
            "Inscrição {$redesimInscricaoEventStrategy->issBase->q02_inscr} gerada com sucesso."
        );
    }

    /**
     * @throws \BusinessException
     * @throws \Throwable
     */
    public function processarEventoAlteracaoInscricao(
        RedesimInscricaoEventStrategy $redesimInscricaoEventStrategy,
        Request $request
    ) {
        $municipalRegistrationList = $request->input("inscr") ? explode(",", $request->input("inscr")) : null;

        if (!$municipalRegistrationList) {
            throw new \BusinessException("Informe ao menos uma inscrição.");
        }

        $issBaseList = IssBase::query()->whereIn("q02_inscr", $municipalRegistrationList)->get();

        if (!$issBaseList) {
            throw new \BusinessException("Inscrição não encontrada.");
        }

        $processedEstablishment = ProcessedEstablishment::whereProcessId($request->processo)->first();

        if (!$processedEstablishment) {
            throw new \BusinessException("Estabelecimento para ser processado não encontrado.");
        }

        if ($processedEstablishment->q190_processed) {
            throw new \BusinessException("Estabelecimento já processado.");
        }

        if ($processedEstablishment->q190_replied) {
            throw new \BusinessException("Resposta já enviada.");
        }

        $redesimInscricaoEventStrategy->runEvents($processedEstablishment, null, $issBaseList);

        $responseMessage = $issBaseList->count() > 1 ? "Inscrições " : "Inscrição ";
        $responseMessage .= $issBaseList->map(function (IssBase $issBase) {
            return $issBase->q02_inscr;
        })->implode(", ");
        $responseMessage .= $issBaseList->count() > 1 ? " alteradas" : " alterada";
        $responseMessage .= " com sucesso.";

        return new DBJsonResponse(
            null,
            $responseMessage
        );
    }

    /**
     * @throws \BusinessException
     * @throws \Throwable
     */
    public function enviarResposta(Request $request, RedesimService $redesimService)
    {
        $isDeferred = $request->input("isDeferred");

        if ($isDeferred == null) {
            throw new \BusinessException("Informe a resposta");
        }

        $isDeferred = toBoolean($isDeferred);

        $municipalRegistration = $request->input("municipalRegistrationId");
        $issBase = null;
        if ($municipalRegistration != null && $municipalRegistration != "null") {
            $issBase = IssBase::query()->where("q02_inscr", $municipalRegistration)->first();

            if (!$issBase) {
                throw new \BusinessException("Inscrição não encontrada.");
            }
        }

        $processId = $request->input("processId");

        if (!$processId) {
            throw new \BusinessException("Processo não informado.");
        }

        $processedEstablishment = ProcessedEstablishment::whereProcessId($processId)->first();

        if (!$processedEstablishment) {
            throw new \BusinessException("Estabelecimento à ser respondido não encontrado.");
        }

        if ($processedEstablishment->q190_replied) {
            throw new \BusinessException("Resposta já enviada.");
        }

        $redesimService->confirmResponse($processedEstablishment, $issBase, $isDeferred);

        return new DBJsonResponse(
            null,
            "Resposta enviada para a REDESIM."
        );
    }

    /**
     * @throws \BusinessException
     */
    public function eventos(Request $request)
    {
        $processedEstablishment = ProcessedEstablishment::whereProcessId($request->processo)->first();

        if (!$processedEstablishment) {
            throw new \BusinessException("Estabelecimento não encontrado.");
        }

        $processedEstablishmentEvent = ProcessedEstablishmentEvent::whereProcessedEstablishment(
            $processedEstablishment->q190_sequencial
        )->get();

        $responseData = $processedEstablishmentEvent->map(
            function (ProcessedEstablishmentEvent $processedEstablishmentEvent) {
                return [
                    "id" => $processedEstablishmentEvent->redesimEvent->q191_sequencial,
                    "external_id" => $processedEstablishmentEvent->redesimEvent->q191_external_id,
                    "description" => $processedEstablishmentEvent->redesimEvent->q191_description
                ];
            }
        );

        return new DBJsonResponse($responseData);
    }

    /**
     * @throws \BusinessException
     */
    public function dadosEstabelecimento(Request $request)
    {
        $processedEstablishment = ProcessedEstablishment::whereProcessId($request->processo)->first();

        if (!$processedEstablishment) {
            throw new \BusinessException("Estabelecimento não encontrado.");
        }

        $processedEstablishmentData = ProcessedEstablishmentData::whereProcessedEstablishment(
            $processedEstablishment->q190_sequencial
        )->first();

        return new DBJsonResponse(["establishmentData" => $processedEstablishmentData->q193_data]);
    }
}
