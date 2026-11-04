<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Tributario\Cadastro\Models\Iptuconstr;

/**
 * Class Model Iptubase
 *
 * @property int j01_matric
 * @property int j01_numcgm
 * @property int j01_idbql
 * @property string j01_baixa
 * @property int j01_codave
 * @property double j01_fracao
 * @property int j01_tipoimovel
 * @property string j01_distrito
 * @property double j01_hectare
 * @property string j01_situcad
 * @property string j01_datacad
 * @property int j01_processo
 * @property int j01_incra
 * @property string j01_descrlocal
 * @property int j01_unidade
 * @property double j01_areaprivativa
 * @property double j01_fracaoproprietario
 * @property int j01_tipoproprietario
 */
class Iptubase extends Model
{
    protected $table = "iptubase";

    protected $fillable = [
        'j01_matric',
        'j01_numcgm',
        'j01_idbql',
        'j01_baixa',
        'j01_codave',
        'j01_fracao',
        'j01_tipoimovel',
        'j01_distrito',
        'j01_hectare',
        'j01_situcad',
        'j01_datacad',
        'j01_processo',
        'j01_incra',
        'j01_descrlocal',
        'j01_unidade',
        'j01_areaprivativa',
        'j01_fracaoproprietario',
        'j01_tipoproprietario'
    ];

    public function getJ01TipoimpAttribute()
    {
        if (isset($this->iptuconstr) and !is_null($this->iptuconstr->j39_dtdemo)) {
            return 'PREDIAL';
        }

        return 'TERRITORIAL';
    }

    public function proprietario()
    {
        return $this->hasOne(Proprietario::class, 'j01_matric', 'j01_matric');
    }

    public function iptuconstr()
    {
        return $this->hasOne(Iptuconstr::class, 'j39_matric', 'j01_matric');
    }
}
