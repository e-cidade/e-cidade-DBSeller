<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $db15_sequencial
 * @property int $db15_db_relatorio
 * @property int $db15_documento
 * @property string $db15_extensao_arquivo
 * @property int $db15_estorage
 */
class RelatorioTemplate extends Model
{
    public $timestamps = false;
    protected $table = 'configuracoes.db_geradorrelatoriotemplate';
    protected $primaryKey = 'db15_sequencial';
}
