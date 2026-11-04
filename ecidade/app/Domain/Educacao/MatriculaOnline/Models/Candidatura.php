<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\Escola\Models\Etapa;
use Illuminate\Database\Eloquent\Model;

class Candidatura extends Model
{
    protected $table = 'plugins.basefase';
    public $timestamps = false;
    protected $primaryKey = 'mo12_codigo';
    protected $appends = ['foiAlocado'];
    /**
     * @var mixed
     */
    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'mo12_base', 'mo01_codigo');
    }

    public function fase()
    {
        return $this->belongsTo(Fase::class, 'mo12_fase', 'mo04_codigo');
    }

    public function alocado()
    {
        return $this->hasOne(
            Alocado::class,
            'mo13_base',
            'mo12_base'
        )->where('mo13_fase', $this->mo12_fase);
    }

    public function getFoiAlocadoAttribute()
    {
        return !is_null($this->alocado) && $this->alocado->count() > 0;
    }
}
