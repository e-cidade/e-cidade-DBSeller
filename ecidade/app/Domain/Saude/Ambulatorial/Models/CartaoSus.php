<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer $s115_i_codigo
 * @property integer $s115_i_cgs
 * @property string $s115_c_cartaosus
 * @property string $s115_c_tipo
 * @property integer $s115_i_entrada
 *
 * @property Cgs $paciente
 */
class CartaoSus extends Model
{
    protected $table = 'ambulatorial.cgs_cartaosus';
    protected $primaryKey = 's115_i_codigo';
    public $timestamps = false;
    const TIPO_CARTAO_SUS = 'D';
    const ENTRADA_CARTAO_SUS = 1;

    public function paciente()
    {
        return $this->belongsTo(Cgs::class, 's115_i_cgs', 'z01_i_numcgs');
    }
}
