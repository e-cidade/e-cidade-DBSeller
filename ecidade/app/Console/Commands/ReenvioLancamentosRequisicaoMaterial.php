<?php

namespace App\Console\Commands;

use App\Domain\Patrimonial\Material\Services\ProcessarLancamentoContabilRequisicaoService;
use App\Jobs\Patrimonial\Material\ProcessarLancamentoContabilRequisicaoJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReenvioLancamentosRequisicaoMaterial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lancamento:requisicao-material-reenvio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recoloca na fila de processamento os lançamentos que deram erro';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::statement('drop table if exists bkp_lancamentocontabilrequisicaobatch;');
            DB::statement(<<<SQL
create table bkp_lancamentocontabilrequisicaobatch as
select * from material.lancamentocontabilrequisicaobatch
where m106_status != 'SUCESSO';
SQL
            );
            $dados = DB::select("
                select * from material.lancamentocontabilrequisicaobatch where m106_status != 'SUCESSO';
            ");

            foreach ($dados as $dado) {
                DB::beginTransaction();
                $parametros = json_decode($dado->m106_parametros);
                $parametros->session = (array)$parametros->session;
                $this->info('fazendo ' .$parametros->codigoLancamentoMaterial);

                $servico = new ProcessarLancamentoContabilRequisicaoService($parametros);
                dispatch(new ProcessarLancamentoContabilRequisicaoJob($servico));

                DB::connection()->getPdo()->exec(<<<SQL
delete from public.queued_jobs
where batch_id = $dado->m106_batch;

delete from material.lancamentocontabilrequisicaobatch
where lancamentocontabilrequisicaobatch.m106_sequencial = $dado->m106_sequencial;

delete from public.batch_jobs
where id = $dado->m106_batch;
SQL
                );
                DB::commit();
            }


            $this->info("Sucesso!");
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }
}
