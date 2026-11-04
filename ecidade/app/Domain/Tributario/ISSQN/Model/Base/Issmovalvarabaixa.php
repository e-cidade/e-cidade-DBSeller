<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\Issmovalvarabaixa
 *
 * @mixin \Eloquent
 * @property int $q129_sequecial
 * @property int $q129_issmovalvara
 * @property int|null $q129_tipobaixa
 */
class Issmovalvarabaixa extends Model
{
    protected $table = "issmovalvarabaixa";
    protected $primaryKey = "q129_sequecial";
    public $timestamps = false;
}
