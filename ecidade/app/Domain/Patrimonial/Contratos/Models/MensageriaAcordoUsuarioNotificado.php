<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

class MensageriaAcordoUsuarioNotificado extends Model
{

    protected $table = 'mensageria_acordo_usuario_notificados';

    protected $fillable = [
        "mensageria_acordo_dias",
        "usuario_id",
        "acordo_id"
    ];
}
