<?php

namespace App\Domain\Configuracao\Usuario\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificacoesController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function get(Request $request)
    {
        $user = $request->user();
        $notications = [];
        foreach ($user->unreadNotifications as $notification) {
            $notications[] = (object)array_merge(['id' => $notification->id], $notification->data);
        }

        return new DBJsonResponse($notications);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->unreadNotifications()->where('id', $id)->first();
        if ($notification === null) {
            throw new \Exception('Notificação não existe ou não pertence ao usuário logado.', 404);
        }
        $notification->markAsRead();

        return new DBJsonResponse([], 'Notificação marcada como visto com sucesso.');
    }
}
