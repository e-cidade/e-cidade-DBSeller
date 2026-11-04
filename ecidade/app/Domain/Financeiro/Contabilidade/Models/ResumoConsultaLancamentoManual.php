<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

class ResumoConsultaLancamentoManual extends Model
{
    protected $table = 'contabilidade.resumo_consulta_lancamento_manual';
    protected $primaryKey = 'c70_codlan';
    public $timestamps = false;
}
