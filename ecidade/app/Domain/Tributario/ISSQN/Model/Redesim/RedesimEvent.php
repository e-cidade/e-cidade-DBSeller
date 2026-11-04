<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\RedesimEvent
 *
 * @property int $q191_sequencial
 * @property string $q191_external_id
 * @property string $q191_description
 * @property string $q191_event_type
 * @property \Illuminate\Support\Carbon|null $q191_created_at
 * @property \Illuminate\Support\Carbon|null $q191_updated_at
 * @method static Builder|RedesimEvent newModelQuery()
 * @method static Builder|RedesimEvent newQuery()
 * @method static Builder|RedesimEvent query()
 * @method static Builder|RedesimEvent whereExternalId($value)
 * @mixin \Eloquent
 */
class RedesimEvent extends Model
{
    const UPDATE = "UPDATE";
    const CREATE = "CREATE";
    const LOW = "LOW";

    const CREATED_AT = "q191_created_at";
    const UPDATED_AT = "q191_updated_at";

    protected $primaryKey = "q191_sequencial";

    /**
     * @throws \Exception
     */
    public function getEventTypeAttribute($event_type)
    {
        switch ($event_type) {
            case RedesimEvent::UPDATE:
                return RedesimEvent::UPDATE;
            case RedesimEvent::CREATE:
                return RedesimEvent::CREATE;
            case RedesimEvent::LOW:
                return RedesimEvent::LOW;
            default:
                throw new \Exception("Tipo de evento fora do padrão esperado.");
        }
    }

    public function scopeWhereExternalId(Builder $builder, $value)
    {
        return $builder->where("q191_external_id", $value);
    }
}
