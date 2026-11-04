<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\EstablishmentDataBatch
 *
 * @property int $q189_sequencial
 * @property boolean $q189_processed
 * @property boolean $q189_error
 * @property string $q189_data
 * @property \Illuminate\Support\Carbon|null $q189_created_at
 * @property \Illuminate\Support\Carbon|null $q189_updated_at
 * @method static Builder|EstablishmentDataBatch newModelQuery()
 * @method static Builder|EstablishmentDataBatch newQuery()
 * @method static Builder|EstablishmentDataBatch query()
 * @method static Builder|EstablishmentDataBatch whereId($value)
 * @method static Builder|EstablishmentDataBatch whereProcessed($value)
 * @method static Builder|EstablishmentDataBatch whereError($value)
 * @method static Builder|EstablishmentDataBatch whereCreatedAt($value)
 * @method static Builder|EstablishmentDataBatch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EstablishmentDataBatch extends Model
{
    const CREATED_AT = "q189_created_at";
    const UPDATED_AT = "q189_updated_at";

    protected $primaryKey = "q189_sequencial";

    public function scopeWhereProcessed(Builder $builder, $value)
    {
        return $builder->where("q189_processed", $value);
    }

    public function scopeWhereError(Builder $builder, $value)
    {
        return $builder->where("q189_error", $value);
    }
}
