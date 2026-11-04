<?php

namespace App\Domain\Patrimonial\Protocolo\Model\Processo;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'protocolo.prottipodocumentoprocesso';
    protected $primaryKey = 'p91_sequencial';
    protected $appends = ["descricao_completa"];

    public function getDescricaoCompletaAttribute()
    {
        return "{$this->p91_sequencial} - {$this->p91_descricao} ($this->p91_sigla)";
    }

    public function tiposDeprocesso()
    {
        return $this->hasMany(TipoProc::class, 'p51_prottipodocumentoprocesso');
    }
}
