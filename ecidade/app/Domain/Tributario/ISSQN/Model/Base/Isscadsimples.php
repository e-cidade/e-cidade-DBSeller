<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\Isscadsimples
 *
 * @mixin \Eloquent
 * @property int $q38_sequencial
 * @property int $q38_inscr
 * @property string|null $q38_dtinicial
 * @property int|null $q38_categoria
 * @property string|null $q38_observacao
 */
class Isscadsimples extends Model
{
    protected $table = "isscadsimples";
    public $timestamps = false;
    protected $primaryKey = "q38_sequencial";
}
