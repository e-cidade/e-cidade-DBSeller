<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn01_codigo
 * @property $pn01_habilitado
 * @property $pn01_data
 * @property $pn01_instit
 * @property $pn01_usuario
 */
class IntegracaoPNCP extends Model
{
    protected $primaryKey = 'pn01_codigo';
    protected $table = 'integracaopncp';
    protected $fillable = [
      'pn01_habilitado',
      'pn01_data',
      'pn01_instit',
      'pn01_usuario'
    ];
    public $timestamps = false;
}
