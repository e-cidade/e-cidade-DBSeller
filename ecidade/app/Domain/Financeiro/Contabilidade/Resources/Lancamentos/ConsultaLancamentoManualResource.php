<?php

namespace App\Domain\Financeiro\Contabilidade\Resources\Lancamentos;

use App\Domain\Financeiro\Contabilidade\Models\ResumoConsultaLancamentoManual;
use Illuminate\Database\Eloquent\Collection;
use JSON;

class ConsultaLancamentoManualResource
{
    public static function organizaPorLote(Collection $dados)
    {
        $lotes = [];

        foreach ($dados as $dado) {
            if (!array_key_exists($dado->c160_codigo, $lotes)) {
                $lotes[$dado->c160_codigo] = (object)[
                    "codigo" => $dado->c160_codigo,
                    "lote" => $dado->c160_lote,
                    "data" => $dado->c70_data,
                    "valor" => 0,
                    "lancamentos" => [],
                ];
            }

            $lotes[$dado->c160_codigo]->valor += $dado->c70_valor;
            $lotes[$dado->c160_codigo]->lancamentos[] = self::toLancamneto($dado);
        }
        return $lotes;
    }

    private static function toLancamneto($dado)
    {
        return (object)[
            "lancamento" => $dado->c70_codlan,
            "exercicio" => $dado->c70_anousu,
            "valor" => $dado->c70_valor,
            "data" => $dado->c70_data,
            "instituicao" => $dado->c02_instit,
            "documento" => self::toDocumento($dado),
            "credito" => self::toConta($dado->credito, $dado->recurso_credito),
            "debito" => self::toConta($dado->debito, $dado->recurso_debito),
            "historico" => self::toHistorico($dado),
            "dotacao" => self::toDotacao($dado),
            "cgm" => self::toCgm($dado),
            "empenho" => self::toEmpenho($dado),
            "receita" => self::toReceita($dado),
            'observacao' => $dado->c72_complem,
            'temExtorno' => $dado->tem_extorno
        ];
    }

    private static function toDocumento($dado)
    {
        return (object)[
            "documento" => $dado->c53_coddoc,
            "descricao" => $dado->c53_descr,
            "tipo" => $dado->c53_tipo,
        ];
    }

    private static function toConta($string, $recurso)
    {
        $conta = JSON::create()->parse($string);

        return (object)[
            "reduzido" => $conta->reduzido,
            "codcon" => $conta->codcon,
            "codigo" => $conta->codigo,
            "estrutural" => $conta->estrutural,
            "descricao" => $conta->descricao,
            "recurso" => $recurso,
        ];
    }

    private static function toHistorico($dado)
    {
        return (object)[
            "codigo" => $dado->c69_codhist,
            "descricao" => $dado->c50_descr
        ];
    }

    private static function toDotacao(ResumoConsultaLancamentoManual $dado)
    {
        return (object)['reduzido' => $dado->c73_coddot];
    }

    private static function toCgm($dado)
    {
        return (object)['numcgm' => $dado->c76_numcgm];
    }

    private static function toEmpenho($dado)
    {
        return (object)['numemp' => $dado->c75_numemp];
    }

    private static function toReceita($dado)
    {
        return (object)['reduzido' => $dado->c74_codrec];
    }
}
