<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\Isscadsimplesbaixa
 *
 * @property int $q39_sequencial
 * @property int $q39_isscadsimples
 * @property string|null $q39_dtbaixa
 * @property int $q39_issmotivobaixa
 * @property string|null $q39_obs
 * @mixin \Eloquent
 */
class Isscadsimplesbaixa extends Model
{
    protected $table = "isscadsimplesbaixa";
    public $timestamps = false;
    protected $primaryKey = "q39_sequencial";
}
