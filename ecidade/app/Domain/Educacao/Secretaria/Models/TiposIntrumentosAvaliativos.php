<?php

namespace App\Domain\Educacao\Secretaria\Models;

use Illuminate\Database\Eloquent\Model;

class TiposIntrumentosAvaliativos extends Model
{
    protected $table = "secretariadeeducacao.tipo_instrumento_avaliativo";
    protected $primaryKey = "ed201_id";

    protected $fillable = [
        'ed201_id',
        'ed201_descricao',
        'ed201_ensinos',
        'ed201_ativo'
    ];

    protected $casts = ['ed201_ensinos' => 'array'];

    public $timestamps = false;

    public function scopeAtivo($query)
    {
        return $query->where('ed201_ativo', true);
    }
}
