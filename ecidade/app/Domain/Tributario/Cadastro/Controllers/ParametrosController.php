<?php

namespace App\Domain\Tributario\Cadastro\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Tributario\Cadastro\Models\ParametrosNumeroCadastral;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;

class ParametrosController extends Controller
{
    public function listar()
    {
       
        $message = "Nenhum registro encontrado";
        $retorno = [
                    "separador" => '',
                    "configuracao" => []
                 ];

        $parametrosNumeroCadastral = ParametrosNumeroCadastral::all();
        if ($parametrosNumeroCadastral->count() > 0) {
            $message = "Registro encontrado";
            $retorno = [
            "separador"    => $parametrosNumeroCadastral->pluck('j180_separadormascara')->pull(0),
            "configuracao" => json_decode(utf8_encode($parametrosNumeroCadastral->pluck('j180_configuracao')->pull(0)))
            ];
        }
       
        return new DBJsonResponse($retorno, $message);
    }

    public function salvar(Request $request)
    {
        try {
            $parametros = new ParametrosNumeroCadastral();
            $parametros->j180_instit           = $request->DB_instit;
            $parametros->j180_separadormascara = $request->separador == ""?null:$request->separador;
            $parametros->j180_configuracao     = stripslashes($request->configuracao);
            $parametros->destroy($request->DB_instit);
            $status    = 200;
            $mensagem  = 'Configuração excluída';
            if (count(json_decode(stripslashes($request->configuracao))) > 0) {
                $parametros->save();
                $mensagem = 'Configuração salva';
            }
        } catch (\Exception $erro) {
            $mensagem = "Não foi possível salvar";
            $status   = 404;
            return new DBJsonResponse(null, $mensagem, $status);
        }
       
        return new DBJsonResponse(null, $mensagem, $status);
    }
}
