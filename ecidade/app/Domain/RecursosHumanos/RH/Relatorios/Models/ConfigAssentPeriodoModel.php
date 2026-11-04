<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe responsavel pelas configuracoes do relatorio
 * de assetamento por periodo
 *
 * @property int  $rh512_sequencial
 * @property int  $rh512_instit
 * @property bool $rh512_filtroadicionais
 */
class ConfigAssentPeriodoModel extends Model
{
    protected $table = 'configassentperiodo';
    protected $primaryKey = 'rh512_sequencial';
    public $timestamps = false;

    public function getRh512AssentferiasAttribute($value)
    {
        return json_decode($value);
    }
}
