<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Promitente
 *
 * @property int j41_matric
 * @property int j41_numcgm
 * @property bool|null j41_tipopro
 * @property string|null j41_promitipo
 * @property int|null j41_tipopromitente
 */
class Promitente extends Model
{
    protected $table = "cadastro.propri";

    protected $fillable = [
        'j41_matric',
        'j41_numcgm',
        'j41_tipopro',
        'j41_promitipo',
        'j41_tipopromitente'
    ];
}
