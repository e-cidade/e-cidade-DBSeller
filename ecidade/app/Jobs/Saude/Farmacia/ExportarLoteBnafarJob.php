<?php

namespace App\Jobs\Saude\Farmacia;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Saude\Farmacia\Services\BnafarQueueService;
use App\Domain\Saude\Farmacia\Services\ProcedimentosBnafarService;
use App\Notifications\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportarLoteBnafarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;

    protected $service;

    protected $user;

    protected $bnafarQueueService;

    /**
     * @param ProcedimentosBnafarService $service
     * @param integer $user
     * @param BnafarQueueService $bnafarQueueService
     * @throws \Exception
     */
    public function __construct(ProcedimentosBnafarService $service, $user, BnafarQueueService $bnafarQueueService)
    {
        if ($this->connection === 'sync' || env('QUEUE_DRIVER') === 'sync') {
            throw new \Exception('N�o � poss�vel executar esse servi�o de forma s�ncrona. Contate o suporte.');
        }

        $this->service = $service;
        $this->user = $user;
        $this->bnafarQueueService = $bnafarQueueService;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $this->service->processarLotes($this->bnafarQueueService);
    }

    public function failed(\Exception $e)
    {
        $user = Usuario::find($this->user);
        $user->notify(new UserNotification('Erro ao iniciar processamento do lote.', $e->getMessage()));
        $this->bnafarQueueService->terminate();
    }
}
