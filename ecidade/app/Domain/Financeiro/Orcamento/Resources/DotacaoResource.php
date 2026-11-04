<?php

namespace App\Domain\Financeiro\Orcamento\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class DotacaoResource
{

    public static function toArray(LengthAwarePaginator $dados)
    {
        $dotacoes = [];
        foreach ($dados as $dado) {
            $dotacoes[] = (object) [
                "exercicio" => $dado->o58_anousu,
                "reduzido" => $dado->o58_coddot,
                "saldoInicial" => $dado->o58_valor,
                "criacao" => $dado->o58_datacriacao,
                "esferaOrcamentaria" => $dado->o58_esferaorcamentaria,
                "funcionalProgramatica" => self::funcionalProgramatica($dado),
                "orgao" => self::toStd($dado->o58_orgao, $dado->o40_descr),
                "unidade" => self::toStd($dado->o58_unidade, $dado->o41_descr),
                "funcao" => self::toStd($dado->o58_funcao, $dado->o52_descr),
                "subfuncao" => self::toStd($dado->o58_subfuncao, $dado->o53_descr),
                "programa" => self::toStd($dado->o58_programa, $dado->o54_descr),
                "projeto" => self::toStd($dado->o58_projativ, $dado->o55_descr),
                "elemento" => self::toObjectElemento($dado),
                "recurso" => self::toObjectRecurso($dado),
                "cp" => self::toStd($dado->o58_concarpeculiar, $dado->c58_descr),
                "instituicao" => self::toStd($dado->o58_instit, $dado->nomeinst),
            ];
        }
        return (object)[
            'totalRegistros' => $dados->total(),
            'dotacoes' => $dotacoes
        ];
    }

    private static function funcionalProgramatica($dado)
    {
        return sprintf(
            '%s.%s.%s.%s.%s.%s.%s.%s.%s.%s',
            str_pad($dado->o58_orgao, 3, '0', STR_PAD_LEFT),
            str_pad($dado->o58_unidade, 3, '0', STR_PAD_LEFT),
            str_pad($dado->o58_funcao, 3, '0', STR_PAD_LEFT),
            str_pad($dado->o58_subfuncao, 3, '0', STR_PAD_LEFT),
            str_pad($dado->o58_programa, 4, '0', STR_PAD_LEFT),
            str_pad($dado->o58_projativ, 4, '0', STR_PAD_LEFT),
            str_pad($dado->o58_codele, 13, '0', STR_PAD_RIGHT),
            str_pad($dado->codigo_siconfi, 4, '0', STR_PAD_LEFT),
            str_pad($dado->o15_recurso, 4, '0', STR_PAD_LEFT),
            str_pad($dado->o15_complemento, 4, '0', STR_PAD_LEFT)
        );
    }

    private static function toStd($codigo, $descicao)
    {
        return (object)[
            "codigo" => $codigo,
            "descricao" => $descicao
        ];
    }

    private static function toObjectElemento($dado)
    {
        return (object)[
            "codigo" => $dado->o58_codele,
            "elemento" => $dado->o56_elemento,
            "descricao" => $dado->o56_descr
        ];
    }

    private static function toObjectRecurso($dado)
    {
        return (object) [
            "orctiporec_id" => $dado->o58_codigo,
            "siconfi" => $dado->codigo_siconfi,
            "gestao" => $dado->gestao,
            "subrecurso" => $dado->o15_recurso,
            "complemento" => self::toStd($dado->o15_complemento, $dado->o200_descricao),
            "descricao_fr" => $dado->descricao,
            "descricao" => $dado->o15_descr,
            "apresentacao" => sprintf(
                '%s - %s - %s - %s',
                $dado->codigo_siconfi,
                $dado->o15_recurso,
                $dado->o15_complemento,
                $dado->o15_descr
            )
        ];
    }
}
