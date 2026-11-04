<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

class RhPessoal extends Model
{
    protected $table = 'pessoal.rhpessoal';
    protected $primaryKey = 'rh01_regist';

    public $timestamps = false;

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'rh01_numcgm', 'z01_numcgm');
    }
}
