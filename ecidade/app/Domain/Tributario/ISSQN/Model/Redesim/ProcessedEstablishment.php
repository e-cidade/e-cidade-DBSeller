<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\EstablishmentDataBatch
 *
 * @property int $q190_sequencial
 * @property boolean $q190_external_id
 * @property string $q190_establishment_data_batch
 * @property int $q190_process_id
 * @property bool $q190_processed
 * @property bool $q190_replied
 * @property \Illuminate\Support\Carbon|null $q190_created_at
 * @property \Illuminate\Support\Carbon|null $q190_updated_at
 * @method static Builder|ProcessedEstablishment newModelQuery()
 * @method static Builder|ProcessedEstablishment newQuery()
 * @method static Builder|ProcessedEstablishment query()
 * @method static Builder|ProcessedEstablishment whereSequencial($value)
 * @method static Builder|ProcessedEstablishment whereExternalId($value)
 * @method static Builder|ProcessedEstablishment whereProcessId($value)
 * @property-read Processo $process
 * @mixin \Eloquent
 */
class ProcessedEstablishment extends Model
{
    const CREATED_AT = "q190_created_at";
    const UPDATED_AT = "q190_updated_at";

    protected $primaryKey = "q190_sequencial";

    public function process()
    {
        return $this->hasOne(Processo::class, "p58_codproc", "q190_process_id");
    }

    public function scopeWhereSequencial(Builder $builder, $value)
    {
        return $builder->where("q190_sequencial", $value);
    }

    public function scopeWhereExternalId(Builder $builder, $value)
    {
        return $builder->where("q190_external_id", $value);
    }

    public function scopeWhereProcessId(Builder $builder, $value)
    {
        return $builder->where("q190_process_id", $value);
    }
}
