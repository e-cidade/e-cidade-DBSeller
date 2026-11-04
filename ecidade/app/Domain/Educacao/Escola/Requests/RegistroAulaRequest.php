<?php

namespace App\Domain\Educacao\Escola\Requests;

use App\Domain\Educacao\Escola\Models\Turma;
use App\Http\Requests\DBFormRequest;

class RegistroAulaRequest extends DBFormRequest
{
    public function rules()
    {
        $turma = Turma::find($this->get('turma'));
        $dataI = $turma->calendario->ed52_d_inicio;
        $dataF = $turma->calendario->ed52_d_fim;
        return [
            'usuario' => 'required|integer',
            'turma' => 'required|integer',
            'regencia' => 'required|integer',
            'conteudo' => 'required|string',
            'data' => "required|after_or_equal:{$dataI}|before_or_equal:{$dataF}"
        ];
    }

    public function messages()
    {
        $turma = Turma::find($this->get('turma'));
        $dataI = $turma->calendario->ed52_d_inicio->format('d/m/Y');
        $dataF = $turma->calendario->ed52_d_fim->format('d/m/Y');
        return [
            "turma.integer" => "Código da turma não informado.",
            "turma.filled" => "O código da turma informado está vazio.",
            "turma.integer" => "Código da turma deve ser um inteiro.",
            "usuario.integer" => "Código do usuario não informado.",
            "usuario.filled" => "O código do usuario informado está vazio.",
            "usuario.integer" => "Código do usuario deve ser um inteiro.",
            "regencia.integer" => "Código da regencia não informado.",
            "regencia.filled" => "O código da regencia informado está vazio.",
            "regencia.integer" => "Código da regencia deve ser um inteiro.",
            "conteudo.required" => "Conteúdo não informado!",
            "data.after_or_equal" => "A data deve estar no período do calendário escolar, entre {$dataI} e {$dataF}.",
            "data.before_or_equal" => "A data deve estar no período do calendário escolar, entre {$dataI} e {$dataF}."
        ];
    }
}
