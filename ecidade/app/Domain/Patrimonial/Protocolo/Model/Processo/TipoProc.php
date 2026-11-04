<?php

namespace App\Domain\Patrimonial\Protocolo\Model\Processo;

use App\Domain\Patrimonial\Ouvidoria\Model\TipoprocPersona;
use App\Domain\Patrimonial\Protocolo\Model\Persona;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cgm
 * @package App\Domain\Patrimonial\Protocolo
 * @method likeDescricao(string $descricao)
 * @method likeId(string $id)
 */

class TipoProc extends Model
{
    protected $table = 'protocolo.tipoproc';
    protected $primaryKey = 'p51_codigo';
    public $timestamps = false;

    public function personas()
    {
        return $this->belongsToMany(
            Persona::class,
            "ouvidoria.tipoprocpersona",
            "ov34_tipoproc",
            "ov34_persona"
        )->withPivot('ov34_sequencial');
    }


    public function scopeInstituicoes($query, $instituicoes = [])
    {
        return $query->whereIn('p51_instit', $instituicoes);
    }

    public function scopeLikeId($query, $id = '')
    {
        return $query->where("p51_codigo", "=", $id);
    }

    public function scopeLikeDescricao($query, $descricao = '')
    {
        return $query->where("p51_descr", "ilike", "%{$descricao}%");
    }
}
