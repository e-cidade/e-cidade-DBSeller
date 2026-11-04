<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\RecursosHumanos\Pessoal\Model\RegimeRecursosHumanos;
use Illuminate\Database\Eloquent\Model;

class Profissional extends Model
{
    protected $table = 'escola.rechumano';
    protected $primaryKey = 'ed20_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function vacinacao()
    {
        return $this->hasMany(ProfissionalVacinacao::class, 'ed181_rechumano', 'ed20_i_codigo');
    }
    public function vinculosEscolas()
    {
        return $this->hasMany(ProfissionalEscola::class, 'ed75_i_rechumano', 'ed20_i_codigo');
    }

    public function cgm()
    {
        return $this->hasOne(ProfissionalCgm::class, 'ed285_i_rechumano', 'ed20_i_codigo');
    }

    public function rhPessoal()
    {
        return $this->hasOne(ProfissionalRhPessoal::class, 'ed284_i_rechumano', 'ed20_i_codigo');
    }

    public function regenciasHorario()
    {
        return $this->hasMany(RegenciaHorario::class, 'ed58_i_rechumano', 'ed20_i_codigo');
    }
    
    public function regime()
    {
        return $this->belongsTo(RegimeRecursosHumanos::class, 'ed20_i_rhregime', 'rh30_codreg');
    }
}
