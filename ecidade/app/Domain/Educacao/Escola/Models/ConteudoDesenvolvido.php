<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Educacao\Secretaria\Models\TiposIntrumentosAvaliativos;
use Illuminate\Database\Eloquent\Model;

class ConteudoDesenvolvido extends Model
{
    protected $table = 'escola.diario_classe_bncc';
    protected $primaryKey = 'ed155_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed155_codigo',
        'ed155_regencia',
        'ed155_db_usuarios',
        'ed155_data',
        'ed155_conteudo',
        'ed155_turmaturnoreferente',
        'ed155_tipo_instrumento_avaliativo',
        'ed155_aulas_dadas'
    ];

    public function regencia()
    {
        return $this->belongsTo(Regencia::class, 'ed155_regencia', 'ed59_i_codigo');
    }

    public function turmaTurnoReferente()
    {
        return $this->belongsTo(
            TurmaTurnoReferente::class,
            'ed155_turmaturnoreferente',
            'ed336_codigo'
        );
    }

    public function habilidades()
    {
        $this->hasMany(
            HabilidadeDesenvolvida::class,
            'ed156_diario_classe_bncc',
            'ed155_codigo'
        );
    }

    public function tipoInstrumentoAvaliativo()
    {
        return $this->belongsTo(
            TiposIntrumentosAvaliativos::class,
            'ed155_tipo_instrumento_avaliativo',
            'ed201_id'
        );
    }
}
