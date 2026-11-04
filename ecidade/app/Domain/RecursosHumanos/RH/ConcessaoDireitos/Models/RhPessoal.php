<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

class RhPessoal extends Model
{
    public $timestamps = false;
    protected $table    = "rhpessoal";
    protected $primaryKey = "rh01_regist";
    protected $fillable = [];

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'rh01_numcgm', 'z01_numcgm');
    }
}
