<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Propri
 *
 * @property int j42_matric
 * @property int j42_numcgm
 * @property double|null j42_fracaoproprietario
 * @property double|null j42_arealoteproprietario
 * @property int|null j42_tipoproprietario
 */
class Propri extends Model
{
    protected $table = "cadastro.propri";

    protected $fillable = [
        'j42_matric',
        'j42_numcgm',
        'j42_fracaoproprietario',
        'j42_arealoteproprietario',
        'j42_tipoproprietario'
    ];
}
