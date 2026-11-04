<?php

namespace App\Domain\Configuracao\Menu\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Illuminate\Database\Eloquent\Model;

class ItemMenuFavorito extends Model
{
    protected $table = 'configuracoes.itens_menu_usuario_favoritos';
    public $incrementing = false;
    protected $primaryKey = ['id_item_menu', 'id_usuario', 'id_insituicao'];
    public $timestamps = false;
    protected $fillable = [
        'id_item_menu',
        'id_usuario',
        'id_insituicao',
        'modulo',
        'area'
    ];

    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'id_insituicao', 'codigo');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item_menu', 'id_item');
    }
}
