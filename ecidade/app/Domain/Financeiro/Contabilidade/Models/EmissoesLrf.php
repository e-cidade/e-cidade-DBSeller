<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\RelarorioLegal\Model\Relatorio;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmissoesLrf
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c181_codigo
 * @property integer $c181_relatorio
 * @property integer $c181_periodo
 * @property integer $c181_usuario
 * @property integer $c181_instituicao
 * @property string $c181_status
  * @property boolean $c181_publicado
 * @property string $c181_storage
 * @property string $c181_filtrosemissao
 * @property Carbon $create_at
 * @property Carbon $updated_at
 */
class EmissoesLrf extends Model
{
    protected $table = 'contabilidade.emissoeslrf';
    protected $primaryKey = 'c181_codigo';

    public function relatorio()
    {
        return $this->belongsTo(Relatorio::class, 'c181_relatorio', 'o42_codparrel');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'c181_periodo', 'o114_sequencial');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'c181_usuario', 'id_usuario');
    }

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'c181_instituicao', 'codigo');
    }
}
