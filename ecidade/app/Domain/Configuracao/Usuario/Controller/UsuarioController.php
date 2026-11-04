<?php

namespace App\Domain\Configuracao\Usuario\Controller;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Configuracao\Usuario\Resources\UsuarioResource;
use App\Domain\Configuracao\Usuario\Services\UsuarioService;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function get(Request $request)
    {
        $user = $request->user();

        return new DBJsonResponse(UsuarioResource::toResponse($user));
    }

    public function search(Request $request)
    {
        $perpage = 10;

        if (!empty($request->get("perpage")) and is_numeric($request->get("perpage"))) {
            $perpage = $request->get("perpage");
        }

        $usuario = new Usuario();

        if (!empty($request->get("nome"))) {
            $usuario = $usuario->likeNome($request->get("nome"));
        }

        if (!empty($request->get("email"))) {
            $usuario = $usuario->likeEmail($request->get("email"));
        }

        if (!empty($request->get("login"))) {
            $usuario = $usuario->likeLogin($request->get("login"));
        }

        $usuario = $usuario->isAtivo();

        if ($request->get("instituicao_sessao") === 'true') {
            if (empty(db_getsession("DB_instit"))) {
                throw new \Exception("Instituição da sessão não encontrada!");
            }
            $usuario = $usuario->join("db_userinst", "db_userinst.id_usuario", "db_usuarios.id_usuario");
            $usuario = $usuario->where("db_userinst.id_instit", db_getsession("DB_instit"));
        }


        $labelField = "CONCAT(
                    'NOME: '
                    ,db_usuarios.nome
                    ,' - CÓDIGO: ['
                    ,db_usuarios.id_usuario
                    ,'] '
                    ,' LOGIN : ('
                    , db_usuarios.login,
                    ')'
                 )";

        $usuario = $usuario->select([
            "db_usuarios.*",
            DB::raw(
                $labelField . " as usuario_label"
            )
        ]);


        if ($request->get("label")) {
            $usuario = $usuario->groupBy("db_usuarios.id_usuario");
            $usuario = $usuario->having(DB::raw(
                $labelField
            ), "ilike", "%{$request->get("label")}%");
        }
        $usuario = $usuario->distinct();
        return new DBJsonResponse($usuario->paginate($perpage));
    }
}
