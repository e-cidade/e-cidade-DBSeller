<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

/**
 * Model da tabela contabilidade.sigfisunidadegestora
 *
 * @property int $c179_codigo
 * @property int $c179_sequencial;
 * @property int $c179_instit;
 * @property boolean $c179_responsavelfolha
 * @property int $c179_codigofolha
 * @property int $c179_cgmordenadordespesa
 *
 */
class SigfisUnidadeGestoraModel extends Model
{
    protected $table = 'contabilidade.sigfisunidadegestora';
    protected $primaryKey = 'c179_sequencial';
    public $timestamps = false;

    public function ordenadorDespesa()
    {
        return $this->hasOne(Cgm::class, 'z01_numcgm', 'c179_cgmordenadordespesa');
    }
}
