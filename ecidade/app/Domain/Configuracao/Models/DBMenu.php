<?php

namespace App\Domain\Configuracao\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id_item
 * @property int id_item_filho
 * @property int|null menusequencia
 * @property int modulo
 */
class DBMenu extends Model
{
    public $timestamps = false;

    protected $table = 'configuracoes.db_menu';
    protected $primaryKey = 'id_item';

    public $fillable = [
        'id_item',
        'id_item_filho',
        'menusequencia',
        'modulo'
    ];

    public function item()
    {
        return $this->hasOne(DBItensmenu::class, 'id_item', 'id_item');
    }

    public function filhoItens()
    {
        return $this->hasMany(DBItensmenu::class, 'id_item', 'id_item_filho');
    }
}
