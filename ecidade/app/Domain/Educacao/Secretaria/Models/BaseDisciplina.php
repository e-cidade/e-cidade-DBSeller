<?php

namespace App\Domain\Educacao\Secretaria\Models;

use App\Domain\Educacao\Escola\Models\DisciplinaEnsino;
use Illuminate\Database\Eloquent\Model;

class BaseDisciplina extends Model
{
    protected $table = 'escola.basemps';
    public $timestamps = false;
    protected $primaryKey = 'ed34_i_codigo';
    protected $fillable = [
        'ed34_i_codigo',
        'ed34_i_base',
        'ed34_i_serie',
        'ed34_i_disciplina',
        'ed34_i_qtdperiodo',
        'ed34_i_chtotal',
        'ed34_c_condicao',
        'ed34_i_ordenacao',
        'ed34_lancarhistorico',
        'ed34_disiciplinaglobalizada',
        'ed34_caracterreprobatorio',
        'ed34_basecomum',
        'ed34_areaconhecimento',
        'ed34_procedimento',
        'ed34_tipobase',
        'ed34_unidade_curricular'
    ];

    public function disciplinaEnsino()
    {
        return $this->belongsTo(DisciplinaEnsino::class, 'ed34_i_disciplina', 'ed12_i_codigo');
    }

    public function tipoBase()
    {
        return $this->belongsTo(TipoBase::class, 'ed34_tipobase', 'ed182_id');
    }

    public function unidadeCurricular()
    {
        return $this->belongsTo(UnidadeCurricular::class, 'ed34_unidade_curricular', 'ed199_id');
    }
}
