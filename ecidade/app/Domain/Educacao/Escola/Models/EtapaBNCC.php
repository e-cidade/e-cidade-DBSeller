<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaBNCC extends Model
{
    protected $table = 'escola.bnccetapas';
    protected $primaryKey = 'ed152_sequencial';

    public function etapas()
    {
        return $this->hasMany(EtapaEquivalenteBNCC::class, 'ed154_bnccetapa', 'ed152_sequencial');
    }
}
