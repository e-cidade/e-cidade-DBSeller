<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal as ModelsRhPessoal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class RhCargo
 * @property int rh04_instit
 * @property int rh04_codigo
 * @property string rh04_descr
 * @property string rh04_datainicial
 * @property string rh04_datafinal
 * @property string rh04_descricaoatividades
 *
 * @package App\Domain\RecursosHumanos\Pessoal\Model
 */
class RhCargo extends Model
{
    protected $table = 'pessoal.rhcargo';
    protected $primaryKey = ['rh04_codigo', 'rh04_instit'];
    public $incrementing = false;
    public $timestamps = false;
}
