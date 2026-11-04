<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Saude\ESF\Models\ProntuarioEsf;

/**
 * Representa uma Ficha de Atendimento(FAA)/Consulta Médica
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer $sd24_i_codigo
 * @property integer $sd24_i_ano
 * @property integer $sd24_i_mes
 * @property integer $sd24_i_seq
 * @property integer $sd24_i_unidade
 * @property integer $sd24_i_numcgs
 * @property string $sd24_v_motivo
 * @property \DateTime $sd24_d_cadastro
 * @property string $sd24_c_cadastro
 * @property integer $sd24_i_cid
 * @property string $sd24_v_pressao
 * @property float $sd24_f_peso
 * @property float $sd24_f_temperatura
 * @property integer $sd24_i_profissional
 * @property string $sd24_t_diagnostico
 * @property integer $sd24_t_siasih
 * @property string $sd24_c_digitada
 * @property integer $sd24_i_login
 * @property integer $sd24_i_motivo
 * @property integer $sd24_i_tipo
 * @property integer $sd24_i_acaoprog
 * @property integer $sd24_setorambulatorial
 * @property integer $sd24_idadegestacional
 * @property \DateTime $sd24_dum
 *
 * @property CgsUnidade $paciente
 * @property Unidade $unidade
 * @property ProfissionalAtendimento $profissionalAtendimento
 * @property ProntuarioEsf $prontuarioEsf
 * @property \Illuminate\Database\Eloquent\Collection $prontuarioProblemas
 * @property \Illuminate\Database\Eloquent\Collection $procedimentos
 * @property MotivoAtendimento $motivoAtendimento
 * @property prontuarioClassificacaoRisco $prontuarioClassificacaoRisco
 * @property MovimentacaoProntuario[] $movimentacoes
 */
class Prontuario extends Model
{
    protected $table = 'ambulatorial.prontuarios';
    protected $primaryKey = 'sd24_i_codigo';
    public $timestamps = false;

    public $casts = [
        'sd24_d_cadastro' => 'datetime',
        'sd24_dum' => 'datetime'
    ];

    public function paciente()
    {
        return $this->belongsTo(CgsUnidade::class, 'sd24_i_numcgs', 'z01_i_cgsund');
    }

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'sd24_i_unidade', 'sd02_i_codigo');
    }

    public function profissionalAtendimento()
    {
        return $this->hasOne(ProfissionalAtendimento::class, 's104_i_prontuario', 'sd24_i_codigo');
    }

    public function prontuarioEsf()
    {
        return $this->hasOne(ProntuarioEsf::class, 'sd30_i_prontuario', 'sd24_i_codigo');
    }

    public function problemasPaciente()
    {
        return $this->belongsToMany(
            ProblemaPaciente::class,
            'ambulatorial.prontuario_problemaspaciente',
            's171_prontuario',
            's171_problemapaciente'
        )->using(ProntuarioProblemaPaciente::class);
    }

    public function procedimentos()
    {
        return $this->hasMany(ProcedimentoProntuario::class, 'sd29_i_prontuario', 'sd24_i_codigo');
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoProntuario::class, 'sd102_prontuarios', 'sd24_i_codigo');
    }

    public function prontuarioClassificacaoRisco()
    {
        return $this->hasOne(ProntuarioClassificacaoRisco::class, 'sd101_prontuarios', 'sd24_i_codigo');
    }

    public function motivoAtendimento()
    {
        return $this->belongsTo(MotivoAtendimento::class, 'sd24_i_motivo', 's144_i_codigo');
    }
}
