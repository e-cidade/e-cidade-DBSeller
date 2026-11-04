<?php

namespace App\Domain\Saude\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $fa76_id
 * @property int $fa76_bnafarbatch
 * @property int $fa76_protocolo
 * @property bool $fa76_falhou
 * @property \DateTime $fa76_created_at
 *
 * @property BnafarBatch $bnafarBatch
 */
class BnafarBatchProtocolo extends Model
{
    public $timestamps = false;
    protected $table = 'farmacia.bnafarbatch_protocolo';
    protected $primaryKey = 'fa76_id';

    public $dates = [ 'fa76_created_at', ];
    public $casts = [ 'fa76_created_at' => 'datetime',];

    public function bnafarBatch()
    {
        return $this->belongsTo(BnafarBatch::class, 'fa76_bnafarbatch', 'fa75_id');
    }
}
