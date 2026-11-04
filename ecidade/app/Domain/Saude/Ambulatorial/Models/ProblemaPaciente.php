<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Configuracao\Usuario\Models\Usuario;

/**
 * Representa um problema/condição de saúde vinculado a um paciente
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer $s170_id
 * @property integer $s170_problema
 * @property integer $s170_paciente
 * @property integer $s170_usuario
 * @property \DateTime $s170_data
 * @property \DateTime $s170_data_inicio
 * @property \DateTime $s170_data_fim
 * @property boolean $s170_ativo
 *
 * @property Problema $problema
 * @property CgsUnidade $paciente
 * @property Usuario $usuario
 * @property Prontuario $prontuarios
 */
class ProblemaPaciente extends Model
{
    protected $table = 'ambulatorial.problemaspaciente';
    protected $primaryKey = 's170_id';
    public $timestamps = false;

    public $casts = [
        's170_data' => 'datetime',
        's170_data_inicio' => 'datetime',
        's170_data_fim' => 'datetime'
    ];

    public function scopePreNatal(Builder $query)
    {
        return $query->where('s170_problema', Problema::PRE_NATAL);
    }

    public function problema()
    {
        return $this->belongsTo(Problema::class, 's170_problema', 's169_id');
    }

    public function paciente()
    {
        return $this->belongsTo(CgsUnidade::class, 's170_paciente', 'z01_i_cgsund');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 's170_usuario', 'id_usuario');
    }

    public function prontuarios()
    {
        return $this->belongsToMany(
            Prontuario::class,
            'ambulatorial.prontuario_problemaspaciente',
            's171_problemapaciente',
            's171_prontuario'
        )->using(ProntuarioProblemaPaciente::class);
    }
}
