<?php

namespace App\Domain\Financeiro\Orcamento\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\RelarorioLegal\Model\Relatorio;
use App\Domain\Financeiro\Contabilidade\Models\Periodo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $o42_sequencial
 * @property integer $o42_codparrel
 * @property integer $o42_anousu
 * @property integer $o42_instit
 * @property string $o42_nota
 * @property string $o42_fonte
 * @property integer $o42_periodo
 * @property numeric $o42_tamanhofontenota
 * @property numeric $o42_tamanhofontedados
 */
class LrfNotaExplicativa extends Model
{
    protected $table = 'orcamento.orcparamrelnota';
    protected $primaryKey = 'o42_sequencial';
    public $timestamps = false;

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'o42_instit', 'codigo');
    }

    public function relatorio()
    {
        return $this->belongsTo(Relatorio::class, 'o42_codparrel', 'o42_codparrel');
    }

    public function scopePeriodo(Builder $query)
    {
        $query->join('configuracoes.periodo', DB::raw('o42_periodo::text'), '=', DB::raw('o114_sequencial::text'));
    }
}
