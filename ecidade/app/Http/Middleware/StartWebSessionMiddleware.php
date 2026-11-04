<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 * Class StartWebSessionMiddleware
 * @package App\Http\Middleware
 */
class StartWebSessionMiddleware
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
        session_start();
        $this->logAcesso(substr($request->getPathInfo(), 1));
        session($_SESSION);

        return $next($request);
    }

    /**
     * @param string $pathMenu
     * @return void
     */
    private function logAcesso($pathMenu)
    {
        $item = $this->getItemMenu($pathMenu);
        if ($item !== null) {
            $this->insertLog($item->id, $pathMenu);
        }
    }

    /**
     * Retorna a chave primária do item de menu acessado, conforme a rota da requisição
     *
     * @param string $pathMenu
     * @return object
     */
    private function getItemMenu($pathMenu)
    {
        return DB::table('db_itensmenu')
            ->select('db_itensmenu.id_item as id')
            ->join('db_menu', 'db_menu.id_item_filho', 'db_itensmenu.id_item')
            ->where(function (Builder $query) use ($pathMenu) {
                $query->whereRaw("trim(funcao) = '{$pathMenu}'");

                if (isset($_SESSION["DB_modulo"])) {
                    $query->where('modulo', $_SESSION['DB_modulo']);
                }
            })
            ->first();
    }

    /**
     * Loga o acesso no banco e salva o item na sessão
     *
     * @param string $item
     * @param string $pathMenu
     * @throws \Exception
     */
    private function insertLog($item, $pathMenu)
    {
        $codsequen = DB::select("SELECT nextval('db_logsacessa_codsequen_seq')")[0]->nextval;

        $_SESSION['DB_itemmenu_acessado'] = $item;
        $_SESSION['DB_acessado'] = $codsequen;

        $rs = DB::table('db_logsacessa')->insert([
            'codsequen' => $codsequen,
            'ip' => $_SESSION['DB_ip'],
            'data' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'arquivo' => $pathMenu,
            'obs' => '',
            'id_usuario' => $_SESSION['DB_id_usuario'],
            'id_modulo' => $_SESSION['DB_modulo'],
            'id_item' => $item,
            'coddepto' => $_SESSION['DB_coddepto'],
            'instit' => $_SESSION['DB_instit']
        ]);

        if (!$rs) {
            throw new \Exception('Houve um erro ao iniciar a auditoria do sistema.');
        }
    }
}
