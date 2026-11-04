<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\Escola\Models\NecessidadeEspecial;
use App\Domain\Educacao\Escola\Models\NecessidadeSubdivisao;
use \Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Collection;

class CandidatoNecessidades extends Model
{
    protected $table = 'plugins.basenecess';
    public $timestamps = false;
    protected $primaryKey = 'mo11_codigo';
    protected $appends = ['subdivisoes'];

    public function necessidade()
    {
        return $this->belongsTo(NecessidadeEspecial::class, 'mo11_necess', 'ed48_i_codigo');
    }

    public function getSubdivisoesAttribute()
    {
        $subs = CandidatoNecessidadeSubdivisao
            ::select('mo26_subdivisao')
            ->where('mo26_base_necessidade', $this->mo11_codigo)->get();

        if ($subs->count() == 0) {
            return collect([]);
        }

        $selecionadas = $subs->map(function ($sub) {
            return $sub->mo26_subdivisao;
        })->toArray();

        $subs = $this->necessidade->subdivisoes->map(function ($sub) use ($selecionadas) {
            $retorno = $sub;
            if (in_array($sub->ed185_sequencial, $selecionadas)) {
                $retorno->selecionada = true;
            }
            return $retorno;
        });

        return  $subs;
    }
}
