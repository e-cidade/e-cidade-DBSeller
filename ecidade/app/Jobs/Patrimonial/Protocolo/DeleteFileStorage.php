<?php

namespace App\Jobs\Patrimonial\Protocolo;

use App\Domain\Configuracao\Helpers\StorageHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteFileStorage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $idsStorage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($idsStorage)
    {
        $this->idsStorage = $idsStorage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->idsStorage as $arquivo) {
            StorageHelper::deleteArquivo($arquivo["id"]);
        }
    }
}
