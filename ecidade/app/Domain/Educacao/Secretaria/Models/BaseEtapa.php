<?php

namespace App\Domain\Educacao\Secretaria\Models;

use App\Domain\Educacao\Escola\Models\Etapa;
use Illuminate\Database\Eloquent\Model;

class BaseEtapa extends Model
{
    protected $table = 'escola.baseserie';
    protected $primaryKey = 'ed87_i_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed87_i_codigo',
        'ed87_i_serieinicial',
        'ed87_i_seriefinal'
    ];

    public function etapaFinal()
    {
        return $this->belongsTo(Etapa::class, 'ed87_i_seriefinal', 'ed11_i_codigo');
    }

    public function etapaInicial()
    {
        return $this->belongsTo(Etapa::class, 'ed87_i_serieinicial', 'ed11_i_codigo');
    }
}
