<?php

namespace App\Domain\Configuracao\Menu\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_item
 * @property string $nome_modulo
 * @property string $descr_modulo
 * @property string $imagem
 * @property bool $temexerc
 * @property string $nome_manual
 *
 * @property Item $item
 * @property Permissao $permissao
 */
class Modulo extends Model
{
    public $timestamps = false;
    protected $table = 'configuracoes.db_modulos';
    protected $primaryKey = 'id_item';

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item', 'id_item');
    }

    public function permissao()
    {
        return $this->hasOne(Permissao::class, 'id_item', 'id_item');
    }
}
