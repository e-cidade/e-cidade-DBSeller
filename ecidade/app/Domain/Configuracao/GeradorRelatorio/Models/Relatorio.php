<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $db63_sequencial
 * @property int $db63_db_gruporelatorio
 * @property int $db63_db_tiporelatorio
 * @property string $db63_nomerelatorio
 * @property string $db63_versao_xml
 * @property string $db63_data
 * @property string $db63_xmlestruturarel
 * @property int $db63_db_relatorioorigem
 *
 * @property GrupoRelatorio $grupo
 * @property TipoRelatorio $tipo
 * @property RelatorioUsuario $relatorioUsuario
 * @property RelatorioDepartamento $relatorioDepartamento
 * @property RelatorioTemplate $template
 */
class Relatorio extends Model
{
    protected $table = 'configuracoes.db_relatorio';
    protected $primaryKey = 'db63_sequencial';
    public $timestamps = false;

    public function grupo()
    {
        return $this->belongsTo(GrupoRelatorio::class, 'db63_db_gruporelatorio', 'db13_sequencial');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoRelatorio::class, 'db63_db_tiporelatorio', 'db14_sequencial');
    }

    public function relatorioUsuario()
    {
        return $this->hasOne(RelatorioUsuario::class, 'db09_db_relatorio', 'db63_sequencial');
    }

    public function relatorioDepartamento()
    {
        return $this->hasOne(RelatorioDepartamento::class, 'db07_db_relatorio', 'db63_sequencial');
    }

    public function template()
    {
        return $this->hasOne(RelatorioTemplate::class, 'db15_db_relatorio', 'db63_sequencial');
    }
}
