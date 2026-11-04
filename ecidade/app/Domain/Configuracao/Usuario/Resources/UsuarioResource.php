<?php

namespace App\Domain\Configuracao\Usuario\Resources;

use App\Domain\Configuracao\Usuario\Models\Usuario;

class UsuarioResource
{
    public static function toResponse(Usuario $usuario)
    {
        return (object)array(
            'id' => $usuario->id_usuario,
            'login' => $usuario->login,
            'nome' => $usuario->nome
        );
    }
}
