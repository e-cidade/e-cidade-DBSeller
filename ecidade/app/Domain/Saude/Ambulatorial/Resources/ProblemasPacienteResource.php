<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use Illuminate\Database\Eloquent\Collection;
use App\Domain\Saude\Ambulatorial\Models\ProblemaPaciente;
use App\Domain\Saude\Ambulatorial\Requests\SalvarProblemasPacienteRequest;
use App\Domain\Saude\Ambulatorial\Resources\ProntuarioProblemasPacienteResource;

/**
 * @package App\Domain\Saude\Ambulatorial\Resources
 */
class ProblemasPacienteResource
{
    public static function requestToObject(SalvarProblemasPacienteRequest $request)
    {
        return (object)[
            'id' => $request->get('id'),
            'paciente' => $request->get('paciente'),
            'problema' => $request->get('problema'),
            'usuario' => $request->get('DB_id_usuario'),
            'dataInicio' => $request->get('dataInicio') !== '' ? $request->get('dataInicio') : null,
            'dataFim' => $request->get('dataFim') !== '' ? $request->get('dataFim') : null,
            'ativo' => $request->get('ativo')
        ];
    }

    public static function toObject(ProblemaPaciente $model)
    {
        $prontuarioProblema = $model->prontuarios()->withPivot('*')->orderByDesc('s171_prontuario')->get();

        return (object)[
            'id' => $model->s170_id,
            'idProblema' => $model->s170_problema,
            'problema' => $model->problema->s169_descricao,
            'paciente' => $model->paciente->z01_v_nome,
            'idUsuario' => $model->s170_usuario,
            'profissional' => $model->usuario->nome,
            'data' => $model->s170_data->format('d/m/Y'),
            'ativo' => $model->s170_ativo,
            'situacao' => $model->s170_ativo ? 'ATIVO' : 'RESOLVIDO',
            'dataInicio' => $model->s170_data_inicio ? $model->s170_data_inicio->format('d/m/Y') : '',
            'dataFim' => $model->s170_data_fim ? $model->s170_data_fim->format('d/m/Y') : '',
            'consultas' => ProntuarioProblemasPacienteResource::toArray($prontuarioProblema)
        ];
    }

    public static function toArray(Collection $collection)
    {
        $dados = [];

        foreach ($collection as $model) {
            $dados[] = static::toObject($model);
        }

        return $dados;
    }
}
