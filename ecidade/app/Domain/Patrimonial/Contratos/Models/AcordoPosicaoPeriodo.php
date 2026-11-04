<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

class AcordoPosicaoPeriodo extends Model
{
    protected $primaryKey = 'ac36_sequencial';
    protected $table = 'acordoposicaoperiodo';
    protected $fillable = [];
    public $timestamps = false;

    public function posicao()
    {
        $this->belongsTo(AcordoPosicao::class, 'ac26_sequencial', 'ac36_acordoposicao');
    }
}
