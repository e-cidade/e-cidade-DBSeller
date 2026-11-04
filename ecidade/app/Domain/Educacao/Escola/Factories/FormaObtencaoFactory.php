<?php

namespace App\Domain\Educacao\Escola\Factories;

use App\Domain\Educacao\Escola\Strategies\ResultadoParcial\MaiorNotaStrategy;
use App\Domain\Educacao\Escola\Strategies\ResultadoParcial\MediaAritmeticaStrategy;
use App\Domain\Educacao\Escola\Strategies\ResultadoParcial\SomaStrategy;
use App\Domain\Educacao\Escola\Strategies\ResultadoParcial\UltimaNotaStrategy;
use ECidade\Enum\Educacao\Escola\FormaObtencaoEnum;
use Exception;

class FormaObtencaoFactory
{
    /**
     * @param $formaObtencao
     * @return UltimaNotaStrategy|MaiorNotaStrategy|MediaAritmeticaStrategy|SomaStrategy
     * @throws Exception
     */
    public static function criarEstrategiaResultadoParcial($formaObtencao)
    {
        switch ($formaObtencao) {
            case FormaObtencaoEnum::MEDIA_ARITMETICA:
                return new MediaAritmeticaStrategy();
            case FormaObtencaoEnum::SOMA:
                return new SomaStrategy();
            case FormaObtencaoEnum::MAIOR_NOTA:
                return new MaiorNotaStrategy();
            case FormaObtencaoEnum::ULTIMA_NOTA:
                return new UltimaNotaStrategy();
//            case FormaObtencaoEnum::ATRIBUIDO:
//                return new AtribuidoStrategy();
//            case FormaObtencaoEnum::MAIOR_NIVEL:
//                return new MaiorNivelStrategy();
//            case FormaObtencaoEnum::ULTIMO_NIVEL:
//                return new UltimoNivelStrategy();
            default:
                throw new Exception('Forma de obtenчуo invсlida');
        }
    }
}
