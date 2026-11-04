<?php

namespace App\Domain\Tributario\ISSQN\Services\Redesim;

use App\Domain\Patrimonial\Ouvidoria\Services\AtendimentoProcessoService;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoPadrao;
use App\Domain\Patrimonial\Protocolo\Services\CgmService;
use App\Domain\Tributario\ISSQN\DTO\Redesim\ConfirmReceipt\ConfirmReceiptRequestDTO;
use App\Domain\Tributario\ISSQN\DTO\Redesim\ConfirmResponse\ConfirmResponseRequestDTO;
use App\Domain\Tributario\ISSQN\DTO\Redesim\RetrieveEstablishments\RetrieveEstablishmentsRequestDTO;
use App\Domain\Tributario\ISSQN\Factories\Reports\GenerateAlvaraFactory;
use App\Domain\Tributario\ISSQN\Model\Redesim\AutomaticEstablishmentProcess;
use App\Domain\Tributario\ISSQN\Model\Redesim\EstablishmentDataBatch;
use App\Domain\Tributario\ISSQN\Model\Redesim\EstablishmentUnprocessedEvent;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentData;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentEvent;
use App\Domain\Tributario\ISSQN\Model\Redesim\RedesimEvent;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao\RedesimDadosAtividadeParser;
use App\Domain\Tributario\ISSQN\RequestManagers\Redesim\RedesimRequestManager;
use App\Mail\RedesimErrorToProcessEstablishmentsMail;
use DBSeller\Session\Session;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Patrimonial\Ouvidoria\Externa\WebService\ProcessoEletronico\Solicitacao;
use ECidade\Patrimonial\Protocolo\Processo\ProcessoEletronico\Filter\ListagemProcessos as FiltroProcesso;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MovimentacaoAlvara;

class RedesimService
{
    private $atendimentoProcessoService;

    private $cgmService;

    public function __construct(AtendimentoProcessoService $atendimentoProcessoService, CgmService $cgmService)
    {
        $this->atendimentoProcessoService = $atendimentoProcessoService;
        $this->cgmService = $cgmService;
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function retrieveEstablishments()
    {
        if (!toBoolean(env("HABILITA_REDESIM"))) {
            return;
        }

        $maxEstablishmentsToRetrieve = 50;

        $retrieveEstablishmentsRequestDTO = new RetrieveEstablishmentsRequestDTO(
            env("REDESIM_ACCESS_KEY_ID"),
            env("REDESIM_SECRET_ACCESS_KEY"),
            $maxEstablishmentsToRetrieve,
            null
        );

        $redesimRequestManager = new RedesimRequestManager(RedesimRequestManager::RETRIEVE_ESTABLISHMENTS_ENDPOINT);
        $redesimRequestManager->enableErrorsLogging("retrieveEstablishments");
        $redesimRequestManager->post($retrieveEstablishmentsRequestDTO);

        if (!$redesimRequestManager->success || $redesimRequestManager->response->status != "OK") {
            throw new \Exception("Não foi possível recuperar os estabelecimentos na REDESIM.");
        }

        $this->saveEstablishmentDataBatch($redesimRequestManager->rawResponse);

        $identifiers = array_map(function ($establishmentData) {
            return $establishmentData->identificador;
        }, $redesimRequestManager->response->registrosRedesim->registroRedesim);

        $this->confirmReceipt($identifiers);
    }

    /**
     * @throws \Throwable
     */
    public function processEstablishments()
    {
        if (!toBoolean(env("HABILITA_REDESIM"))) {
            return;
        }

        $hasBatchWithError = EstablishmentDataBatch::whereError(true)->first();
        if ($hasBatchWithError) {
            Log::warning("RedesimService.processEstablishments >>> Existe dados de estabelecimento com erro.");
            return;
        }

        $establishmentDataBatch = EstablishmentDataBatch::whereProcessed(false)->orderBy("q189_created_at")->first();

        if (!$establishmentDataBatch) {
            return;
        }

        $establishmentsData = json_decode($establishmentDataBatch->q189_data);

        $errorMessageLog = "RedesimService.processEstablishments >>>";
        $errorMessageLog .= " Não foi possível processar os dados do estabelecimento";
        $errorMessageLog .= " [establishmentDataBatchSequencial: {$establishmentDataBatch->q189_sequencial}]";

        $hasError = false;
        $currentIdentifier = null;

        withNewTransactionAndRollbackOnError(function () use (
            $establishmentDataBatch,
            $establishmentsData,
            &$currentIdentifier
        ) {
            foreach ($establishmentsData->registrosRedesim->registroRedesim as $establishmentData) {
                $currentIdentifier = $establishmentData->identificador;

                $alreadyProcessed = ProcessedEstablishment::whereExternalId($establishmentData->identificador)->first();

                if (!$alreadyProcessed) {
                    $processedEstablishment = $this->saveProcessedEstablishment(
                        $establishmentDataBatch,
                        $establishmentData->identificador
                    );

                    $this->processEstablishmentEvents($processedEstablishment, $establishmentData);

                    if ($this->canProcessAutomatically($processedEstablishment, $establishmentData)) {
                        $this->saveAutomaticEstablishmentProcess($processedEstablishment);
                    }

                    $processTypeId = $this->getProcessTypeId($processedEstablishment);

                    $this->saveProcessedEstablishmentData($processedEstablishment, $establishmentData);

                    $responseData = $this->createProcess($processTypeId, $establishmentData);

                    $this->saveProcessOnProcessedEstablishment($processedEstablishment, $responseData->processId);
                }
            }
        }, ["onError" => function (\Exception $exception) use (&$currentIdentifier, &$hasError) {
            $hasError = true;

            $this->sendEmailForErrorInEstablishmentProcess($exception, $currentIdentifier);
        }, "errorMessageLog" => $errorMessageLog]);

        withNewTransactionAndRollbackOnError(function () use ($hasError, $establishmentDataBatch) {
            $establishmentDataBatch->q189_processed = !$hasError;
            $establishmentDataBatch->q189_error = $hasError;
            $establishmentDataBatch->saveOrFail();
        });
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function confirmResponse(ProcessedEstablishment $processedEstablishment, $issBase, $isDeferred)
    {
        if ($processedEstablishment->q190_replied) {
            throw new \Exception("A resposta já foi enviada para a REDESIM.");
        }

        $processedEstablishment->q190_replied = true;
        $processedEstablishment->saveOrFail();

        $alvaraInfo = null;
        $alvaraReport = null;

        if ($issBase && $issBase->q02_dtbaix == null && $isDeferred) {
            $alvaraInfo = DB::query()
                            ->from("issalvara")
                            ->join("issmovalvara", "issmovalvara.q120_issalvara", "issalvara.q123_sequencial")
                            ->where("q123_inscr", $issBase->q02_inscr)
                            ->whereIn(
                                "q120_isstipomovalvara",
                                [MovimentacaoAlvara::TIPO_LIBERACAO, MovimentacaoAlvara::TIPO_RENOVACAO]
                            )
                            ->orderBy("q120_dtmov", "desc")
                            ->first(["q123_sequencial", "q123_dtinclusao", "q120_validadealvara"]);

            if (!$alvaraInfo) {
                throw new \Exception("Informações do alvará não encontradas.");
            }

            $alvaraReport = GenerateAlvaraFactory::get($issBase, true);
            $alvaraReport->generate();
        }

        $confirmResponseRequestDTO = new ConfirmResponseRequestDTO(
            env("REDESIM_ACCESS_KEY_ID"),
            env("REDESIM_SECRET_ACCESS_KEY"),
            $processedEstablishment,
            $issBase,
            $alvaraReport,
            $alvaraInfo,
            $isDeferred
        );

        $redesimRequestManager = new RedesimRequestManager(RedesimRequestManager::CONFIRM_RESPONSE_ENDPOINT);
        $redesimRequestManager->enableErrorsLogging("confirmResponse");
        $redesimRequestManager->post($confirmResponseRequestDTO);

        if (!$redesimRequestManager->success || $redesimRequestManager->response->codigoRetornoWSEnum != "OK") {
            throw new \Exception("Não foi possível confirmar a resposta para a REDESIM.");
        }
    }

    /**
     * @throws \Throwable
     */
    public function saveUnprocessedEvent(ProcessedEstablishment $processedEstablishment, RedesimEvent $redesimEvent)
    {
        $establishmentUnprocessedEvent = new EstablishmentUnprocessedEvent();
        $establishmentUnprocessedEvent->q195_processed_establishment = $processedEstablishment->q190_sequencial;
        $establishmentUnprocessedEvent->q195_redesim_event = $redesimEvent->q191_sequencial;
        $establishmentUnprocessedEvent->saveOrFail();
    }

    /**
     * @throws \Exception
     */
    public function isLowRiskMunicipalRegistration($establishmentData)
    {
        $establishmentData = json_encode($establishmentData);
        $establishmentData = json_decode($establishmentData, true);

        $activityInfoList = RedesimDadosAtividadeParser::buildActivities($establishmentData);
        $activityInfoList = collect($activityInfoList);

        $lowRiskCode = "b";

        $lowRiskEstablishment = $activityInfoList->every(function ($activityInfo) use ($lowRiskCode) {
            return $activityInfo->data->risk == $lowRiskCode;
        });

        if ($lowRiskEstablishment) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    private function confirmReceipt($identifiers)
    {
        $confirmReceiptRequestDTO = new ConfirmReceiptRequestDTO(
            env("REDESIM_ACCESS_KEY_ID"),
            env("REDESIM_SECRET_ACCESS_KEY"),
            $identifiers
        );

        $redesimRequestManager = new RedesimRequestManager(RedesimRequestManager::CONFIRM_RECEIPT_ENDPOINT);
        $redesimRequestManager->enableErrorsLogging("confirmReceipt");
        $redesimRequestManager->post($confirmReceiptRequestDTO);

        if (!$redesimRequestManager->success || $redesimRequestManager->response->codigoRetornoWSEnum != "OK") {
            throw new \Exception("Não foi possível confirmar o recebimento dos estabelecimentos para a REDESIM.");
        }
    }

    /**
     * @param string $data
     * @throws \Throwable
     */
    private function saveEstablishmentDataBatch($data)
    {
        $establishmentDataBatch = new EstablishmentDataBatch();
        $establishmentDataBatch->q189_data = $data;
        $establishmentDataBatch->q189_processed = false;
        $establishmentDataBatch->saveOrFail();
    }

    /**
     * @throws \Exception
     */
    private function createProcess($processTypeId, $establishmentData)
    {
        $this->initSession($processTypeId);

        $oAtendimento = new Solicitacao();
        $oAtendimento->setMetadados("{}");
        $oAtendimento->setCodigoDepartamento(\db_getsession(DefaultSession::DB_CODDEPTO));
        $oAtendimento->setTipoProcesso($processTypeId);
        $oAtendimento->setRequerenteNome("ANONIMO");
        $oAtendimento->setRequerenteCpf(null);
        $oAtendimento->setCodigoAtendimentoAnterior(null);
        $oAtendimento->setClientAPPAtendimentoID(null);
        $oDadosAtendimento = $oAtendimento->salvar();

        $filtroProcesso = new FiltroProcesso();
        $filtroProcesso->setSequencial($oDadosAtendimento->atendimento->sequencial);

        $oAtendimento = $this->atendimentoProcessoService->buscarSolicitacaoOuvidoria($filtroProcesso, true);
        $oAtendimento->metadados = \JSON::create()->parse($oAtendimento->metadados);

        $cgm = $this->cgmService->findOrCreateLegacy(
            $establishmentData->dadosRedesim->cnpj,
            $this->buildEstablishmentOwnerInfo($establishmentData)
        );

        if (!$cgm) {
            throw new \Exception("Não foi possível cadastrar o CGM para o estabelecimento.");
        }

        $cgm->setNome(addcslashes($cgm->getNome(), "'"));
        $cgm->setNomeCompleto(addcslashes($cgm->getNomeCompleto(), "'"));

        $this->atendimentoProcessoService->aprovarProcessoSemCapa(
            $cgm,
            $oAtendimento,
            $cgm,
            "Processo gerado a partir da REDESIM."
        );

        return (object) [
            "processId" => $this->atendimentoProcessoService->getProcesso()->p58_codproc
        ];
    }

    private function processEstablishmentEvents(ProcessedEstablishment $processedEstablishment, $establishmentData)
    {
        foreach ($establishmentData->eventos->evento as $event) {
            $redesimEvent = RedesimEvent::whereExternalId($event->codEvento)->first();

            $this->saveEstablishmentEvent($processedEstablishment, $redesimEvent);
        }
    }

    /**
     * @throws \Exception
     */
    private function initSession($iTipoProcesso)
    {
        $andamentoPadrao = AndamentoPadrao::ordem(1)->tipoProcesso($iTipoProcesso)->first();

        if (!$andamentoPadrao) {
            throw new \Exception("Andamento do processo de ordem 1 (um) não configurado.");
        }

        $session = DefaultSession::getInstance()
            ->add([
                DefaultSession::DB_CODDEPTO => $andamentoPadrao->p53_coddepto,
                DefaultSession::DB_IP => "127.0.0.1",
                DefaultSession::DB_MODULO => 1,
                DefaultSession::DB_NOME_MODULO => 'Configuração',
                DefaultSession::DB_ACESSADO => 24
            ])
            ->start();

        if ($session->status() !== Session::ACTIVE) {
            throw new \Exception("Não foi possível construir a sessão legada.");
        }

        $databaseSession = DatabaseSession::getInstance()->addSessionToDatabase();

        if (!$databaseSession) {
            throw new \Exception("Não foi possível adicionar a sessão ao banco de dados.");
        }
    }

    private function saveProcessedEstablishment(EstablishmentDataBatch $establishmentDataBatch, $externalId)
    {
        $processedEstablishment = new ProcessedEstablishment();
        $processedEstablishment->q190_external_id = $externalId;
        $processedEstablishment->q190_establishment_data_batch = $establishmentDataBatch->q189_sequencial;
        $processedEstablishment->q190_process_id = null;
        $processedEstablishment->q190_processed = false;
        $processedEstablishment->saveOrFail();

        return $processedEstablishment;
    }

    private function saveEstablishmentEvent(ProcessedEstablishment $processedEstablishment, RedesimEvent $redesimEvent)
    {
        $processedEstablishmentEvent = new ProcessedEstablishmentEvent();
        $processedEstablishmentEvent->q192_processed_establishment = $processedEstablishment->q190_sequencial;
        $processedEstablishmentEvent->q192_redesim_event = $redesimEvent->q191_sequencial;
        $processedEstablishmentEvent->saveOrFail();
    }

    private function saveProcessOnProcessedEstablishment(ProcessedEstablishment $processedEstablishment, $processId)
    {
        if (!$processId) {
            throw new \Exception("Código do processo não informado.");
        }

        $processedEstablishment->q190_process_id = $processId;
        $processedEstablishment->saveOrFail();
    }

    /**
     * @throws \Exception
     */
    private function getProcessTypeId(ProcessedEstablishment $processedEstablishment)
    {
        $processedEstablishmentEventList = ProcessedEstablishmentEvent::whereProcessedEstablishment(
            $processedEstablishment->q190_sequencial
        )->get();

        $redesimEventIdList = $processedEstablishmentEventList->map(
            function (ProcessedEstablishmentEvent $processedEstablishmentEvent) {
                return $processedEstablishmentEvent->q192_redesim_event;
            }
        );

        $redesimEventList = RedesimEvent::whereIn("q191_sequencial", $redesimEventIdList->toArray())->get();

        if ($redesimEventList->contains("q191_event_type", RedesimEvent::CREATE)) {
            $processTypeId = env("REDESIM_PROCESSO_INCLUSAO_INSCRICAO");
        } elseif ($redesimEventList->contains("q191_event_type", RedesimEvent::LOW)) {
            $processTypeId = env("REDESIM_PROCESSO_BAIXA_INSCRICAO");
        } elseif ($redesimEventList->contains("q191_event_type", RedesimEvent::UPDATE)) {
            $processTypeId = env("REDESIM_PROCESSO_ATUALIZACAO_INSCRICAO");
        } else {
            throw new \Exception("Evento não configurado.");
        }

        if (!$processTypeId) {
            throw new \Exception("Tipo de processo não configurado.");
        }

        return $processTypeId;
    }

    private function sendEmailForErrorInEstablishmentProcess(\Exception $exception, $identifider)
    {
        if (App::isLocal() || !toBoolean(env("HABILITA_EMAIL_ERRO_PROCESSAMENTO_ESTABELECIMENTO_REDESIM"))) {
            return;
        }

        $redesimErrorToProcessEstablishmentsMail = new RedesimErrorToProcessEstablishmentsMail(
            $identifider,
            $exception
        );

        Mail::send($redesimErrorToProcessEstablishmentsMail);
    }

    private function saveProcessedEstablishmentData(ProcessedEstablishment $processedEstablishment, $establishmentData)
    {
        $processedEstablishmentData = new ProcessedEstablishmentData();
        $processedEstablishmentData->q193_processed_establishment = $processedEstablishment->q190_sequencial;
        $processedEstablishmentData->q193_data = json_encode($establishmentData);
        $processedEstablishmentData->saveOrFail();
    }

    /**
     * @throws \Exception
     */
    private function buildEstablishmentOwnerInfo($establishmentData)
    {
        $data = [];
        $data["nome"] = $establishmentData->dadosRedesim->nomeEmpresarial;
        $data["nomeCompleto"] = $establishmentData->dadosRedesim->nomeEmpresarial;

        if (isset($establishmentData->dadosRedesim->endereco)) {
            $address = $establishmentData->dadosRedesim->endereco;

            $data["cep"] = $address->cep;
            $data["bairro"] = isset($address->bairro) ? $address->bairro : null;
            $data["numero"] = onlyNumbers($address->numLogradouro);
            $data["logradouro"] = $address->logradouro;
            $data["complemento"] = isset($address->complemento) ? $address->complemento : null;
        }

        return (object) $data;
    }

    /**
     * @throws \Throwable
     */
    private function saveAutomaticEstablishmentProcess(ProcessedEstablishment $processedEstablishment)
    {
        $automaticEstablishmentProcess = new AutomaticEstablishmentProcess();
        $automaticEstablishmentProcess->q194_processed_establishment = $processedEstablishment->q190_sequencial;
        $automaticEstablishmentProcess->q194_processed = false;
        $automaticEstablishmentProcess->q194_error = false;
        $automaticEstablishmentProcess->saveOrFail();
    }

    /**
     * @throws \Exception
     */
    private function canProcessAutomatically(ProcessedEstablishment $processedEstablishment, $establishmentData)
    {
        if ($this->getProcessTypeId($processedEstablishment) == env("REDESIM_PROCESSO_INCLUSAO_INSCRICAO")) {
            return $this->isLowRiskMunicipalRegistration($establishmentData);
        }

        return false;
    }
}
