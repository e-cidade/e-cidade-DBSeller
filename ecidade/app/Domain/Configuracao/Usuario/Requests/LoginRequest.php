<?php

namespace App\Domain\Configuracao\Usuario\Requests;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Http\Requests\DBFormRequest;

const MENSAGEM = 'configuracao.configuracao.abrir.';

/**
 * @property int $client_id
 * @property string $client_secret
 * @property string $username
 * @property string $password
 * @property string $conteudoCaptcha
 * @property string $DB_HOST
 * @property string $DB_DATABASE
 * @property string $DB_PORT
 * @property string $DB_USERNAME
 * @property string $DB_PASSWORD
 *
 * @property Usuario|null $usuario
 */
class LoginRequest extends DBFormRequest
{

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['present', 'string'],
            'conteudoCaptcha' => ['string'],
            'DB_HOST' => ['string'],
            'DB_DATABASE' => ['string'],
            'DB_PORT' => ['string'],
            'DB_USERNAME' => ['string'],
            'DB_PASSWORD' => ['string']
        ];
    }

    public function messages()
    {
        return [
            'username.required' => mb_convert_encoding(_M(MENSAGEM . 'login_invalido'), 'UTF-8', 'ISO-8859-1')
        ];
    }
}
