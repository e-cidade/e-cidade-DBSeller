<?php


namespace App\Domain\Financeiro\Contabilidade\Requests\Procedimento;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class ManutencaoFonteRecursoDespesaRequest
 * @package App\Domain\Financeiro\Contabilidade\Requests\Procedimento
 */
class ManutencaoFonteRecursoReceitaRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'gestao' => 'nullable|string|required_without:codigoReceita',
            'subrecurso' => 'nullable|string|required_without:codigoReceita',
            'codigoReceita' => 'nullable|integer|required_without:gestao',
            'idEmpenho' => 'nullable|string',
            'dataInicio' => 'date|required',
            'dataFinal' => 'date|required',
        ];
    }

    /**
     * @param array $errors
     * @return DBJsonResponse|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        $mensagem = $errors[array_keys($errors)[0]][0];
        return new DBJsonResponse($errors, $mensagem, 406, false);
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'gestao.string' => 'A Fonte de Gestão deve ser um código válido.',
            'gestao.required_without' => 'A Fonte de Gestão é obrigatório quando não informado a receita.',
            'subrecurso.string' => 'O subrecurso deve ser um código válido.',
            'subrecurso.required_without' => 'O subrecurso é obrigatório quando não informado a receita.',
            'codigoReceita.required_without' => 'O código da receita deve ser preenchido se não informado o recurso.',
            'dataInicio.date' => "Data Inicial deve ser uma data válida e estar no formato Y-m-d.",
            'dataFinal.date' => "Data Final deve ser uma data válida e estar no formato Y-m-d.",
            'dataInicio.required' => "Data Inicial é obrigatória.",
            'dataFinal.required' => "Data Final é obrigatório.",
        ];
    }
}
