<?php
namespace App\Domain\Patrimonial\Ouvidoria\Requests\Atendimento\Atendimento;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Requests\BaseFormRequest;
use ECidade\Lib\Session\DefaultSession;

class AprovarAtendimento extends BaseFormRequest
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
            DefaultSession::DB_INSTIT => 'required|filled|integer',
            DefaultSession::DB_CODDEPTO => 'required|filled|integer',
            DefaultSession::DB_ID_USUARIO => 'required|filled|integer',
            'numeroProcesso' => 'required|filled|integer',
            'anoProcesso' => 'required|filled|integer',
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

    /**
     * @return array
     */
    public function messages()
    {
        return [
            DefaultSession::DB_INSTIT . ".required" => utf8_encode(
                "Código da instituicao não informado."
            ),DefaultSession::DB_INSTIT . ".filled" => utf8_encode(
                "O código da instituicao informado está vazio."
            ),DefaultSession::DB_INSTIT . ".integer" => utf8_encode(
                "Código inválido da instituicao."
            ),DefaultSession::DB_CODDEPTO . ".required" => utf8_encode(
                "Código do departamento não informado."
            ),DefaultSession::DB_CODDEPTO . ".filled" => utf8_encode(
                "O código do departamento informado está vazio."
            ),DefaultSession::DB_CODDEPTO . ".integer" => utf8_encode(
                "Código inválido do departamento."
            ),DefaultSession::DB_ID_USUARIO . ".required" => utf8_encode(
                "Código do usuario não informado."
            ),DefaultSession::DB_ID_USUARIO . ".filled" => utf8_encode(
                "O código do usuario informado está vazio."
            ),DefaultSession::DB_ID_USUARIO . ".integer" => utf8_encode(
                "Código inválido do usuario."
            ),
            "numeroProcesso.required" => utf8_encode("Código da Numero de Processo não informado."),
            "numeroProcesso.filled" => utf8_encode("O código da Numero de Processo informado está vazio."),
            "numeroProcesso.integer" => utf8_encode("Código inválido da Numero de Processo."),
            "anoProcesso.required" => utf8_encode("Código do anoProcesso não informado."),
            "anoProcesso.filled" => utf8_encode("O código do anoProcesso informado está vazio."),
            "anoProcesso.integer" => utf8_encode("Código inválido do anoProcesso."),
        ];
    }
}
