<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssProcesso
 *
 * @property int $q14_inscr
 * @property int $q14_proces
 * @mixin \Eloquent
 */
class IssProcesso extends Model
{
    protected $table = 'issprocesso';
    public $timestamps = false;
    public $incrementing = false;
}
