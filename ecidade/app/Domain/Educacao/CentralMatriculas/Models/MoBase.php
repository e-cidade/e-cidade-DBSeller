<?php

namespace App\Domain\Educacao\CentralMatriculas\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MoBase
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $mo01_codigo
 */
class MoBase extends Model
{
    protected $table = 'plugins.mobase';
    protected $primaryKey = 'mo01_codigo';
    public $timestamps = false;
    public $incrementing = false;
}
