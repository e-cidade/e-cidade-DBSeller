<?php

namespace App\Domain\Saude\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $fa71_id
 * @property integer $fa71_bnafarenvio
 * @property string $fa71_content
 *
 * @property BnafarConferencia|null $conferencia
 */
class BnafarInconsistencia extends Model
{
    public $timestamps = false;
    protected $table = 'farmacia.bnafarinconsistencias';
    protected $primaryKey = 'fa71_id';

    public function conferencia()
    {
        return $this->hasOne(BnafarConferencia::class, 'fa72_bnafarinconsistencia', 'fa71_id');
    }
}
