<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Services;

use Illuminate\Support\Facades\DB;

class ExecutaSqlService
{
    /**
     * @throws \Exception
     */
    public function execute($sql, array $bindings = [])
    {
        if (\DBSqlValidador::sqlAlteraDadosOuEstrutura($sql)) {
            throw new \Exception('Operaчуo nуo permita. SQL possui comandos nуo permitidos!', 406);
        }

        return DB::select($sql, $bindings);
    }
}
