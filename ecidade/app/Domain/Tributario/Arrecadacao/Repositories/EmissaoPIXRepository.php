<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIX;
use Illuminate\Support\Collection;

class EmissaoPIXRepository
{
    public static function listEmissao()
    {
        $result = EmissaoPIX::orderBy('tr10_sequencial', 'desc')->get();

        $result->each(function ($object) {
            if ($object->tr10_concluidos_jobs > 0 && $object->tr10_num_jobs > 0) {
                $object->status = round(($object->tr10_concluidos_jobs / $object->tr10_num_jobs) * 100);
            } else {
                $object->status = 0;
            }
        });

        return $result;
    }

    public static function listEmissaoConcluidas()
    {
        $result = new Collection();

        EmissaoPIX::where([
            ['tr10_status', true],
            ['tr10_concluidos_jobs', '>', 0],
            ['tr10_num_jobs', '>', 0]
        ])
            ->orderBy('tr10_sequencial', 'desc')
            ->each(function ($item) use (&$result) {
                $total = $item->tr10_concluidos_jobs + $item->tr10_failed_jobs;
                
                if ($total >= $item->tr10_num_jobs) {
                    $result->push($item);
                }
            });

        return $result;
    }
    
    public static function listEmissaoAvailable()
    {
        $result = EmissaoPIX::where([
            ['tr10_concluidos_jobs', '>', 0],
            ['tr10_num_jobs', '>', 0]
        ])->orderBy('tr10_sequencial', 'desc')->get();

        return $result;
    }
}
