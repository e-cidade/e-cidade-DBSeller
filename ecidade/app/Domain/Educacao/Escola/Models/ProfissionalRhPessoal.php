<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal;
use Illuminate\Database\Eloquent\Model;

class ProfissionalRhPessoal extends Model
{
    protected $table = 'escola.rechumanopessoal';
    protected $primaryKey = 'ed284_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function rhPessoal()
    {
        return $this->belongsTo(RhPessoal::class, 'ed284_i_rhpessoal', 'rh01_regist');
    }
}
