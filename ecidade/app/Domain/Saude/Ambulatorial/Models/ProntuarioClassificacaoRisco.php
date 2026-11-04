<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa a classificação de risco de um prontuário
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property int $sd101_codigo
 * @property int $sd101_prontuarios
 * @property int $sd101_classificacaorisco
 * @property ClassificacaoRisco $classificacaoRisco
 */
class ProntuarioClassificacaoRisco extends Model
{
    protected $table = 'ambulatorial.prontuariosclassificacaorisco';
    protected $primaryKey = 'sd101_codigo';
    public $timestamps = false;

    public function classificacaoRisco()
    {
        return $this->belongsTo(ClassificacaoRisco::class, 'sd101_classificacaorisco', 'sd78_codigo');
    }
}
