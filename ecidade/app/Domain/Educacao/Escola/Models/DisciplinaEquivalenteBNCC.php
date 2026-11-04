<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaEquivalenteBNCC extends Model
{
    protected $table = 'escola.caddisciplinabnccdisciplinas';
    protected $primaryKey = 'ed153_sequencial';

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'ed153_caddisciplina', 'ed232_i_codigo');
    }

    public function disciplinaBncc()
    {
        return $this->belongsTo(DisciplinaBNCC::class, 'ed153_bnccdisciplina', 'ed149_sequencial');
    }
}
