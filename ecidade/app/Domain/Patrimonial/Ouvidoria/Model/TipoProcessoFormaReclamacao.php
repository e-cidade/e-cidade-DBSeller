<?php

namespace App\Domain\Patrimonial\Ouvidoria\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static tipoProcessoCodigo( int $tipoProcesso)
 */
class TipoProcessoFormaReclamacao extends Model
{

    protected $table = 'ouvidoria.tipoprocformareclamacao';
    protected $primaryKey = 'p43_sequencial';
    public $timestamps = false;

    public function formaReclamacao()
    {
        return $this->belongsTo(FormaReclamacao::class, "p43_formareclamacao");
    }

    public function scopeTipoProcessoCodigo($query, $tipoProcesso)
    {
        return $query->where("p43_tipoproc", $tipoProcesso);
    }
}
