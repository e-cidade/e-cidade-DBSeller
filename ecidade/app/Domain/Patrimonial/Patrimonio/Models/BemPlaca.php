<?php

namespace App\Domain\Patrimonial\Patrimonio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property intger $t41_codigo
 * @property integer $t41_bem
 * @property string $t41_placa
 * @property integer $t41_placaseq
 * @property string $t41_obs
 * @property \DateTime $t41_data
 * @property integer $t41_usuario
 * @property boolean $t41_excluido
 */
class BemPlaca extends Model
{
    protected $table = "patrimonio.bensplaca";
    protected $primaryKey = 't41_codigo';
    public $timestamps = false;

    public $casts = ['t41_data' => 'datetime'];
}
