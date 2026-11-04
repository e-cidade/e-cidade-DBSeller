<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $l50_codigo
 * @property int $l50_id_externo
 * @property int $l50_chave
 * @property int $l50_instituicao
 */
class LicitaconUsuario extends Model
{
    public $timestamps = false;
    protected $fillable = ['l50_id_externo', 'l50_chave', 'l50_instituicao'];
    protected $table = 'licitacao.licitaconusuarios';
    protected $primaryKey = 'l50_codigo';

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'l50_instituicao');
    }
}
