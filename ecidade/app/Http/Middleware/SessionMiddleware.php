<?php

namespace App\Http\Middleware;

use Closure;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SessionMiddleware
 * @package App\Http\Middleware
 */
class SessionMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $this->makeSession($request);

        return $next($request);
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    private function makeSession($request)
    {
        $session = DefaultSession::getInstance()->add([DefaultSession::DB_REQUEST_FROM_API => true]);
        if ($request->header('X-Window-Session', '') !== '') {
            $this->makeSessionFromWindow($request->header('X-Window-Session'));
        } else {
            $session->start();
            $session->addFromRequest($request->all());
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new AccessDeniedException('Não foi possível construir a sessão de acesso.');
        }

        $request->merge(DefaultSession::getInstance()->all());

        $databaseSession = DatabaseSession::getInstance()->addSessionToDatabase();

        if (!$databaseSession) {
            throw new NotFoundHttpException('Não foi possível adicionar a sessão ao banco de dados.');
        }
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    private function makeSessionFromWindow($id)
    {
        $session = new \ECidade\V3\Window\Session($id);
        $session->create()->start();
        session($session->all());
        DefaultSession::getInstance()->addFromRequest($session->all());
    }
}
