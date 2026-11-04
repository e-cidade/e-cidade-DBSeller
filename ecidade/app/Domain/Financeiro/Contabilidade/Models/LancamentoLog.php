<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

class LancamentoLog extends Model
{
    protected $table = 'contabilidade.lancamentoscontabeislog';
    protected $primaryKey = 'sequencial';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
