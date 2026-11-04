<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AjudaCusto extends Model
{
    protected $table = 'pessoal.ajudacusto';
    protected $primaryKey = 'rh312_sequencial';

    public $timestamps = false;

    public function dependente()
    {
        return $this->belongsTo(RhDepend::class, 'rh312_dependente', 'rh31_codigo')
            ->with('dependeplug');
    }

    public function servidor()
    {
        return $this->belongsTo(RhPessoal::class, 'rh312_regist', 'rh01_regist')
            ->with(['cgm' => function ($query) {
                $query->select(
                    'z01_numcgm',
                    'z01_nome',
                    'z01_nasc',
                    'z01_cgccpf'
                );
            }]);
    }

    public function unidadeEnsino()
    {
        return $this->belongsTo(Cgm::class, 'rh312_unidade_ensino', 'z01_numcgm');
    }

    public function lancamentos()
    {
        return $this->hasMany(
            LancamentoAjudaCusto::class,
            'rh313_ajuda_custo',
            'rh312_sequencial'
        )->orderBy('rh313_sequencial');
    }

    public function totalLancamentos()
    {
        return $this->lancamentos->count();
    }
}
