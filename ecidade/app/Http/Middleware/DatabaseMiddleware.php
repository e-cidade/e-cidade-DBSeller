<?php

namespace App\Http\Middleware;

use App\Domain\Configuracao\Usuario\Requests\LoginRequest;
use Closure;
use ECidade\V3\Datasource\Database;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class DatabaseMiddleware
 * @package App\Http\Middleware
 */
class DatabaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $this->prepareConnection($request);
        $database = Database::getInstance(true, null, true);

        global $conn;
        $conn = $database->getConnection();

        $applicationName = 'ecidade_api_' . str_replace('/', '_', $request->path());

        Config::set('database.connections.pgsql.host', $database->getServidor());
        Config::set('database.connections.pgsql.port', $database->getPorta());
        Config::set('database.connections.pgsql.database', $database->getBase());
        Config::set('database.connections.pgsql.username', $database->getUsuario());
        Config::set('database.connections.pgsql.password', $database->getSenha());
        Config::set('database.connections.pgsql.application_name', 'pdo_' . $applicationName);

        DB::purge('pgsql');
        DB::beginTransaction();

        pg_query("SET application_name='pg_{$applicationName}'");
        $database->begin();

        $response = $next($request);

        if ($response instanceof BaseResponse && ($response->isSuccessful() || $response->isRedirection())) {
            // DB::rollBack();
            // $database->rollBack();
            DB::commit();
            $database->commit();
        } else {
            DB::rollBack();
            $database->rollBack();
        }

        return $response;
    }

    /**
     * Prepara a conexão com o banco de dados
     * @param Request $request
     * @return void
     */
    private function prepareConnection(Request $request)
    {
        /**
         * Não seta os cookies quando não for uma requisição de login pois, nesses momentos,
         * os cookie já devem estar setados.
         */
        if ($request->path() !== 'v4/login') {
            return;
        }

        $cookieOptions = ['expires' => 0, 'path' => '/', 'samesite' => 'Lax'];
        if (!app()->isLocal() || empty($request->DB_HOST) || empty($request->DB_DATABASE)) {
            setcookie("DB_base", null, $cookieOptions);
            setcookie("DB_servidor", null, $cookieOptions);
            setcookie("DB_porta", null, $cookieOptions);
            setcookie("DB_usuario", null, $cookieOptions);
            setcookie("DB_senha", null, $cookieOptions);

            unset(
                $_COOKIE['DB_base'],
                $_COOKIE['DB_servidor'],
                $_COOKIE['DB_porta'],
                $_COOKIE['DB_usuario'],
                $_COOKIE['DB_senha']
            );

            return;
        }

        // Seta os valores no cookie para usar em outras requisições
        setcookie("DB_base", $request->DB_DATABASE, $cookieOptions);
        setcookie("DB_servidor", $request->DB_HOST, $cookieOptions);
        setcookie("DB_porta", $request->DB_PORT, $cookieOptions);
        setcookie("DB_usuario", $request->DB_USERNAME, $cookieOptions);
        setcookie("DB_senha", $request->DB_PASSWORD, $cookieOptions);

        // Seta os valores no cookie para usar nessa requisição
        $_COOKIE['DB_base'] = $request->DB_DATABASE;
        $_COOKIE['DB_servidor'] = $request->DB_HOST;
        $_COOKIE['DB_porta'] = $request->DB_PORT;
        $_COOKIE['DB_usuario'] = $request->DB_USERNAME;
        $_COOKIE['DB_senha'] = $request->DB_PASSWORD;
    }
}
