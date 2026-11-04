<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssBasePorte
 *
 * @property int $q45_inscr
 * @property int $q45_codporte
 * @mixin \Eloquent
 */
class IssBasePorte extends Model
{
    protected $table = 'issbaseporte';
    public $timestamps = false;
}
