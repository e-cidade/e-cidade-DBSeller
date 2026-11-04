<?php

namespace App\Domain\Tributario\Arrecadacao\Requests;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class ValidacaoPixRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "tipo_debito" => ["required", "integer"],
            "modelo" => ["required", "integer"],
            "codigo_arrecadacao" => ["required", "integer"],
            "convenio" => ["required", "integer"],
            "vencimento" => ["required", "date"],
        ];
    }

    public function messages()
    {
        return [
            "tipo_debito.required" => utf8_encode("Tipo de Débito não informado."),
            "modelo.required" => utf8_encode("Modelo não informado."),
            "codigo_arrecadacao.required" => utf8_encode("Códido de arrecadação não informado."),
            "convenio.required" => utf8_encode("Códido do convenio não informado"),
            "vencimento.required" => utf8_encode("Data vencimento não informada"),
        ];
    }
    /**
     * @param array $errors
     * @return DBJsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        $mensagem = utf8_decode($errors[array_keys($errors)[0]][0]);
        return new DBJsonResponse($errors, $mensagem, 406);
    }
}
