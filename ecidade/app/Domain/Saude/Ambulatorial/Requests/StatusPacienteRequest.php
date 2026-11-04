<?php

namespace App\Domain\Saude\Ambulatorial\Requests;

use App\Http\Requests\DBFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int|null $faa
 * @property string|null $paciente
 * @property int|null $classificacaoRiscoSelecionado
 * @property int|null $motivoAtendimentoSelecionado
 * @property int|null $setorSelecionado
 * @property int|null $profissionalEncaminhadoSelecionado
 * @property int|null $statusPacienteSelecionado
 * @property int|null $profissionalAtendimentoSelecionado
 * @property string|null $dataInicial
 * @property string|null $dataFinal
 * @property int|null $sd24_c_digitada
*/
class StatusPacienteRequest extends DBFormRequest
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        $situacoes = [1, 2, 3, 5];
        return [
            'faa' => ['integer'],
            'paciente' => ['string'],
            'classificacaoRisco' => ['integer', 'exists:classificacaorisco,sd78_codigo'],
            'motivoAtendimento' => ['integer', 'exists:sau_motivoatendimento,s144_i_codigo'],
            'setor' => ['integer', 'exists:setorambulatorial,sd91_codigo'],
            'profissionalEncaminhado' => ['integer', 'exists:especmedico,sd27_i_codigo'],
            'profissionalAtendimento' => ['integer', 'exists:especmedico,sd27_i_codigo'],
            'situacao' => ['integer', Rule::in($situacoes)],
            'dataInicial' => ['date'],
            'dataFinal' => ['date'],
            '$sd24_c_digitada' => ['integer', 'max:1']
        ];
    }
}
