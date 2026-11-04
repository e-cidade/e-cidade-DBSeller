<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\AutomaticEstablishmentProcess
 *
 * @mixin \Eloquent
 * @property int $q194_sequencial
 * @property int $q194_processed_establishment
 * @property bool $q194_processed
 * @property bool $q194_error
 * @property \Carbon\Carbon|null $q194_created_at
 * @property \Carbon\Carbon|null $q194_updated_at
 * @method static Builder|AutomaticEstablishmentProcess whereProcessed($value)
 * @method static Builder|AutomaticEstablishmentProcess whereError($value)
 */
class AutomaticEstablishmentProcess extends Model
{
    const CREATED_AT = "q194_created_at";
    const UPDATED_AT = "q194_updated_at";

    protected $table = "automatic_establishment_process";
    protected $primaryKey = "q194_sequencial";

    public function scopeWhereProcessed(Builder $builder, $value)
    {
        return $builder->where("q194_processed", $value);
    }

    public function scopeWhereError(Builder $builder, $value)
    {
        return $builder->where("q194_error", $value);
    }
}
