<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaBNCC extends Model
{
    protected $table = 'escola.bnccdisciplinas';
    protected $primaryKey = 'ed149_sequencial';

    public function disciplnasEquivalentesBNCC()
    {
        $this->hasMany(DisciplinaEquivalenteBNCC::class, 'ed153_bnccdisciplina', 'ed149_sequencial');
    }
}
