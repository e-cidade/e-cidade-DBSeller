<?php

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial;

use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Importar\Importar;
use Exception;

/**
 * Class ImportarFactory
 * @package ECidade\Educacao\Escola\Censo\MatriculaInicial
 */
class ImportarFactory
{
    /**
     * @param $ano
     * @return Importar
     * @throws Exception
     */
    public static function factory($ano)
    {
        if ($ano >= 2019 && $ano <= 2022) {
            return new Importar();
        }

        throw new Exception("Não existe layout previsto para o ano de {$ano}.");
    }
}
