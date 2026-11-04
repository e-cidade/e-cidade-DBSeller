<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoRelatorio extends Model
{
    public $timestamps = false;
    protected $table = 'configuracoes.db_gruporelatorio';
    protected $primaryKey = 'db13_sequencial';
}
