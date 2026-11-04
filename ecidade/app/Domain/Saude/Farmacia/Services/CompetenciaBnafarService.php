<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Repositories\DispensacaoMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Repositories\EntradaMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Repositories\SaidaMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Strategies\DispensacaoBnafarStrategy;
use App\Domain\Saude\Farmacia\Strategies\EntradaBnafarStrategy;
use App\Domain\Saude\Farmacia\Strategies\SaidaBnafarStrategy;
use App\Domain\Saude\Farmacia\Validators\DispensacaoBnafarValidator;
use App\Domain\Saude\Farmacia\Validators\EntradaBnafarValidator;
use App\Domain\Saude\Farmacia\Validators\SaidaBnafarValidator;

class CompetenciaBnafarService
{
    /**
     * @param \UnidadeProntoSocorro $unidade
     * @param \DateTime[] $periodo
     * @return array
     */
    public static function validar(\UnidadeProntoSocorro $unidade, array $periodo)
    {
        $procedimentos = [];
        $repository = new EntradaMedicamentoBnafarRepository();
        $validator = new EntradaBnafarValidator();
        $entrada = new EntradaBnafarStrategy($unidade, $repository, $validator);
        $procedimentos[] = $entrada->getSituacaoEnvio($periodo);

        $repository = new SaidaMedicamentoBnafarRepository();
        $validator = new SaidaBnafarValidator();
        $saida = new SaidaBnafarStrategy($unidade, $repository, $validator);
        $procedimentos[] = $saida->getSituacaoEnvio($periodo);

        $repository = new DispensacaoMedicamentoBnafarRepository();
        $validator = new DispensacaoBnafarValidator();
        $dispensacao = new DispensacaoBnafarStrategy($unidade, $repository, $validator);
        $procedimentos[] = $dispensacao->getSituacaoEnvio($periodo);

        return $procedimentos;
    }
}
