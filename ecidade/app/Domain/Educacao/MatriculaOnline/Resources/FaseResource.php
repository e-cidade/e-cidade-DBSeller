<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Enums\PublicosAlvosEnum;
use App\Domain\Educacao\MatriculaOnline\Models\Ciclo;
use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use Carbon\Carbon;

class FaseResource
{
    public static function toResponse(Fase $fase)
    {
        return (object) [
            'codigo' => $fase->mo04_codigo,
            'descricao' => $fase->mo04_desc,
            'dataCorte' => $fase->mo04_datacorte->format('m/d/Y'),
            'dataFim' => $fase->mo04_dtfim->format('m/d/Y'),
            'dataInicio' => $fase->mo04_dtini->format('m/d/Y'),
            'ciclo' => CiclosResource::toResponse(Ciclo::find($fase->mo04_ciclo)),
            'isProcessada' => $fase->mo04_processada,
            'isEncerrada' => $fase->mo04_encerrada,
            'publicosAlvo' => collect(json_decode($fase->mo04_publicos_alvo))->map(function ($publico) {
                return (new PublicosAlvosEnum(intval($publico)))->toOject();
            }),
            'exibeEscolaOrigem' => $fase->mo04_exibe_escola_origem,
            'horaInicio' => $fase->mo04_hora_inicial,
            'horaFim' => $fase->mo04_hora_final,
            'opcoesEscolha' => $fase->mo04_opcoes_escolha,
            'opcoesObrigatoria' => $fase->mo04_opcoes_obrigatoria
        ];
    }

    public static function toArrayModel($parametros)
    {
        $dados = [
            'mo04_codigo' => isset($parametros['codigo']) ? $parametros['codigo'] : null,
            'mo04_desc' => $parametros['descricao'],
            'mo04_anousu' => Carbon::createFromFormat('d/m/Y', $parametros['dataCorte'])->year,
            'mo04_datacorte' => Carbon::createFromFormat('d/m/Y', $parametros['dataCorte'])
                ->format('Y-m-d'),
            'mo04_dtfim' =>  Carbon::createFromFormat('d/m/Y', $parametros['dataFim'])
                ->format('Y-m-d'),
            'mo04_dtini' =>  Carbon::createFromFormat('d/m/Y', $parametros['dataInicio'])
                ->format('Y-m-d'),
            'mo04_ciclo' => $parametros['ciclo'],
            'mo04_processada' => isset($parametros['isProcessada']) ? $parametros['isProcessada'] : false,
            'mo04_encerrada' =>  isset($parametros['isEncerrada']) ? $parametros['isProcessada'] : false,
            'mo04_publicos_alvo' => json_encode($parametros['publicosAlvo']),
            'mo04_exibe_escola_origem' => $parametros['exibeEscolaOrigem'] == 1,
            'mo04_opcoes_escolha' => $parametros['opcoesEscolha'],
            'mo04_opcoes_obrigatoria' => $parametros['opcoesObrigatoria']
        ];
        if (isset($parametros['horaInicio'])) {
            $dados['mo04_hora_inicial'] = $parametros['horaInicio'];
        }
        if (isset($parametros['horaFim'])) {
            $dados['mo04_hora_final'] = $parametros['horaFim'];
        }
        return $dados;
    }
}
