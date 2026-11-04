<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\Escola\Models\Etapa;
use Illuminate\Database\Eloquent\Model;

class IdadeEtapa extends Model
{
    protected $table = 'plugins.idadeetapa';
    public $timestamps = false;
    protected $primaryKey = 'mo15_sequencial';
    public $incrementing = true;

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'mo15_etapa', 'ed11_i_codigo');
    }
}
