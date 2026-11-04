<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Educacao\Escola\Models\Views\ProfissionaisEscolasView;
use Illuminate\Database\Eloquent\Model;

class ProfissionalEscola extends Model
{
    protected $table = 'escola.rechumanoescola';
    protected $primaryKey = 'ed75_i_codigo';
    protected $dates = ['ed75_d_ingresso', 'ed75_i_saidaescola'];
    public $timestamps = false;
    public $incrementing = false;

    public function atividades()
    {
        return $this->hasMany(AtividadeProfissionalEscola::class, 'ed22_i_rechumanoescola', 'ed75_i_codigo');
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'ed75_i_rechumano', 'ed20_i_codigo');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'ed75_i_escola', 'ed18_i_codigo');
    }
    
    public function profissionaisView()
    {
        return $this->belongsTo(ProfissionaisEscolasView::class, 'ed75_i_codigo', 'cod_rechumano_escola');
    }
}
