<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssZona
 *
 * @property int $q35_inscr
 * @property int|null $q35_zona
 * @mixin \Eloquent
 */
class IssZona extends Model
{
    protected $table = 'isszona';
    public $timestamps = false;
}
