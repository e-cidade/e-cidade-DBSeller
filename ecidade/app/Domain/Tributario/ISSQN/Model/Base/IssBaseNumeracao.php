<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\IssBaseNumeracao
 *
 * @property int $q133_sequencial
 * @property int|null $q133_numeracaoatual
 * @mixin \Eloquent
 */
class IssBaseNumeracao extends Model
{
    protected $table = 'issqn.issbasenumeracao';
    protected $primaryKey = 'q133_sequencial';
    public $timestamps = false;
}
