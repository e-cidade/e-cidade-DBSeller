<?php

namespace App\Domain\Saude\Farmacia\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $fa70_id
 * @property integer $fa70_matestoqueini
 * @property DateTime $fa70_data
 * @property string $fa70_uri
 * @property string $fa70_method
 * @property string $fa70_body
 * @property integer|null $fa70_codigobnafar
 * @property integer|null $fa70_protocolo
 *
 * @property BnafarInconsistencia[] $inconsistencias
 */
class BnafarEnvio extends Model
{
    public $timestamps = false;
    protected $table = 'farmacia.bnafarenvios';
    protected $primaryKey = 'fa70_id';

    public $casts = [
        'fa70_data' => 'datetime'
    ];

    public function inconsistencias()
    {
        return $this->hasMany(BnafarInconsistencia::class, 'fa71_bnafarenvio', 'fa70_id');
    }
}
