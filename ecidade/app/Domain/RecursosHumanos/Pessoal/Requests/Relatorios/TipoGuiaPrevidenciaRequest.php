<?php


namespace App\Domain\RecursosHumanos\Pessoal\Requests\Relatorios;

use App\Http\Requests\DBFormRequest;

class TipoGuiaPrevidenciaRequest extends DBFormRequest
{
    public function rules()
    {
        return [
            'ano' => ['required', 'int', 'filled'],
            'mes' => ['required', 'int', 'filled'],
            'tipo' => ['required', 'string', 'filled'],
            'codigoLotacao' => ['required_if:tipo,==,lotacao', 'filled'],
            'codigoSelecao' => ['required_if:tipo,==,selecao', 'filled'],
            'dataPagamento' => ['required','date_format:d/m/Y'],
            'codigoPagamento' => ['required', 'int', 'filled'],
            'arquivo' => ['required', 'string', 'filled'],
            'tipoGuia' => ['required', 'string', 'filled'],
            'tabelasPrevidencia' => ['required', 'array'],
        ];
    }
}
