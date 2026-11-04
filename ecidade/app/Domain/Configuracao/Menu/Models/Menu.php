<?php

namespace App\Domain\Configuracao\Menu\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_item
 * @property int $id_item_filho
 * @property int $menusequencia
 * @property int $modulo
 */
class Menu extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'configuracoes.db_menu';
    protected $primaryKey = null;
}
