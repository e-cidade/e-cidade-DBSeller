<?php

namespace App\Domain\Educacao\Escola\Models\Views;

use App\Domain\Configuracao\Usuario\Models\UsuarioCgm;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Models\Profissional;
use App\Domain\Educacao\Escola\Models\ProfissionalEscola;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal;
use Illuminate\Database\Eloquent\Builder;

class ProfissionaisEscolasView extends Model
{
    protected $table = 'escola.profissionais_escolas';
    protected $appends = ['usuarioInterno', 'temPermissaoDiario', 'permissaoDiario'];

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'cod_cgm', 'z01_numcgm');
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'cod_rechumano', 'ed20_i_codigo');
    }

    public function profissionalEscola()
    {
        return $this
            ->belongsTo(ProfissionalEscola::class, 'cod_rechumano_escola', 'ed75_i_codigo');
    }

    public function rhPessoal()
    {
        return $this->belongsTo(RhPessoal::class, 'matricula', 'rh01_regist');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'cod_escola', 'ed18_i_codigo');
    }

    public function getUsuarioInternoAttribute()
    {
        $users =  $this->cgm->usuarios->filter(function (UsuarioCgm $usuarioCgm) {
            return $usuarioCgm->usuario()
                    ->where('usuarioativo', 1)
                    ->where('usuext', 0)
                    ->get()
                    ->count() > 0;
        });

        return $users->count() > 0 ? $users->first()->usuario->id_usuario : null;
    }

    public function scopeApenasUsuarioInterno(Builder $query)
    {
        $query->whereHas('cgm', function (Builder $query) {
            $query->whereHas('usuarios', function (Builder $query) {
                $query->whereHas('usuario', function (Builder $query) {
                    $query->where('usuarioativo', 1)->where('usuext', 0);
                });
            });
        });
    }

    public function scopeApenasAtivos(Builder $query)
    {
        return $query->whereHas('profissionalEscola', function (Builder $query) {
            $query->whereNull('ed75_i_saidaescola');
        });
    }

    public function getTemPermissaoDiarioAttribute()
    {
        return $this->permissaoDiario === 'PROFESSOR' || $this->permissaoDiario === 'TOTAL';
    }

    public function getPermissaoDiarioAttribute()
    {
        $perm = [];
        foreach ($this->profissionalEscola->atividades as $atividade) {
            $atividade = $atividade->atividade;
            if ($atividade->ed01_permissao_diario === 1) {
                $perm['TOTAL'] = true;
            } elseif ($atividade->ed01_permissao_diario === 0 && $atividade->ed01_c_docencia === 'S') {
                $perm['PROFESSOR'] = true;
            } else {
                $perm['SEM_PERMISSAO'] = true;
            }
        }

        if (array_key_exists('TOTAL', $perm)) {
            return 'TOTAL';
        } elseif (array_key_exists('PROFESSOR', $perm)) {
             return 'PROFESSOR';
        } else {
            return 'SEM PERMISÃO';
        }
    }
}
