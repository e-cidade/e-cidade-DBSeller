<?php

namespace App\Domain\Educacao\Escola\Repositories;

use Illuminate\Support\Facades\DB;
use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Enums\NacionalidadeEnum;
use App\Domain\Educacao\Escola\Models\AlunoAtendimentoEspecial;
use App\Domain\Educacao\Escola\Resources\AtendimentoEspecialResource;

class AlunoAtendimentoEspecialRepository extends BaseRepository
{
    /**
     * Model
     *
     * @var AlunoAtendimentoEspecial
     */
    protected $modelClass = AlunoAtendimentoEspecial::class;

    public function getAtendimentosEspecial($aluno)
    {
        return $this->newQuery()
            ->where('ed197_aluno', $aluno)
            ->distinct()
            ->orderBy('ed197_data_emissao')
            ->get();
    }

    public function getTiposAtendimentos($aluno)
    {
        return $this->newQuery()
            ->where('ed197_aluno', $aluno)
            ->distinct()
            ->get();
    }

    public function deleteAtendimentoEspecial($codigo)
    {
        return $this->newQuery()
            ->where('ed197_codigo', $codigo)
            ->delete();
    }

    public function persist($parametros)
    {
        return $this->newQuery()->create(AtendimentoEspecialResource::toArrayModel($parametros));
    }

    public function update($parametros)
    {
  
        return $this->newQuery()
                    ->where('ed197_codigo', $parametros['codigo'])
                    ->update(AtendimentoEspecialResource::toArrayModel($parametros));
    }

    public function getProfissionaisEspecializados($turma)
    {
        $listagemProfissionais = [];
        $dados = new \StdClass();
        $dados->codigo = '';
        $dados->profissional = '';
        $listagemProfissionais[] = $dados;
        $profissionaisSelecionados = [];
        $listaProfissionais1 = DB::table('turmaachorarioprofissional')
            ->join('turmaac', 'turmaac.ed268_i_codigo', '=', 'turmaachorarioprofissional.ed346_turmaac')
            ->join(
                'funcaoatividade',
                'funcaoatividade.ed119_sequencial',
                '=',
                'turmaachorarioprofissional.ed346_funcaoatividade'
            )
            ->join('escola', 'escola.ed18_i_codigo', '=', 'turmaac.ed268_i_escola')
            ->join('turno', 'turno.ed15_i_codigo', '=', 'turmaac.ed268_i_turno')
            ->leftJoin('sala', 'sala.ed16_i_codigo', '=', 'turmaac.ed268_i_sala')
            ->join('calendario', 'calendario.ed52_i_codigo', '=', 'turmaac.ed268_i_calendario')
            ->join('rechumano', 'rechumano.ed20_i_codigo', '=', 'turmaachorarioprofissional.ed346_rechumano')
            ->join('pais', 'pais.ed228_i_codigo', '=', 'rechumano.ed20_i_pais')
            ->leftJoin('rechumanopessoal', 'rechumanopessoal.ed284_i_rechumano', '=', 'rechumano.ed20_i_codigo')
            ->leftJoin('rhpessoal', 'rhpessoal.rh01_regist', '=', 'rechumanopessoal.ed284_i_rhpessoal')
            ->leftJoin('cgm as cgmrh', 'cgmrh.z01_numcgm', '=', 'rhpessoal.rh01_numcgm')
            ->leftJoin('rechumanocgm', 'rechumanocgm.ed285_i_rechumano', '=', 'rechumano.ed20_i_codigo')
            ->leftJoin('cgm as cgmcgm', 'cgmcgm.z01_numcgm', '=', 'rechumanocgm.ed285_i_cgm')
            ->where('ed346_turmaac', '=', $turma)
            ->where('ed119_descricao', '=', 'Docente')
            ->select(DB::raw('
                distinct
                case 
                when ed20_i_tiposervidor = 1 then cgmrh.z01_numcgm 
                else rechumanocgm.ed285_i_rechumano
                end as codigo,
                case 
                when ed20_i_tiposervidor = 1 then trim(cgmrh.z01_nome) 
                else trim(cgmcgm.z01_nome) 
                end as profissional, 
                ed119_descricao
            '))
            ->get();

        $listaProfissionais2 = DB::table('turmaachorarioprofissionalsemrec')
            ->join('turmaac', 'turmaac.ed268_i_codigo', '=', 'turmaachorarioprofissionalsemrec.ed176_turmaac')
            ->leftJoin(
                'funcaoatividade',
                'funcaoatividade.ed119_sequencial',
                '=',
                'turmaachorarioprofissionalsemrec.ed176_funcaoatividade'
            )
            ->join('escola', 'escola.ed18_i_codigo', '=', 'turmaac.ed268_i_escola')
            ->join('turno', 'turno.ed15_i_codigo', '=', 'turmaac.ed268_i_turno')
            ->leftJoin('sala', 'sala.ed16_i_codigo', '=', 'turmaac.ed268_i_sala')
            ->join('calendario', 'calendario.ed52_i_codigo', '=', 'turmaac.ed268_i_calendario')
            ->where('ed176_rechumano', '>', 0)
            ->where('ed176_turmaac', '=', $turma)
            ->where('ed119_descricao', '=', 'Docente')
            ->select(DB::raw('
                distinct
                ed176_rechumano as codigo,
                ed119_descricao as profissional, ed119_descricao
            '))
            ->get();
        if ($listaProfissionais1->count() > 0) {
            foreach ($listaProfissionais1 as $profissional) {
                if (in_array($profissional->profissional, $profissionaisSelecionados)) {
                    continue;
                }
                $dados = new \StdClass();
                $dados->codigo = $profissional->codigo;
                $dados->profissional = $profissional->profissional;
                $listagemProfissionais[] = $dados;
                $profissionaisSelecionados[] = $profissional->profissional;
            }
        }
        
        if ($listaProfissionais2->count() > 0) {
            foreach ($listaProfissionais2 as $profissional) {
                if (in_array($profissional->profissional, $profissionaisSelecionados)) {
                    continue;
                }
                $dados = new \StdClass();
                $dados->codigo = $profissional->codigo;
                $dados->profissional = $profissional->profissional;
                $listagemProfissionais[] = $dados;
                $profissionaisSelecionados[] = $profissional->profissional;
            }
        }
        return $listagemProfissionais;
    }
}
