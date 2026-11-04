<?php

namespace App\Domain\Configuracao\RelarorioLegal\Resources;

class RelatoriosLegaisResource
{
    public static function toArray($dados)
    {
        $retornar = [];
        foreach ($dados as $dado) {
            $retornar[] = (object)[
                "codigo" => $dado->codigo,
                "descricao" => $dado->descricao,
                "periodos" => self::toPeriodos($dado->periodos)
            ];
        }
        return $retornar;
    }

    private static function toPeriodos($periodos)
    {
        $retornar = [];
        foreach ($periodos as $periodo) {
            $retornar [] = (object)[
                "codigo" => $periodo->o114_sequencial,
                "descricao" => $periodo->o114_descricao,
                "diaInicial" => $periodo->o114_diainicial,
                "mesInicial" => $periodo->o114_mesinicial,
                "mesFinal" => $periodo->o114_mesfinal,
                "sigla" => $periodo->o114_sigla,
                "ordem" => $periodo->o114_ordem,
            ];
        }
        return $retornar;
    }
}
