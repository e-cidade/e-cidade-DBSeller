<?php

namespace App\Domain\Configuracao\Banco\Requests;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BancoPixRequest extends FormRequest
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
     * @param $k00_tipo Indece da tabela arretipo
     *
     * @return array
     */
    public function rules($d90_codban_pix = null)
    {
        $res = [
            "db90_codban"     => [
                "required",
                "alpha_num",
                "max:10",
                "exists:db_bancos,db90_codban",
                // Rule::unique("db_bancos_pix")->ignore($d90_codban_pix, "db90_codban_pix")
            ],
            "db90_tipo_ambiente"  => ["integer"],
            "db90_login"          => ["string", "max:255"],
            "db90_senha"          => ["string", "max:255"],
            "db90_chave_api"      => ["string", "max:255"],
            "db90_chave_pix"      => ["string", "max:255"],
            "db90_numconv"        => ["string", "max:255"],
            "db90_cnpj_municipio" => ["boolean"],
            "db90_cnpj"           => ["string", "max:15", "required_if:db90_cnpj_municipio,=,1"],
        ];
    
        return $res;
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
            "integer" => (":attribute deve ser um inteiro."),
            "boolean" => (":attribute deve ser sim ou não."),
            "required" => ("Código :attribute deve ser informado."),
            "db90_cnpj.required_if" => ("O campo CNPJ deve ser informado."),
        ];
    }
}
