<?php

namespace App\Domain\Educacao\Escola\Models;

use BaconQrCode\Common\Mode;
use Illuminate\Database\Eloquent\Model;

class CensoEstado extends Model
{
    protected $table = 'escola.censouf';
    protected $primaryKey = 'ed260_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
}
