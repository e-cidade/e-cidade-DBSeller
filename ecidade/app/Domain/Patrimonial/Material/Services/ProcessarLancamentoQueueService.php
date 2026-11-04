<?php

namespace App\Domain\Patrimonial\Material\Services;

use App\Domain\Core\Services\QueueService;
use App\Domain\Patrimonial\Material\Models\LancamentoContabilRequisicaoBatch;
use App\Jobs\Patrimonial\Material\ProcessarLancamentoContabilRequisicaoJob;

class ProcessarLancamentoQueueService extends QueueService
{
    private $lancamentoContabilRequisicaoBatch;
    public static function newBatch(ProcessarLancamentoContabilRequisicaoService $dadosRequisicao)
    {
        $self = new self(ProcessarLancamentoContabilRequisicaoJob::class);

        $self->lancamentoContabilRequisicaoBatch = LancamentoContabilRequisicaoBatch::create(
            ["m106_batch"=>$self->getBatch()->id, "m106_parametros"=>json_encode($dadosRequisicao->toStd())]
        );

        return $self;
    }

    public function markAsSuccess()
    {
        $this->lancamentoContabilRequisicaoBatch->m106_status = "SUCESSO";
        $this->lancamentoContabilRequisicaoBatch->saveOrFail();
    }

    public function markAsFailed(\Exception $e)
    {
        $this->lancamentoContabilRequisicaoBatch->m106_status = "ERRO";
        $this->lancamentoContabilRequisicaoBatch->m106_exception = $e;
        $this->lancamentoContabilRequisicaoBatch->save();
    }

    public static function getActiveProcess()
    {
        return LancamentoContabilRequisicaoBatch::where("m106_status", "PROCESSANDO")->count();
    }
}
