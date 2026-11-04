<?php

namespace App\Domain\Tributario\ISSQN\Strategies\Redesim;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\ISSQN\Enum\Redesim\RedesimEventExternalCodeEnum;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentEvent;
use App\Domain\Tributario\ISSQN\Services\Redesim\Action\RedesimAlterarInscricaoService;
use App\Domain\Tributario\ISSQN\Services\Redesim\Action\RedesimGerarInscricaoService;
use App\Domain\Tributario\ISSQN\Services\Redesim\RedesimService;
use Illuminate\Support\Arr;

class RedesimInscricaoEventStrategy
{
    private $redesimGerarInscricaoService;

    private $redesimAlterarInscricaoService;

    /**
     * @var IssBase
     */
    public $issBase;

    /**
     * @var RedesimService
     */
    private $redesimService;

    private $isAutomaticProcessing = false;

    public function __construct(
        RedesimService $redesimService,
        RedesimGerarInscricaoService $redesimGerarInscricaoService,
        RedesimAlterarInscricaoService $redesimAlterarInscricaoService
    ) {
        $this->redesimService = $redesimService;
        $this->redesimGerarInscricaoService = $redesimGerarInscricaoService;
        $this->redesimAlterarInscricaoService = $redesimAlterarInscricaoService;
    }

    /**
     * @param boolean $isAutomaticProcessing
     */
    public function setIsAutomaticProcessing($isAutomaticProcessing)
    {
        $this->isAutomaticProcessing = $isAutomaticProcessing;

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function runEvents(ProcessedEstablishment $processedEstablishment, $cgm, $issBaseList)
    {
        if ($issBaseList) {
            $issBaseList->each(function ($issBase) use ($processedEstablishment) {
                $this->processEvents($processedEstablishment, null, $issBase);
            });
        } else {
            $this->processEvents($processedEstablishment, $cgm, null);
        }

        $processedEstablishment->q190_processed = true;
        $processedEstablishment->saveOrFail();
    }

    /**
     * @throws \Throwable
     */
    private function processEvents(ProcessedEstablishment $processedEstablishment, $cgm, $issBase)
    {
        $this->setEstablishmentInfoAtServices($processedEstablishment, $cgm, $issBase);

        $processedEstablishmentEvents = ProcessedEstablishmentEvent::whereProcessedEstablishment(
            $processedEstablishment->q190_sequencial
        )->get();

        $processedEstablishmentEvents->each(
            function (ProcessedEstablishmentEvent $processedEstablishmentEvent) use ($processedEstablishment) {
                $this->processEvent($processedEstablishment, $processedEstablishmentEvent);
            }
        );
    }

    /**
     * @throws \Throwable
     */
    private function processEvent(
        ProcessedEstablishment $processedEstablishment,
        ProcessedEstablishmentEvent $processedEstablishmentEvent
    ) {
        $redesimEvent = $processedEstablishmentEvent->redesimEvent;

        switch ($redesimEvent->q191_external_id) {
            case RedesimEventExternalCodeEnum::ENTRADA_DE_SOCIO_ADMINISTRADOR:
                $this->redesimAlterarInscricaoService->addNewPartner();
                break;
            case RedesimEventExternalCodeEnum::INSCRICAO_DE_PRIMEIRO_ESTABELECIMENTO:
                $this->createMunicipalRegistration($processedEstablishment);
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DE_ENDERECO_DENTRO_DO_MESMO_MUNICIPIO:
                $this->redesimAlterarInscricaoService->changeAddress();
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DE_CORREIO_ELETRONICO:
                $this->redesimAlterarInscricaoService->changeEstablishmentEmail();
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DO_NOME_EMPRESARIAL_FIRMA_OU_DENOMINACAO:
                $this->redesimAlterarInscricaoService->changeEstablishmentName();
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DO_TITULO_DO_ESTABELECIMENTO_NOME_DE_FANTASIA:
                $this->redesimAlterarInscricaoService->changeFantasyName();
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DE_ATIVIDADES_ECONOMICAS_PRINCIPAL_E_SECUNDARIAS:
                $this->redesimAlterarInscricaoService->changeActivities();
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DE_CAPITAL_SOCIAL:
                $this->redesimAlterarInscricaoService->changePartnerCapitalStock();
                break;
            case RedesimEventExternalCodeEnum::EVENTO_DE_ALTERACAO_DE_PERIODO_DO_SIMPLES_NACIONAL:
                $this->redesimAlterarInscricaoService->changeSimplesNacional();
                break;
            case RedesimEventExternalCodeEnum::PEDIDO_DE_BAIXA:
                $this->redesimAlterarInscricaoService->baixa(true);
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DE_DADOS_DO_SOCIO_REPRESENTANTE:
                $this->redesimAlterarInscricaoService->changePartnerInfo();
                break;
            case RedesimEventExternalCodeEnum::ALTERACAO_DE_TELEFONE_DDD_TELEFONE:
                $this->redesimAlterarInscricaoService->changePhone();
                break;
            default:
                $this->redesimService->saveUnprocessedEvent($processedEstablishment, $redesimEvent);
        }
    }

    /**
     * @throws \Exception
     */
    private function setEstablishmentInfoAtServices(ProcessedEstablishment $processedEstablishment, $cgm, $issBase)
    {
        $this->validateInstance($cgm, $issBase);

        $this->redesimGerarInscricaoService->setProcessedEstablishment($processedEstablishment)->setCgm($cgm);

        $this->redesimAlterarInscricaoService->setProcessedEstablishment($processedEstablishment)
                                             ->setIssBase($issBase)
                                             ->findEstablishmentData();
    }

    /**
     * @throws \Exception
     */
    private function validateInstance($cgm, $issBase)
    {
        if (!$cgm && !$issBase) {
            throw new \Exception("Informe uma instância de CGM ou IssBase!");
        }

        if ($cgm && !($cgm instanceof Cgm)) {
            throw new \Exception("Informe uma instância de CGM!");
        }

        if ($issBase && !($issBase instanceof IssBase)) {
            throw new \Exception("Informe uma instância de IssBase!");
        }
    }

    /**
     * @throws \Throwable
     */
    private function createMunicipalRegistration(ProcessedEstablishment $processedEstablishment)
    {
        $this->redesimGerarInscricaoService->findEstablishmentData();

        if ($this->isAutomaticProcessing) {
            $isLowRisk = $this->redesimService->isLowRiskMunicipalRegistration(
                $this->redesimGerarInscricaoService->getEstablishmentData()
            );

            if (!$isLowRisk) {
                throw new \Exception("Somente estabelecimentos de baixo risco podem ser gerados automaticamente.");
            }
        }

        $this->issBase = $this->redesimGerarInscricaoService->run();
        $this->setEstablishmentInfoAtServices($processedEstablishment, null, $this->issBase);
    }
}
