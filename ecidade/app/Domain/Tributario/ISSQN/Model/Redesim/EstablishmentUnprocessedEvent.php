<?php

namespace App\Domain\Tributario\ISSQN\Model\Redesim;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Redesim\EstablishmentUnprocessedEvent
 *
 * @property int $q195_sequencial
 * @property int $q195_processed_establishment
 * @property int $q195_redesim_event
 * @property \Carbon\Carbon|null $q195_created_at
 * @property \Carbon\Carbon|null $q195_updated_at
 * @mixin \Eloquent
 */
class EstablishmentUnprocessedEvent extends Model
{
    const CREATED_AT = "q195_created_at";
    const UPDATED_AT = "q195_updated_at";

    protected $primaryKey = "q195_sequencial";
}
