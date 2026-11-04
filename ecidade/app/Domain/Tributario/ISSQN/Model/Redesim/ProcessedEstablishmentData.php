<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentData
 *
 * @property int $q193_sequencial
 * @property mixed $q193_data
 * @property int $q193_processed_establishment
 * @property \Carbon\Carbon|null $q193_created_at
 * @property \Carbon\Carbon|null $q193_updated_at
 * @method static Builder|ProcessedEstablishmentData whereProcessedEstablishment($value)
 * @mixin \Eloquent
 */
class ProcessedEstablishmentData extends Model
{
    const CREATED_AT = "q193_created_at";
    const UPDATED_AT = "q193_updated_at";

    protected $primaryKey = "q193_sequencial";
    protected $table = "processed_establishment_datas";

    public function scopeWhereProcessedEstablishment(Builder $builder, $value)
    {
        return $builder->where("q193_processed_establishment", $value);
    }
}
