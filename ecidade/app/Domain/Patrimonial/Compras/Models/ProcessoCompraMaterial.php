<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc01_codmater,
 * @property $pc01_descrmater,
 * @property $pc01_complmater,
 * @property $pc01_codsubgrupo,
 * @property $pc01_ativo,
 * @property $pc01_conversao,
 * @property $pc01_id_usuario,
 * @property $pc01_libaut,
 * @property $pc01_servico,
 * @property $pc01_veiculo,
 * @property $pc01_validademinima,
 * @property $pc01_obrigatorio,
 * @property $pc01_fraciona,
 * @property $pc01_liberaresumo
 */
class ProcessoCompraMaterial extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcmater';
    protected $primaryKey = 'pc01_codmater';
}
