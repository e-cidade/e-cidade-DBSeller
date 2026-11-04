<?php

namespace App\Domain\Educacao\Escola\Requests;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransposicaoCandidatosRequest extends FormRequest
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
            'turma' => 'required|integer',
            'etapaDestino' => 'required|integer',
            'anoDestino' => 'required|integer',
        ];
    }

    /**
     * @param array $errors
     * @return DBJsonResponse|JsonResponse|Response
     */
    public function response(array $errors)
    {
        $mensagem = $errors[array_keys($errors)[0]][0];
        return new DBJsonResponse($errors, $mensagem, 406);
    }
}
