<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use App\Domain\Tributario\Cadastro\Models\Ruas;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssRuas
 *
 * @property int $q02_inscr
 * @property int $j14_codigo
 * @property int|null $q02_numero
 * @property string|null $q02_compl
 * @property string|null $q02_cxpost
 * @property string|null $z01_cep
 * @property-read Ruas $rua
 * @mixin \Eloquent
 */
class IssRuas extends Model
{
    protected $table = 'issruas';
    public $timestamps = false;
    public $incrementing = false;

    public function rua()
    {
        return $this->hasOne(Ruas::class, "j14_codigo", "j14_codigo");
    }
}
