<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRelatorio extends Model
{
    public $timestamps = false;
    protected $table = 'configuracoes.db_tiporelatorio';
    protected $primaryKey = 'db14_sequencial';
}
