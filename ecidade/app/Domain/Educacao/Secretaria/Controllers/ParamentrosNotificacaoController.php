<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Secretaria\Models\ParametrosNotificacao;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ParamentrosNotificacaoController extends Controller
{
    public function show()
    {
        $parametros = ParametrosNotificacao::first();

        return new DBJsonResponse($parametros);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function update(Request $request)
    {
        $codigo = $request->get('codigo');

        try {
            $parametros = ParametrosNotificacao::findOrFail($codigo);
        } catch (Exception $exception) {
            throw new Exception("Erro ao buscar parametros (Código: {$codigo})");
        }

        $parametros->ed177_notificar_escolas = $request->get('notificar-escolas');
        $parametros->ed177_notificar_secretaria = $request->get('notificar-secretaria');
        $parametros->ed177_email_secretaria = $request->get('email-secretaria');
        $parametros->save();

        return new DBJsonResponse([], 'Parametros atualizados com sucesso!');
    }
}
