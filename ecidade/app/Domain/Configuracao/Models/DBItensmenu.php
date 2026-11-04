<?php

namespace App\Domain\Configuracao\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int id_item
 * @property string|null descricao
 * @property string|null help
 * @property string|null funcao
 * @property int|null itemativo
 * @property string|null manutencao
 * @property string|null desctec
 * @property bool|null libcliente
 */
class DBItensmenu extends Model
{
    public $timestamps = false;

    protected $table = 'configuracoes.db_itensmenu';
    protected $primaryKey = 'id_item';

    public $fillable = [
        'id_item',
        'descricao',
        'help',
        'funcao',
        'itemativo',
        'manutencao',
        'desctec',
        'libcliente'
    ];

    public static function getNewItemMenu()
    {
        $data = DB::select("select nextval('db_itensmenu_id_item_seq') as id_item");

        return $data[0]->id_item;
    }
}
