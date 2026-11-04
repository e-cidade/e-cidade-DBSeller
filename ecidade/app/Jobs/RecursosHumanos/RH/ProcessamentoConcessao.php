<?php

namespace App\Jobs\RecursosHumanos\RH;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\ProcessConcessaoCountProviders;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Bus\Queueable;
use App\Domain\Core\Services\QueueService;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers\Concessao;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Services\ConcessaoDireitosService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessamentoConcessao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $serviceConcessao;

    /**
     * @var int
     */
    private $inst;

    /**
     * @var int
     */
    private $rh500_sequencial;

    /**
     * @var int
     */
    private $assentconfig;

    /**
     * @var String
     */
    private $data_processamento;

    /**
     * @var Date
     */
    private $data;

    private $rh01_regist;

    public $tries = 3;

    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * @var QueuedJob
     */
    public $queuedJob;

    public $id_usuario;


    public function __construct(
        $inst,
        $rh500_sequencial,
        $data_processamento,
        $data,
        $assentconfig,
        $rh01_regist,
        QueueService $queueService,
        $id_usuario
    ) {
        $this->data = $data;
        $this->assentconfig = $assentconfig;
        $this->inst = $inst;
        $this->rh500_sequencial = $rh500_sequencial;
        $this->data_processamento = $data_processamento;
        $this->rh01_regist = $rh01_regist;
        $this->queueService = $queueService;
        $this->id_usuario = $id_usuario;
        $this->queuedJob = $queueService->next();
        $this->serviceConcessao = new ConcessaoDireitosService();
    }

    /**
     * Execute the job. ProvidersRhpessoal
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->queuedJob->refresh();

            $this->queueService->terminate($this->queuedJob);
            if ($this->queuedJob->batch->cancelled) {
                return;
            }
            $periodos = Concessao::periodos($this->rh500_sequencial);
            $assents_envolvidos = Concessao::todosAssentamntosEnvolvidos($this->rh500_sequencial);

            $this->serviceConcessao->processamento(
                $this->rh01_regist,
                $this->inst,
                $this->rh500_sequencial,
                $this->data_processamento,
                $assents_envolvidos,
                $periodos
            );

            $existCache = Cache::has('processaconcessao');
            if ($existCache) {
                $cache = Cache::get('processaconcessao');
                $array = json_decode($cache);
                $jobs = DB::select('select count(1) from queued_jobs where batch_id = ' . $array->id);
                $totaljobs = $jobs[0]->count;

                if ($totaljobs == 0) {
                    $this->serviceConcessao->notificaUsuario($this->id_usuario);
                    Cache::forget('processaconcessao');
                }
            }
        } catch (Exception $th) {
            ProcessConcessaoCountProviders::salvarLogErro(
                null,
                'Matricula - ERRO ao iniciar Job - ' . $th->getMessage()
            );
            return;
        }
    }
}
