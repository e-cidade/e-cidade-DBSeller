<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\AtivPrinc
 *
 * @property int $q88_inscr
 * @property int|null $q88_seq
 * @mixin \Eloquent
 */
class AtivPrinc extends Model
{
    protected $table = 'ativprinc';
    public $timestamps = false;
    public $incrementing = false;
}
