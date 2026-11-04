<?php

namespace App\Domain\Saude\Farmacia\Factories;

use App\Domain\Saude\Farmacia\Contracts\ProcedimentoBnafar;
use App\Domain\Saude\Farmacia\Repositories\DispensacaoMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Repositories\EntradaMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Repositories\SaidaMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Strategies\DispensacaoBnafarStrategy;
use App\Domain\Saude\Farmacia\Strategies\EntradaBnafarStrategy;
use App\Domain\Saude\Farmacia\Strategies\SaidaBnafarStrategy;
use App\Domain\Saude\Farmacia\Validators\DispensacaoBnafarValidator;
use App\Domain\Saude\Farmacia\Validators\EntradaBnafarValidator;
use App\Domain\Saude\Farmacia\Validators\SaidaBnafarValidator;
use DBCompetencia;
use Exception;
use UnidadeProntoSocorro;

class IntegracaoBnafarFactory
{
    const ENTRADA = 1;
    const SAIDA = 2;
    const DISPENSACAO = 3;

    /**
     * @param integer $tipo
     * @param UnidadeProntoSocorro $unidade
     * @return ProcedimentoBnafar
     * @throws Exception
     */
    public static function getStrategy($tipo, UnidadeProntoSocorro $unidade)
    {
        switch ($tipo) {
            case self::ENTRADA:
                $repository = new EntradaMedicamentoBnafarRepository();
                $validator = new EntradaBnafarValidator();
                return new EntradaBnafarStrategy($unidade, $repository, $validator);
            case self::SAIDA:
                $repository = new SaidaMedicamentoBnafarRepository();
                $validator = new SaidaBnafarValidator();
                return new SaidaBnafarStrategy($unidade, $repository, $validator);
            case self::DISPENSACAO:
                $repository = new DispensacaoMedicamentoBnafarRepository();
                $validator = new DispensacaoBnafarValidator();
                return new DispensacaoBnafarStrategy($unidade, $repository, $validator);
            default:
                throw new Exception('Procedimento não cadastrado');
        }
    }
}
