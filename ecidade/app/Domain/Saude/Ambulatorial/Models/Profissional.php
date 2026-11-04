<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;

/**
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer sd03_i_codigo
 * @property integer sd03_i_crm
 * @property integer sd03_i_numerodias
 * @property \DateTime sd03_d_folgaini
 * @property \DateTime sd03_d_folgafim
 * @property integer sd03_i_cgm
 * @property integer sd03_i_tipo
 *
 * @property Cgm $cgm
 */
class Profissional extends Model
{
    protected $table = 'ambulatorial.medicos';
    protected $primaryKey = 'sd03_i_codigo';
    public $timestamps = false;

    public $casts = [
        'sd03_d_folgaini' => 'datetime',
        'sd03_d_folgafim' => 'datetime',
    ];

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'sd03_i_cgm', 'z01_numcgm');
    }
}
