<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers;

use App\Domain\Core\Models\BatchJob;
use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\ConcessaoErros;
use App\Jobs\RecursosHumanos\RH\ProcessamentoConcessao;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProcessConcessaoCountProviders
{
    public static function salvarLogErro($matricula, $erro)
    {
        if ($erro) {
            $proce = new ConcessaoErros();
            $proce->rh509_matricula = $matricula;
            $proce->rh509_erro = $erro;
            $s = $proce->save();
            return $s;
        }
    }

    public static function pararFila()
    {
        $queueService = json_decode(Cache::get('processaconcessao'));

        BatchJob::where('classname', ProcessamentoConcessao::class)
            ->where('id', $queueService->id)->update(['cancelled' => true]);

        Cache::forget('processaconcessao');
    }

    public static function getFila()
    {
        $existCache = Cache::has('processaconcessao');

        if ($existCache) {
            $cache = Cache::get('processaconcessao');
            $array = json_decode($cache);

            $totaljobs = QueuedJob::where('batch_id', $array->id)->get()->count();

            if ($totaljobs > 0) {
                return [[ "rh510_total" => $array->total,
                "rh510_quantidade" => $totaljobs]];
            } else {
                Cache::forget('processaconcessao');
                return [];
            }
        } else {
            return [];
        }
    }
}
