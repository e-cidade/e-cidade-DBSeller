<?php

namespace App\Domain\Financeiro\Contabilidade\Requests\Relatorios;

use App\Http\Requests\DBFormRequest;

class RelatorioSuperavitRequest extends DBFormRequest
{

    public function rules()
    {
        return [
            'instituicoes' => ['required', 'array', 'filled'],
            'dataInicial' => ['required','date_format:Y-m-d'],
            'dataFinal' => ['required', 'date_format:Y-m-d', 'after_or_equal:dataInicial'],
            'recursos' => ['array'],
            'apresentarRecurso' => ['required', 'integer'],
            'DB_anousu' => ['required'],
            'DB_instit' => ['required'],
        ];
    }
}
