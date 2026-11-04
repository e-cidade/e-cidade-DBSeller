<?php

namespace App\Domain\Saude\TFD\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa o cancelamento da saida de um veiculo.
 * @package App\Domain\Saude\TFD\Models
 * @property int $tf41_codigo
 * @property string $tf41_usuario
 * @property DateTime $tf41_datahora
 * @property int $tf41_veiculodestino
 * @property string $tf41_motivocancelamento
 * @property VeiculoDestino $veiculoDestino
 */
class CancelamentoViagem extends Model
{
    protected $table = 'tfd.cancelamentoviagem';
    protected $primaryKey = 'tf41_codigo';
    public $timestamps = false;

    public function veiculoDestino()
    {
        return $this->belongsTo(VeiculoDestino::class, 'tf41_veiculodestino', 'tf18_i_codigo');
    }
}
