<?php

namespace App\Domain\Configuracao\Usuario\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use Illuminate\Support\Facades\DB;

class UsuarioService
{
    public function getUltimoAcesso(Usuario $usuario)
    {
        $ultimoAcesso = DB::table('db_logsacessa')
            ->select(['data', 'hora'])
            ->where('id_usuario', $usuario->id_usuario)
            ->orderByRaw('data DESC, hora DESC')
            ->limit(1)
            ->first();

        if ($ultimoAcesso === null) {
            return null;
        }

        return new \DateTime("{$ultimoAcesso->data} {$ultimoAcesso->hora}");
    }
}
