<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma movimentação do prontuário
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property int $sd102_codigo
 * @property int $sd102_prontuarios
 * @property int $sd102_db_usuarios
 * @property int $sd102_setorambulatorial
 * @property \DateTime $sd102_data
 * @property string $sd102_hora
 * @property int $sd102_situacao
 * @property string $sd102_observacao
 * @property int $sd102_profissionalencaminhado
 * @property int $sd102_profissionalatendimento
 * @property EspecialidadeProfissional $profissionalAtendimento
 * @property EspecialidadeProfissional $profissionalEncaminhado
 * @property SetorAmbulatorial $setorAmbulatorial
 */
class MovimentacaoProntuario extends Model
{
    protected $table = 'ambulatorial.movimentacaoprontuario';
    protected $primaryKey = 'sd102_codigo';
    public $timestamps = false;

    const SITUACAO_ENTRADA            = 1;
    const EM_ATENDIMENTO              = 2;
    const SITUACAO_FINALIZADA         = 3;
    const SITUACAO_ATESTADO_EM_BRANCO = 4;
    const SITUACAO_ENCAMINHADA        = 5;

    public function profissionalAtendimento()
    {
        return $this->belongsTo(EspecialidadeProfissional::class, 'sd102_profissionalatendimento', 'sd27_i_codigo');
    }

    public function profissionalEncaminhado()
    {
        return $this->belongsTo(EspecialidadeProfissional::class, 'sd102_profissionalencaminhado', 'sd27_i_codigo');
    }

    public function setorAmbulatorial()
    {
        return $this->belongsTo(SetorAmbulatorial::class, 'sd102_setorambulatorial', 'sd91_codigo');
    }
}
