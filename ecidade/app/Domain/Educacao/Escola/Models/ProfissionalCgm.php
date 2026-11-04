<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

class ProfissionalCgm extends Model
{
    protected $table = 'escola.rechumanocgm';
    protected $primaryKey = 'ed285_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'ed285_i_cgm', 'z01_numcgm');
    }
}
