<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Iptuconstr
 *
 * @property int j39_matric
 * @property int j39_idcons
 * @property int j39_ano
 * @property double j39_area
 * @property double j39_areap
 * @property string j39_dtlan
 * @property int j39_codigo
 * @property int j39_numero
 * @property string j39_compl
 * @property string j39_dtdemo
 * @property int j39_idaument
 * @property bool j39_idprinc
 * @property string j39_habite
 * @property int j39_pavim
 * @property string j39_codprotdemo
 * @property string j39_obs
 */
class Iptuconstr extends Model
{
    protected $table = "cadastro.iptuconstr";

    protected $fillable = [
        'j39_matric',
        'j39_idcons',
        'j39_ano',
        'j39_area',
        'j39_areap',
        'j39_dtlan',
        'j39_codigo',
        'j39_numero',
        'j39_compl',
        'j39_dtdemo',
        'j39_idaument',
        'j39_idprinc',
        'j39_habite',
        'j39_pavim',
        'j39_codprotdemo',
        'j39_obs'
    ];
}
