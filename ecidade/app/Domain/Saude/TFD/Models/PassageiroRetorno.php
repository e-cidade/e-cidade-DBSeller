<?php

namespace App\Domain\Saude\TFD\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa o cancelamento da saida de um veiculo.
 * @package App\Domain\Saude\TFD\Models
 * @property int $tf31_i_codigo
 * @property string $tf31_i_veiculodestino
 * @property DateTime $tf31_i_passageiroveiculo
 * @property int $tf31_i_valido
 * @property VeiculoDestino $veiculoDestino
 * @property Passageiro $passageiroVeiculo
 */
class PassageiroRetorno extends Model
{
    protected $table = 'tfd.tfd_passageiroretorno';
    protected $primaryKey = 'tf31_i_codigo';
    public $timestamps = false;

    public function veiculoDestino()
    {
        return $this->belongsTo(VeiculoDestino::class, 'tf31_i_veiculodestino', 'tf18_i_codigo');
    }

    public function passageiroVeiculo()
    {
        return $this->belongsTo(Passageiro::class, 'tf31_i_passageiroveiculo', 'tf19_i_codigo');
    }
}
