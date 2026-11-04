<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $db09_sequencial
 * @property integer $db09_db_relatorio
 * @property integer $db09_db_usuarios
 */
class RelatorioUsuario extends Model
{
    protected $table = 'configuracoes.db_relatoriousuario';
    protected $primaryKey = 'db09_sequencial';
    public $timestamps = false;
}
