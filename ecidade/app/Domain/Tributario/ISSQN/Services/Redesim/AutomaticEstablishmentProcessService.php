<?php

namespace App\Domain\Tributario\ISSQN\Services\Redesim;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Ouvidoria\Services\AtendimentoProcessoService;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Tributario\ISSQN\Model\Base\IssAlvara;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvara;
use App\Domain\Tributario\ISSQN\Model\Redesim\AutomaticEstablishmentProcess;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Services\AlvaraService;
use App\Domain\Tributario\ISSQN\Strategies\Redesim\RedesimInscricaoEventStrategy;
use Carbon\Carbon;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use MovimentacaoAlvara;

class AutomaticEstablishmentProcessService
{
    private $redesimInscricaoEventStrategy;

    private $atendimentoProcessoService;

    private $redesimService;

    private $alvaraService;

    public function __construct(
        RedesimInscricaoEventStrategy $redesimInscricaoEventStrategy,
        AtendimentoProcessoService $atendimentoProcessoService,
        RedesimService $redesimService,
        AlvaraService $alvaraService
    ) {
        $this->redesimInscricaoEventStrategy = $redesimInscricaoEventStrategy;
        $this->atendimentoProcessoService = $atendimentoProcessoService;
        $this->redesimService = $redesimService;
        $this->alvaraService = $alvaraService;
    }

    public function processPendingEstablishments()
    {
        if (!toBoolean(env("HABILITA_REDESIM"))) {
            return;
        }

        $maxEstablishments = 20;
        $automaticEstablishmentProcess = AutomaticEstablishmentProcess::whereProcessed(false)
                                                                      ->whereError(false)
                                                                      ->limit($maxEstablishments)
                                                                      ->get();

        $automaticEstablishmentProcess->each(function (AutomaticEstablishmentProcess $automaticEstablishmentProcess) {
            withNewTransactionAndRollbackOnError(function () use ($automaticEstablishmentProcess) {
                $processedEstablishment = ProcessedEstablishment::whereSequencial(
                    $automaticEstablishmentProcess->q194_processed_establishment
                )->first();

                $automaticEstablishmentProcess->q194_processed = true;
                $automaticEstablishmentProcess->saveOrFail();

                if ($processedEstablishment->q190_processed || $processedEstablishment->q190_replied) {
                    return;
                }

                $this->process($processedEstablishment);
            }, ["onError" => function () use ($automaticEstablishmentProcess) {
                $automaticEstablishmentProcess->q194_error = true;
                $automaticEstablishmentProcess->saveOrFail();
            }, "errorMessageLog" => "AutomaticEstablishmentProcessService.processPendingEstablishments >>>
                    Não foi possível processar automaticamente o estabelecimento
                    [automaticEstablishmentProcessId: {$automaticEstablishmentProcess->q194_sequencial}]"
            ]);
        });
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    private function process(ProcessedEstablishment $processedEstablishment)
    {
        $process = Processo::where("p58_codproc", $processedEstablishment->q190_process_id)->first();

        if ($process->p58_codigo != env("REDESIM_PROCESSO_INCLUSAO_INSCRICAO")) {
            throw new \Exception("Informe uma inscrição para executar um processo de alteração.");
        }

        $this->loadSession($process);

        $cgm = Cgm::where("z01_numcgm", $process->p58_numcgm)->first();
        $this->redesimInscricaoEventStrategy
            ->setIsAutomaticProcessing(true)
            ->runEvents($processedEstablishment, $cgm, null);
        $issBase = $this->redesimInscricaoEventStrategy->issBase;

        $this->releaseAlvaraIfNecessary($issBase, $process);

        $this->atendimentoProcessoService->setProcesso($process);
        $this->atendimentoProcessoService->baixarProcesso(
            "Processo baixado no processamento automático de estabelecimento de baixo risco REDESIM.",
            false
        );

        $this->redesimService->confirmResponse($processedEstablishment, $issBase, true);
    }

    /**
     * @throws \Throwable
     */
    private function releaseAlvaraIfNecessary(IssBase $issBase, Processo $process)
    {
        $issAlvara = IssAlvara::where("q123_inscr", $issBase->q02_inscr)->first();
        $issMovAlvara = IssMovAlvara::where("q120_issalvara", $issAlvara->q123_sequencial)
                                    ->where("q120_isstipomovalvara", MovimentacaoAlvara::TIPO_LIBERACAO)
                                    ->first();

        if ($issMovAlvara) {
            return;
        }

        $user = Usuario::where("id_usuario", db_getsession("DB_id_usuario"))->first();
        $alvaraReleaseDate = Carbon::now()->format("Y-m-d");
        $alvaraExpirydays = 0;
        $alvaraReleaseObservation = "";
        $this->alvaraService->release(
            $issAlvara,
            $user,
            $process,
            $alvaraReleaseDate,
            $alvaraExpirydays,
            $alvaraReleaseObservation
        );
    }

    private function loadSession(Processo $process)
    {
        $defaultSession = DefaultSession::getInstance();
        $defaultSession->set(DefaultSession::DB_CODDEPTO, $process->getDepartamento());

        DatabaseSession::getInstance()->addSessionToDatabase();

        DBQuery()->selectRaw("fc_startsession()")->get();

        DBQuery()->selectRaw("fc_putsession('DB_anousu', ?)", [
            $defaultSession->get(DefaultSession::DB_ANOUSU)
        ])->get();

        DBQuery()->selectRaw("fc_putsession('DB_id_usuario', ?)", [
            $defaultSession->get(DefaultSession::DB_ID_USUARIO)
        ])->get();

        DBQuery()->selectRaw("fc_putsession('DB_instit', ?)", [
            $defaultSession->get(DefaultSession::DB_INSTIT)
        ])->get();

        DBQuery()->selectRaw("fc_putsession('DB_coddepto', ?)", [
            $defaultSession->get(DefaultSession::DB_CODDEPTO)
        ])->get();
    }
}
