<?php

namespace App\Domain\Financeiro\Contabilidade\Resources;

use App\Domain\Financeiro\Contabilidade\Mappers\LancamentoManualAtributosMinimos;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ConsultaReduzidosResource
{
    /**
     * @param LengthAwarePaginator $dados
     * @return array
     */
    public static function toArray(LengthAwarePaginator $dados)
    {
        $reduzidos = [];
        foreach ($dados as $dado) {
            $reduzido = (object) [
                "codcon" => $dado->c60_codcon,
                "exercicio" => $dado->c60_anousu,
                "estrutural" => $dado->c60_estrut,
                "descricao" => $dado->c60_descr,
                "c60_codigo" => $dado->c60_codigo,
                "recurso_id" => $dado->c61_codigo,
                "reduzido" => $dado->c61_reduz,
                "instituicao" => $dado->c61_instit,
                "atributos" => LancamentoManualAtributosMinimos::atributo($dado->c60_estrut)
            ];

            if (isset($dado->uniao)) {
                $reduzido->pcasp = (object) [
                    "conta" => $dado->conta,
                    "uniao" => $dado->uniao,
                    "nome" => $dado->nome,
                    "funcao" => $dado->funcao,
                    "natureza" => $dado->natureza,
                    "sintetica" => $dado->sintetica,
                    "indicador" => $dado->indicador,
                    "informacoescomplementares" => $dado->informacoescomplementares,
                ];
            }


            if (isset($dado->atributos)) {
                $reduzido->atributos = $dado->atributos;
            }
            $reduzidos[] = $reduzido;
        }

        return [
            'totalRegistros' => $dados->total(),
            'reduzidos' => $reduzidos
        ];
    }
}
