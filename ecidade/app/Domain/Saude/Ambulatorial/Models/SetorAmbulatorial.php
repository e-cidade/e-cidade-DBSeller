<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma setor ambulatorial
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property int sd91_codigo
 * @property string sd91_unidades
 * @property string sd91_descricao
 * @property int sd91_local
 */

class SetorAmbulatorial extends Model
{
    protected $table = 'ambulatorial.setorambulatorial';
    protected $primaryKey = 'sd91_codigo';
    public $timestamps = false;
}
