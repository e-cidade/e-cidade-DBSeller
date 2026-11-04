<?php

namespace App\Domain\Saude\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $fa73_matestoqueini
 * @property string $fa73_descricao
 * @property string $fa73_campo
 * @property integer $fa73_matestoqueitem
 */
class BnafarErro extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'farmacia.bnafarerros';
    protected $primaryKey = null;
}
