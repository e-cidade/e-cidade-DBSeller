<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $db07_sequencial
 * @property integer $db07_db_relatorio
 * @property integer $db07_db_depart
 */
class RelatorioDepartamento extends Model
{
    protected $table = 'configuracoes.db_relatoriodepart';
    protected $primaryKey = 'db07_db_relatorio';
    public $timestamps = false;
}
