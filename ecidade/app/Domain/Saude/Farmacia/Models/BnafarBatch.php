<?php

namespace App\Domain\Saude\Farmacia\Models;

use App\Domain\Core\Models\BatchJob;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $fa75_id
 * @property string $fa75_batch
 * @property integer $fa75_tipo
 * @property boolean $fa75_concluido
 *
 * @property BatchJob $batch
 */
class BnafarBatch extends Model
{
    public $timestamps = false;
    protected $table = 'farmacia.bnafarbatch';
    protected $primaryKey = 'fa75_id';
    protected $fillable = [
        'fa75_batch',
        'fa75_tipo'
    ];

    public function batch()
    {
        return $this->belongsTo(BatchJob::class, 'fa75_batch', 'id');
    }
}
