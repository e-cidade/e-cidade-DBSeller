<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use App\Domain\Tributario\Cadastro\Models\Bairro;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssBairro
 *
 * @property int $q13_inscr
 * @property int|null $q13_bairro
 * @property-read Bairro $bairro
 * @mixin \Eloquent
 */
class IssBairro extends Model
{
    protected $table = 'issbairro';
    public $timestamps = false;
    public $incrementing = false;

    public function bairro()
    {
        return $this->hasOne(Bairro::class, "j13_codi", "q13_bairro");
    }
}
