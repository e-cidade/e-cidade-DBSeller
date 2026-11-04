<?php

namespace App\Domain\RecursosHumanos\ESocial\Models;

use App\Domain\RecursosHumanos\Pessoal\Model\Instituicao\Instituicao;
use Illuminate\Database\Eloquent\Model;

class InformacaoComplementar extends Model
{
    protected $table = 'recursoshumanos.informacoes_complementares';
    protected $primaryKey = 'eso40_sequencial';
    protected $fillable = [
        'eso40_sequencial',
        'eso40_tp_insc',
        'eso40_nr_insc',
        'eso40_num_insc',
        'eso40_ind_subst_patr',
        'eso40_perc_red_contrib',
        'eso40_cod_lotacao',
        'eso40_fator_mes',
        'eso40_fator_13',
        'eso40_perc_transf',
        'eso40_cod_inst',
        'eso40_periodo'
    ];

    public $timestamps = false;

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class, 'eso40_cod_inst', 'codigo');
    }
}
