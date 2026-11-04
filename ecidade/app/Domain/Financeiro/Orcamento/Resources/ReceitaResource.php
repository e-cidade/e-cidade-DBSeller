<?php

namespace App\Domain\Financeiro\Orcamento\Resources;

use ECidade\Enum\Financeiro\Orcamento\EsferaOrcamentariaEnum;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceitaResource
{

    public static function toArray(LengthAwarePaginator $dados)
    {
        $receitas = [];
        foreach ($dados as $dado) {
            $receitas[] = (object)[
                "exercicio" => $dado->o70_anousu,
                "reduzido" => $dado->o70_codrec,
                "saldoInicial" => $dado->o70_valor,
                "naturezaReceita" => self::toObjectNaturezaReceita($dado),
                "recurso" => self::toObjectRecurso($dado),
                "instituicao" => self::toStd($dado->o70_instit, $dado->nomeinst),
                "cp" => self::toStd($dado->o70_concarpeculiar, $dado->c58_descr),
                "criacao" => $dado->o70_datacriacao,
                "unidade" => self::toObjectUnidade($dado),
                "esferaOrcamentaria" => self::toObjecEsfera($dado),

            ];
        }

        return (object)[
            'totalRegistros' => $dados->total(),
            'receitas' => $receitas
        ];
    }

    private static function toObjectNaturezaReceita($dado)
    {
        return (object)[
            'codigo' => $dado->o70_codfon,
            'estrutural' => $dado->o57_fonte,
            'descricao' => $dado->o57_descr,
        ];
    }

    private static function toObjectRecurso($dado)
    {
        return (object)[
            "orctiporec_id" => $dado->o15_codigo,
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

    private static function toStd($codigo, $descicao)
    {
        return (object)[
            "codigo" => $codigo,
            "descricao" => $descicao
        ];
    }

    private static function toObjectUnidade($dado)
    {
        return (object)[
            "orgao" => $dado->o70_orcorgao,
            "unidade" => $dado->o70_orcunidade,
            "descricao" => $dado->o41_descr,
            "orgaoUnidade" => sprintf(
                '%s%s',
                str_pad($dado->o70_orcorgao, 2, '0', STR_PAD_LEFT),
                str_pad($dado->unidade, 2, '0', STR_PAD_LEFT)
            )
        ];
    }

    private static function toObjecEsfera($dado)
    {
        return (object) [
            'codigo' => $dado->o70_esferaorcamentaria,
            'descricao' => (new EsferaOrcamentariaEnum((int)$dado->o70_esferaorcamentaria))->name()
        ];
    }
}
