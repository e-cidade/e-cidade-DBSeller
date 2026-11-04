<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaEquivalenteBNCC extends Model
{
    protected $table = 'escola.seriebnccetapas';
    protected $primaryKey = 'ed154_sequencial';

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'ed154_serie', 'ed11_i_codigo');
    }

    public function etapaBncc()
    {
        return $this->belongsTo(EtapaBNCC::class, 'ed154_bnccetapa', 'ed152_sequencial');
    }
}
