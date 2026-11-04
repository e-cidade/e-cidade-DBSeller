<?php
namespace App\Http\Middleware;

use App\Domain\Configuracao\Configuracao\Repositories\MenuPermissionRepository;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Closure;
use Illuminate\Http\Request;

class PermissoesMenuMiddleware
{
    protected $menuPermissionRepository;

    public function __construct(MenuPermissionRepository $menuPermissionRepository)
    {
        $this->menuPermissionRepository = $menuPermissionRepository;
    }

    /**
     * Lida com a requisição e verifica se o usuário ou perfil tem permissão para acessar os itens do menu.
     * Exemplo: ->middleware('permissaoMenu:123,124,125');
     * @param Request $request O objeto de requisição HTTP.
     * @param Closure $next O próximo middleware na fila.
     * @param ...$idItemMenus Os IDs dos itens do menu para verificar a permissão.
     * @return mixed A resposta do próximo middleware ou um DBJsonResponse se o usuário não tiver permissão.
     */
    public function handle(Request $request, Closure $next, ...$idItemMenus)
    {
        $idItemMenusArray = [];

        if ($request->get('id_item_menu')) {
            $id = (integer) $request->get('id_item_menu');
            $idItemMenusArray[] = $id;
        } else {
            $idItemMenusArray = $idItemMenus;
        }

        $idUser = $request->user()->id_usuario;

        if ($this->menuPermissionRepository->verificaAdmin($idUser)) {
            return $next($request);
        }

        foreach ($idItemMenusArray as $idItemMenu) {
            $id = (integer) $idItemMenu;

            $results = $this->menuPermissionRepository->userHasPermission($idUser, $id);

            if (!$results->isEmpty()) {
                return $next($request);
            }
        }

        return new DBJsonResponse(
            [],
            "Não possui permissão para esta ação, contate o administrador",
            403
        );
    }
}
