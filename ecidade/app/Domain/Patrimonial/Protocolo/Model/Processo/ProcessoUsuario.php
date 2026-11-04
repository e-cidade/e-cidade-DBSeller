<?php

namespace App\Domain\Patrimonial\Protocolo\Model\Processo;

use Illuminate\Database\Eloquent\Model;

class ProcessoUsuario extends Model
{


    protected $primaryKey = "p119_codigo";

    public $timestamps = false;

    protected $fillable = [
        "p119_protprocesso",
        "p119_id_usuario",
        "p119_atividadeexecucao",
    ];
}
