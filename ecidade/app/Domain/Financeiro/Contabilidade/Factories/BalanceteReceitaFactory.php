<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaEcidadeService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaEmentarioPadraoService;

class BalanceteReceitaFactory
{
    /**
     * @param $ementario
     * @return BalanceteReceitaEcidadeService|BalanceteReceitaEmentarioPadraoService
     * @throws \Exception
     */
    public static function getService($ementario)
    {
        switch ($ementario) {
            case 'ecidade':
                return new BalanceteReceitaEcidadeService();
            case 'uniao':
            case 'estadual':
                return new BalanceteReceitaEmentarioPadraoService();
        }
        throw new \Exception('Não foi encontrado service para o ementário informado.', 400);
    }
}
