<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\TabativBaixa
 *
 * @property int $q11_inscr
 * @property int $q11_seq
 * @property int|null $q11_processo
 * @property bool|null $q11_oficio
 * @property string|null $q11_obs
 * @property int $q11_login
 * @property string|null $q11_data
 * @property string|null $q11_hora
 * @property string|null $q11_numero
 * @mixin \Eloquent
 */
class TabativBaixa extends Model
{
    protected $table = "tabativbaixa";
    public $timestamps = false;
    public $incrementing = false;
}
