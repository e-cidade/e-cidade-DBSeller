<?php

namespace App\Domain\Configuracao\Menu\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_item
 * @property string $descricao
 * @property string $help
 * @property string $funcao
 * @property int $itemativo
 * @property string $manutencao
 * @property string $desctec
 * @property bool $libcliente
 */
class Item extends Model
{
    public $timestamps = false;
    protected $table = 'configuracoes.db_itensmenu';
    protected $primaryKey = 'id_item';
}
