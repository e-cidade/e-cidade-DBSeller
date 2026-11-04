<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Secretaria\Models\ParametrosGlobais;

class HabilidadesBNCCFundamentalResource
{
    public static function toResponse($habilidades)
    {
        $configuracao = ParametrosGlobais::all()->first();

        $dados = [];
        foreach ($habilidades as $habilidade) {
            $unidadeTematica = md5($habilidade->ed148_unidade_tematica);
            if (!array_key_exists($unidadeTematica, $dados)) {
                $std = (object)[
                    'nome' => $habilidade->ed148_unidade_tematica,
                    'objetosConhecimento' => []
                ];
                $dados[$unidadeTematica] = $std;
            }

            $objetoConhecimento = md5($habilidade->ed148_objeto_conhecimento);

            if (!array_key_exists($objetoConhecimento, $dados[$unidadeTematica]->objetosConhecimento)) {
                $stdObj = (object)[
                    'nome' => $habilidade->ed148_objeto_conhecimento,
                    'habilidades' => []
                ];

                $dados[$unidadeTematica]->objetosConhecimento[$objetoConhecimento] = $stdObj;
            }

            $dadosHabilidade = (object)[
                'codigo' => $habilidade->ed148_codigo,
                'nome' => $habilidade->ed148_habilidade
            ];

            if ($configuracao->isReferencialCurricularEstadual) {
                $dadosHabilidade->referencial = $habilidade->referenciaisCurricularEstadual;
            }

            $dados[$unidadeTematica]->objetosConhecimento[$objetoConhecimento]->habilidades[] = $dadosHabilidade;
        }

        $dados = array_values($dados);
        foreach ($dados as $unidadeTematica) {
            $unidadeTematica->objetosConhecimento = array_values($unidadeTematica->objetosConhecimento);
        }

        return $dados;
    }
}
