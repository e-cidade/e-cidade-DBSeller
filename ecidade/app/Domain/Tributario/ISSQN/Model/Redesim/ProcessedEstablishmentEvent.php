<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentEvent
 *
 * @property int $q192_sequencial
 * @property int $q192_redesim_event
 * @property int $q192_processed_establishment
 * @property \Carbon\Carbon|null $q192_created_at
 * @property \Carbon\Carbon|null $q192_updated_at
 * @method static Builder|ProcessedEstablishmentEvent whereProcessedEstablishment($value)
 * @property-read RedesimEvent $redesimEvent
 * @mixin \Eloquent
 */
class ProcessedEstablishmentEvent extends Model
{
    const CREATED_AT = "q192_created_at";
    const UPDATED_AT = "q192_updated_at";

    protected $primaryKey = "q192_sequencial";

    public function redesimEvent()
    {
        return $this->belongsTo(RedesimEvent::class, "q192_redesim_event", "q191_sequencial");
    }

    public function scopeWhereProcessedEstablishment(Builder $builder, $value)
    {
        return $builder->where("q192_processed_establishment", $value);
    }
}
