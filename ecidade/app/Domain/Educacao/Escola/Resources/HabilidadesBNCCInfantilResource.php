<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Secretaria\Models\ParametrosGlobais;

class HabilidadesBNCCInfantilResource
{
    public static function toResponse($habilidades)
    {
        $configuracao = ParametrosGlobais::all()->first();
        $dados = [];
        foreach ($habilidades as $habilidade) {
            $unidadeTematica = md5($habilidade->ed148_unidade_tematica);
            if (!array_key_exists($unidadeTematica, $dados)) {
                $std = (object)[
                    'nome' => $habilidade->ed147_disciplina,
                    'objetosConhecimento' => []
                ];
                $dados[$unidadeTematica] = $std;
            }

            $objetoConhecimento = md5($habilidade->ed147_faixa_etaria);

            if (!array_key_exists($objetoConhecimento, $dados[$unidadeTematica]->objetosConhecimento)) {
                $stdObj = (object)[
                    'nome' => $habilidade->ed147_faixa_etaria,
                    'habilidades' => []
                ];

                $dados[$unidadeTematica]->objetosConhecimento[$objetoConhecimento] = $stdObj;
            }

            $dadosHabilidade = (object)[
                'codigo' => $habilidade->ed147_codigo,
                'nome' => $habilidade->ed147_habilidade
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
