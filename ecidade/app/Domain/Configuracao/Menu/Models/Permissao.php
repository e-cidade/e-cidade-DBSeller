<?php

namespace App\Domain\Configuracao\Menu\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_usuario
 * @property int $id_item
 * @property string $permissaoativa
 * @property int $anousu
 * @property int $id_instit
 * @property int $id_modulo

 */
class Permissao extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'configuracoes.db_permissao';
    protected $primaryKey = null;
}
