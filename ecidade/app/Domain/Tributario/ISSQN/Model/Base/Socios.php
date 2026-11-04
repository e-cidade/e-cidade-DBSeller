<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\Socios
 *
 * @property int $q95_cgmpri
 * @property int $q95_numcgm
 * @property float|null $q95_perc
 * @property int|null $q95_tipo
 * @property int|null $q95_qualificacaosocio
 * @property-read Cgm $cgm
 * @mixin \Eloquent
 */
class Socios extends Model
{
    protected $table = 'socios';
    public $timestamps = false;
    public $incrementing = false;

    public function cgm()
    {
        return $this->hasOne(Cgm::class, "z01_numcgm", "q95_numcgm");
    }
}
