<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/**
 * @todo renomear o nome do canal para algo melhor
 */
Broadcast::channel('App.Domain.Configuracao.Usuario.Models.Usuario.{id}', function ($user, $id) {
    return (int) $user->id_usuario === (int) $id;
});
