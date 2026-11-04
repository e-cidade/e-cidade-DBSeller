<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Model;

class LancamentoAjudaCusto extends Model
{
    protected $table = 'pessoal.lancamentoajudacusto';
    protected $primaryKey = 'rh313_sequencial';

    public $timestamps = false;

    public function ajuda()
    {
        return $this->belongsTo(AjudaCusto::class, 'rh313_ajuda_custo', 'rh312_sequencial');
    }
}
