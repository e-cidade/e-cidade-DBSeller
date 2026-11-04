<?php

namespace ECidade\Api\V1\Middleware\Session;

use ECidade\Api\V1\Domain\Route\RouteConfig;
use ECidade\Api\V1\Middleware\RequestMiddlewareInterface;
use ECidade\Api\V1\Models\Session\MenuAcessadoSession;
use ECidade\Lib\Session\DefaultSession;
use ECidade\V3\Datasource\Database;
use Exception;
use Illuminate\Support\Str;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MenuAcessadoSessionMiddleware implements RequestMiddlewareInterface
{
    /**
     * @param Request $request
     * @param Application $application
     * @throws Exception
     */
    public static function handle(Request $request, Application $application)
    {
        self::setMenuAcessado($request);
    }

    /**
     * @param MenuAcessadoSession $menuAcessado
     * @throws Exception
     */
    private static function addSessionMenu($menuAcessado)
    {
        $menu = $menuAcessado->getMenu();
        $modulo = $menuAcessado->getModulo();

        $sessaoMenu = array(
            DefaultSession::DB_MODULO => $modulo->getId(),
            DefaultSession::DB_NOME_MODULO => $modulo->getNome(),
            DefaultSession::DB_ACESSADO => $menu->getId()
        );

        DefaultSession::getInstance()->add($sessaoMenu);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public static function setMenuAcessado(Request $request)
    {
        if (!Database::getInstance()->isConnected()) {
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $script = $request->get('_route') ?: Str::replaceFirst('/v4/api', '', $request->getPathInfo());
        $route = RouteConfig::normalizeRequestRoute($script);
        $routes = RouteConfig::routesMenu();

        if (array_key_exists($route, $routes)) {
            $menuAcessado = $routes[$route];
            self::addSessionMenu($menuAcessado);
        }
    }
}
