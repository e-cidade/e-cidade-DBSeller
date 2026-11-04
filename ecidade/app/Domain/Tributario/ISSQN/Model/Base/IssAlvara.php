<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssAlvara
 *
 * @property int $q123_sequencial
 * @property int $q123_isstipoalvara
 * @property int $q123_inscr
 * @property Carbon $q123_dtinclusao
 * @property int $q123_situacao
 * @property int|null $q123_usuario
 * @property bool|null $q123_geradoautomatico
 * @mixin \Eloquent
 */
class IssAlvara extends Model
{
    protected $table = "issalvara";
    protected $primaryKey = "q123_sequencial";
    public $timestamps = false;

    protected $casts = [
        "q123_dtinclusao" => "date"
    ];
}
