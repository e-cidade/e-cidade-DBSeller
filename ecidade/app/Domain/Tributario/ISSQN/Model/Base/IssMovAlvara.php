<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvara
 *
 * @property int $q120_sequencial
 * @property int $q120_issalvara
 * @property int $q120_isstipomovalvara
 * @property Carbon $q120_dtmov
 * @property int|null $q120_validadealvara
 * @property int $q120_usuario
 * @property string|null $q120_obs
 * @mixin \Eloquent
 */
class IssMovAlvara extends Model
{
    protected $table = "issmovalvara";
    protected $primaryKey = "q120_sequencial";
    public $timestamps = false;

    protected $casts = [
        "q120_dtmov" => "date"
    ];
}
