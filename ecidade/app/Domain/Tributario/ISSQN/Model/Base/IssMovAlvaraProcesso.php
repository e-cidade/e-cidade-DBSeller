<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvaraProcesso
 *
 * @property int $q124_sequencial
 * @property int $q124_codproc
 * @property int|null $q124_issmovalvara
 * @method static Builder|IssMovAlvaraProcesso whereQ124Codproc($value)
 * @method static Builder|IssMovAlvaraProcesso whereQ124Issmovalvara($value)
 * @method static Builder|IssMovAlvaraProcesso whereQ124Sequencial($value)
 * @mixin \Eloquent
 */
class IssMovAlvaraProcesso extends Model
{
    protected $table = "issmovalvaraprocesso";
    protected $primaryKey = "q124_sequencial";
    public $timestamps = false;
}
