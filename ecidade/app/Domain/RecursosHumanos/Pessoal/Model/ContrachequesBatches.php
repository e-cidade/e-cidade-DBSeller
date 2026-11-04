<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use App\Domain\Core\Models\BatchJob;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $rh269_codigo
 * @property integer $rh269_instit
 * @property integer $rh269_batch
 * @property string $rh269_competencia
 * @property integer $rh269_total
 * @property BatchJob $batch
 */
class ContrachequesBatches extends Model
{
    protected $primaryKey = 'rh269_codigo';
    public $timestamps = false;

    public function batch()
    {
        return $this->belongsTo(BatchJob::class, 'rh269_batch', 'id');
    }
}
