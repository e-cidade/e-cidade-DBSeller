<?php

namespace App\Domain\Patrimonial\Patrimonio\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $t52_bem
 * @property integer $t52_codcla
 * @property integer $t52_numcgm
 * @property integer $t52_valaqu
 * @property \DateTime $t52_dtaqu
 * @property string $t52_ident
 * @property string $t52_descr
 * @property string $t52_obs
 * @property integer $t52_depart
 * @property integer $t52_instit
 * @property integer $t52_bensmarca
 * @property intger $t52_bensmodelo
 * @property integer $t52_bensmedida
 * @property $t52_foto
 * @property \DateTime $t52_dtinclusao
 *
 * @property BemDivisao|null $divisao
 * @property BemPlaca $placa
 * @property DBConfig $instituicao
 */
class Bem extends Model
{
    protected $table = "patrimonio.bens";
    protected $primaryKey = 't52_bem';
    public $timestamps = false;
    public $casts = [
        't52_dtaqu'  => 'datetime',
        't52_dtinlcusao' => 'datetime'
    ];

    public function divisao()
    {
        return $this->hasOne(BemDivisao::class, 't33_bem', 't52_bem');
    }

    public function placa()
    {
        return $this->hasOne(BemPlaca::class, 't41_bem', 't52_bem');
    }

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 't52_instit', 'codigo');
    }
}
