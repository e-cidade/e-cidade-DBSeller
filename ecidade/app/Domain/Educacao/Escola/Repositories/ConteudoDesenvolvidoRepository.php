<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Configuracao\Usuario\Models\UsuarioCgm;
use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\ConteudoDesenvolvido;
use App\Domain\Educacao\Escola\Models\Views\ProfissionaisEscolasView;
use App\Domain\Educacao\Escola\Resources\ConteudoDesenvolvidoResource;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConteudoDesenvolvidoRepository extends BaseRepository
{
    protected $modelClass = ConteudoDesenvolvido::class;

    public function find($id)
    {
        return $this->newQuery()->find($id);
    }

    public function getByFiltros($filtros)
    {
        $query = $this->newQuery();
        foreach ($filtros as $key => $filtro) {
            switch ($key) {
                case 'regencia':
                    if (is_array($filtro)) {
                        $query->whereIn("ed155_regencia", $filtro);
                    } else {
                        $query->where("ed155_regencia", $filtro);
                    }
                    break;
                case 'data':
                    $query->where("ed155_data", new Carbon($filtro));
                    break;
                case 'periodo':
                    $query
                        ->where('ed155_data', '>=', $filtro->ed53_d_inicio)
                        ->where('ed155_data', '<=', $filtro->ed53_d_fim);
                    break;
                default:
                    break;
            }
        }

        return $query->get();
    }


    public function salvar($parametros)
    {
        $parametros = ConteudoDesenvolvidoResource::toArrayModel($parametros);
        $pk = array_remove($parametros, 'ed155_codigo');
        if (is_null($pk)) {
            return $this->newQuery()->create($parametros);
        } else {
            return tap($this->find($pk))->update($parametros);
        }
    }

    public function getConteudosByTurma($turma)
    {
        return $this->newQuery()->whereHas('turmaTurnoReferente', function ($query) use ($turma) {
            $query->where('ed336_turma', $turma);
        })->get();
    }

    public function getConteudosRegenteEscola($idUser, $idEscola, $dataSistema, $campos)
    {
        $campos = implode(',', $campos);
        $cgm = UsuarioCgm::where('id_usuario', $idUser)->first()->cgm;
        $rechumano = ProfissionaisEscolasView
            ::where('cod_cgm', $cgm->z01_numcgm)
            ->where('cod_escola', $idEscola)
            ->get();
        $query = $this->newQuery()->selectRaw($campos)
            ->join('regencia', 'ed59_i_codigo', '=', 'ed155_regencia')
            ->join('regenciahorario', 'ed58_i_regencia', '=', 'ed59_i_codigo')
            ->join('turma', 'ed59_i_turma', '=', 'ed57_i_codigo')
            ->join('calendario', 'ed57_i_calendario', '=', 'ed52_i_codigo')
            ->join('turmaturnoreferente', 'ed336_codigo', '=', 'ed155_turmaturnoreferente')
            ->where("ed57_i_escola", $idEscola)
            ->where("ed155_db_usuarios", $idUser)
            ->whereRaw(
                "'{$dataSistema->format('Y-m-d')}'
                    between ed52_d_inicio and ed52_d_fim"
            );

        if ($rechumano->count() > 1) {
            $cod = $rechumano->map(function ($rec) {
                return $rec->cod_rechumano;
            });
            $query->whereIn("ed58_i_rechumano", $cod->toArray());
        } else {
            $query->where("ed58_i_rechumano", $rechumano->first()->cod_rechumano);
        }
        return $query;
    }

    public function getConteudosRegenteEscolaPaginator($idUser, $idEscola, $linhas, $filtros)
    {
        $query = $this->getConteudosRegenteEscola(
            $idUser,
            $idEscola,
            $filtros['dataSistema'],
            ['diario_classe_bncc.*']
        );
        
        foreach ($filtros as $key => $filtro) {
            switch ($key) {
                case 'turma':
                    $query->where("ed57_i_codigo", $filtro);
                    break;
                case 'etapa':
                    $query->where("ed59_i_serie", $filtro);
                    break;
                case 'regencia':
                    $query->where("ed59_i_codigo", $filtro);
                    break;
                case 'regenciaAtiva':
                    $query->where('ed58_ativo', true)
                        ->whereNotNull('ed58_datafim');
                    break;
                case 'turno':
                    $query->where('ed57_i_turno', $filtro);
                    break;
                case 'turnoReferente':
                    $query->where("ed336_turnoreferente", $filtro);
                    break;
                case 'data':
                    $query->where("ed155_data", new Carbon($filtro));
                    break;
                case 'periodo':
                    $query
                        ->where('ed155_data', '>=', $filtro->ed53_d_inicio)
                        ->where('ed155_data', '<=', $filtro->ed53_d_fim);
                    break;
                default:
                    break;
            }
        }
        $query->orderBy('ed155_data', 'desc')->distinct();

        if (is_null($linhas)) {
            return $query->get();
        }

        $total = $query->count();
        return $query->paginate($linhas)
            ->map(function ($cont) use ($total) {
                $cont->totalRegistros = $total;
                return $cont;
            });
    }

    public function excluir($codigo)
    {
        return $this->find($codigo)->delete();
    }

    public function updateConteudosLote(array $conteudos)
    {
        foreach ($conteudos as $key => $conteudo) {
            $falhas = [];
            $parametros = [];
            foreach ($conteudo as $campo => $valor) {
                switch ($campo) {
                    case 'regencia':
                        $parametros['ed155_regencia'] = $valor;
                        break;
                    case 'usuario':
                        $parametros['ed155_usuarios'] = $valor;
                        break;
                    case 'data':
                        $parametros['ed155_data'] = $valor;
                        break;
                    case 'conteudo':
                        $parametros['ed155_conteudo'] = $valor;
                        break;
                    case 'turmaTurnoReferente':
                        $parametros['ed155_turmaturnoreferente'] = $valor;
                        break;
                    case 'tipoInstrumento':
                        $parametros['ed155_tipo_instrumento_avaliativo'] = $valor;
                        break;
                    case 'aulasDadas':
                        $parametros['ed155_aulas_dadas'] = $valor;
                        break;
                }
            }

            $rs = $this->find($conteudo['codigo'])->update($parametros);
            if (!$rs) {
                $falhas[] = $conteudo['codigo'];
            }
        }

        return $falhas;
    }
}
