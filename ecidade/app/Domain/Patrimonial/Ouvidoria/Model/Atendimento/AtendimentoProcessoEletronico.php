<?php

namespace App\Domain\Patrimonial\Ouvidoria\Model\Atendimento;

use Illuminate\Database\Eloquent\Model;

class AtendimentoProcessoEletronico extends Model
{

    protected $table = "ouvidoria.ouvidoriaatendimentoprocessoeletronico";
    protected $primaryKey = "ov01_sequencial";

    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, "ov33_ouvidoriaatendimento");
    }
}
