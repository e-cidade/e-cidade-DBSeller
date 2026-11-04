<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma classificação de risco em uma consulta médica
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property int sd78_codigo
 * @property string sd78_descricao
 * @property int sd78_peso
 * @property string sd78_labelcor
 * @property string sd78_cor
 */
class ClassificacaoRisco extends Model
{
    protected $table = 'ambulatorial.classificacaorisco';
    protected $primaryKey = 'sd78_codigo';
    public $timestamps = false;
}
