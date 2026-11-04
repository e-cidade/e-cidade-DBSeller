<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Requests;

use App\Http\Requests\DBFormRequest;

class RelatorioRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'codigo' => ['required_if:tipo,2', 'integer'],
            'grupo' => ['required', 'integer'],
            'tipo' => ['required', 'integer'],
            'origem' => ['required', 'integer'],
            'sql' => ['required', 'string'],

            'campos' => ['present', 'array'],
            'campos.*.nome' => ['required', 'string'],
            'campos.*.alias' => ['required', 'string'],
            'campos.*.largura' => ['required', 'integer'],
            'campos.*.alinhamento' => ['required', 'string'],
            'campos.*.alinhamentoCabecalho' => ['required', 'string'],
            'campos.*.mascara' => ['required', 'string'],
            'campos.*.totalizar' => ['required', 'string'],
            'campos.*.quebra' => ['required', 'boolean'],

            'ordem' => ['present', 'array'],
            'ordem.*.nome' => ['required', 'string'],
            'ordem.*.alias' => ['required', 'string'],
            'ordem.*.tipo' => ['required', 'string'],

            'layout' => ['required', 'array'],
            'layout.nome' => ['required', 'string'],
            'layout.versao' => ['required', 'string'],
            'layout.orientacao' => ['required', 'string'],
            'layout.formato' => ['required', 'string'],
            'layout.layout' => ['required', 'string'],
            'layout.tipoSaida' => ['required', 'string'],
            'layout.margem' => ['required', 'array'],
            'layout.margem.direita' => ['required', 'integer'],
            'layout.margem.esquerda' => ['required', 'integer'],
            'layout.margem.inferior' => ['required', 'integer'],
            'layout.margem.superior' => ['required', 'integer'],

            'variaveis' => ['present', 'array'],
            'variaveis.*.nome' => ['required', 'string'],
            'variaveis.*.label' => ['present'],
            'variaveis.*.default' => ['present'],
            'variaveis.*.tipo' => ['required', 'string'],
            'variaveis.*.sql' => ['present'],

            'filtros' => ['present', 'array'],
            'filtros.*.operador' => ['required', 'string'],
            'filtros.*.campo' => ['required', 'string'],
            'filtros.*.condicao' => ['required', 'string'],
            'filtros.*.valor' => ['required', 'string']
        ];
    }
}
