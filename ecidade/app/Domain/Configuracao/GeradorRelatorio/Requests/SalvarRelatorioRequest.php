<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Requests;

use App\Domain\Configuracao\GeradorRelatorio\Enums\TipoVisualizacaoRelatorioEnum;
use App\Http\Requests\DBFormRequest;
use Illuminate\Validation\Rule;

class SalvarRelatorioRequest extends DBFormRequest
{
    public function rules()
    {
        $tiposVisualizacao = TipoVisualizacaoRelatorioEnum::toArray();
        return [
            'grupo' => ['required', 'integer'],
            'tipo' => ['required', 'integer'],
            'origem' => ['required', 'integer'],
            'tipoVisualizacao' => ['required', 'integer', Rule::in($tiposVisualizacao)],
            'sql' => ['required', 'string'],

            'campos' => ['required', 'array'],
            'campos.*.codigo' => ['present', 'integer'],
            'campos.*.nome' => ['required', 'string'],
            'campos.*.alias' => ['required', 'string'],
            'campos.*.largura' => ['required', 'integer'],
            'campos.*.alinhamento' => ['required', 'string'],
            'campos.*.alinhamentoCabecalho' => ['required', 'string'],
            'campos.*.mascara' => ['required', 'string'],
            'campos.*.totalizar' => ['required', 'string'],
            'campos.*.quebra' => ['required', 'boolean'],

            'ordem' => ['present', 'array'],
            'ordem.*.codigo' => ['present', 'integer'],
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
            'layout.margem' => ['present', 'array'],
            'layout.margem.direita' => ['required_if:layout.tipoSaida,pdf', 'integer'],
            'layout.margem.esquerda' => ['required_if:layout.tipoSaida,pdf', 'integer'],
            'layout.margem.inferior' => ['required_if:layout.tipoSaida,pdf', 'integer'],
            'layout.margem.superior' => ['required_if:layout.tipoSaida,pdf', 'integer'],

            'variaveis' => ['present', 'array'],
            'variaveis.*.nome' => ['required', 'string'],
            'variaveis.*.label' => ['required', 'string'],
            'variaveis.*.default' => ['present', 'string'],
            'variaveis.*.tipo' => ['required', 'string'],
            'variaveis.*.sql' => ['present', 'string'],

            'filtros' => ['present', 'array'],
            'filtros.*.operador' => ['required', 'string'],
            'filtros.*.campo' => ['required', 'string'],
            'filtros.*.condicao' => ['required', 'string'],
            'filtros.*.valor' => ['required', 'string']
        ];
    }

    public function messages()
    {
        /** @todo revisar mensagens */
        return [
            'campos.*.nome.required' => 'É obrigatório informar o nome do campo.',
            'campos.*.alias.required' => 'É obrigatório informar o alias do campo.',
            'campos.*.largura.required' => 'É obrigatório informar uma largura para o campo.',
            'campos.*.alinhamento.required' => 'É obrigatório informar um tipo de alinhamento para o campo.',
            'campos.*.alinhamentoCabecalho.required' =>
                'É obrigatório informar um tipo de alinhamento do cabecalho do campo.',
            'campos.*.mascara.required' => 'É obrigatório informar uma mascara para o campo.',
            'campos.*.totalizar.required' => 'É obrigatório informar se o campo deve totalizar ou não.',
            'campos.*.quebra.required' => 'É obrigatório informar se o campo deve quebrar ou não.',
        ];
    }
}
