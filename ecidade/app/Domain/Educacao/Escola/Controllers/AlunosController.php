<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\Matricula;
use App\Domain\Educacao\Escola\Models\Turma;
use App\Domain\Educacao\Escola\Models\TurmaEspecialMatricula;
use App\Domain\Educacao\Escola\Services\AlunoService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TurmaRepository;
use Exception;
use db_utils;
use stdClass;

class AlunosController extends Controller
{
    public function getHistoricosAlunosByEscola(Request $request)
    {
        $escola = $request->get('escola');
        $ano = $request->get('ano');
        $curso = $request->get('curso');
        $whereCurso = is_null($curso) ? "1 = 1" :"ed61_i_curso = {$curso}";
        $sqlEsola = $request->get('tipoVinculo') == 1
            ? "(historicomps.ed62_i_escola = {$escola} or alunocurso.ed56_i_escola = {$escola})"
            : "ed56_i_escola is null";

        $sqlAno = is_null($ano) ? "1 = 1" :
            "(SELECT  ed62_i_anoref AS ano
                FROM historico
            LEFT JOIN historicomps
                ON historicomps.ed62_i_historico = historico.ed61_i_codigo
            LEFT JOIN historicompsfora
                ON historicompsfora.ed99_i_historico = ed61_i_codigo
            WHERE ed61_i_aluno = ed47_i_codigo
            AND ed62_i_anoref IS NOT NULL
                UNION
            SELECT  ed99_i_anoref AS anoHis
                FROM historico
            LEFT JOIN historicomps
                ON historicomps.ed62_i_historico = historico.ed61_i_codigo
            LEFT JOIN historicompsfora
                ON historicompsfora.ed99_i_historico = ed61_i_codigo
            WHERE ed61_i_aluno = ed47_i_codigo
            AND ed99_i_anoref IS NOT NULL
            ORDER BY 1 DESC
            LIMIT 1) = {$ano}";

            $alunosHistoricos = DB::table('historico')
                ->selectRaw('distinct ed47_i_codigo, trim(aluno.ed47_v_nome) AS ed47_v_nome, ed56_i_escola')
                ->join('aluno', 'aluno.ed47_i_codigo', '=', 'historico.ed61_i_aluno')
                ->leftJoin('alunocurso', 'alunocurso.ed56_i_aluno', '=', 'aluno.ed47_i_codigo')
                ->leftJoin('calendario', 'calendario.ed52_i_codigo', '=', 'alunocurso.ed56_i_calendario')
                ->leftJoin('historicomps', 'historicomps.ed62_i_historico', '=', 'historico.ed61_i_codigo')
                ->leftJoin('historicompsfora', 'historicompsfora.ed99_i_historico', '=', 'historico.ed61_i_codigo')
                ->whereRaw($sqlEsola)
                ->whereRaw($sqlAno)
                ->whereRaw($whereCurso)
                ->orderBy('ed47_v_nome')->get();

        return new DBJsonResponse($alunosHistoricos);
    }

    public function getHistoricosAlunosTransferidosFora(Request $request)
    {
        $escola = $request->get('escola');
        $ano = $request->get('ano');
        $curso = $request->get('curso');
        $whereAno = is_null($ano) ? "1 = 1" : "ed52_i_ano = {$ano}";
        $whereCurso = is_null($curso) ? "1 = 1" :
            "exists(select 1
                from base
            where base.ed31_i_codigo = turma.ed57_i_base
                and base.ed31_i_curso = {$curso})";


        $alunosHistoricos = DB::table('transfescolarede')
            ->selectRaw('distinct ed47_i_codigo, trim(aluno.ed47_v_nome) AS ed47_v_nome, escola.ed18_i_codigo')
            ->join('escola', 'escola.ed18_i_codigo', '=', 'transfescolarede.ed103_i_escolaorigem')
            ->join('matricula', 'matricula.ed60_i_codigo', '=', 'transfescolarede.ed103_i_matricula')
            ->join('turma', 'turma.ed57_i_codigo', '=', 'matricula.ed60_i_turma')
            ->join('matriculaserie', 'matriculaserie.ed221_i_matricula', '=', 'matricula.ed60_i_codigo')
            ->join('atestvaga', 'atestvaga.ed102_i_codigo', '=', 'transfescolarede.ed103_i_atestvaga')
            ->join('aluno', 'aluno.ed47_i_codigo', '=', 'matricula.ed60_i_aluno')
            ->join('historico', 'historico.ed61_i_aluno', '=', 'aluno.ed47_i_codigo')
            ->join('calendario', 'calendario.ed52_i_codigo', '=', 'atestvaga.ed102_i_calendario')
            ->join('base', 'base.ed31_i_codigo', '=', 'atestvaga.ed102_i_base')
            ->whereRaw("matricula.ed60_c_situacao = 'TRANSFERIDO REDE'")
            ->whereRaw("transfescolarede.ed103_i_escolaorigem = {$escola}")
            ->whereRaw($whereAno)
            ->whereRaw($whereCurso)
            ->orderBy('ed47_v_nome')->get();
        return new DBJsonResponse($alunosHistoricos);
    }

    public function getAlunosPorTurma($turma)
    {
        $alunos = Matricula::join('aluno', 'ed47_i_codigo', 'ed60_i_aluno')
            ->join('turma', 'ed57_i_codigo', 'ed60_i_turma')
            ->join('matriculaserie', 'ed221_i_matricula', 'ed60_i_codigo')
            ->where('ed57_i_codigo', $turma)
            ->where('ed221_c_origem', 'S')
            ->orderBy('ed47_v_nome')->get()->map(function ($aluno) {
                return (object) [
                    'nome' => trim($aluno->ed47_v_nome),
                    'codigo' => $aluno->ed47_i_codigo,
                    'matricula' => $aluno->ed60_i_codigo,
                    'situacao_matricula' => trim($aluno->ed60_c_situacao),
                    'email_responsavel' => trim($aluno->ed47_c_emailresp),
                    'telefone_responsavel' => $aluno->ed47_celularresponsavel,
                    'codigo_etapa' => $aluno->ed221_i_serie
                ];
            });
        return new DBJsonResponse($alunos);
    }

    public function getAlunosPorTurmaEspecial($turma)
    {
        $alunos = TurmaEspecialMatricula::join('aluno', 'ed47_i_codigo', 'ed269_aluno')
            ->join('turmaac', 'ed268_i_codigo', 'ed269_i_turmaac')
            ->where('ed268_i_codigo', $turma)->orderBy('ed47_v_nome')->get()->map(function ($aluno) {
                return (object) [
                    'nome' => $aluno->ed47_v_nome,
                    'codigo' => $aluno->ed47_i_codigo,
                    'matricula' => $aluno->ed60_i_codigo
                ];
            });

        return new DBJsonResponse($alunos);
    }

    public function getAluno($aluno)
    {
        $aluno = Aluno::find($aluno);
        $aluno->cursos = $aluno->historicos->map(function ($historico) {
            return $historico->curso;
        });

        return new DBJsonResponse($aluno);
    }

    public function getDetalhesAlunosEspecial($aluno)
    {
        try {
            $alunoEspecial = DB::table('aluno')
            ->selectRaw("ed47_d_nasc as datanascimento, ed11_c_descr as etapa,
                        exists(select 1 from escola.necessidadealunocadeirante where
                        ed189_aluno={$aluno}) as cadeirante ")
            ->join('matricula', 'matricula.ed60_i_aluno', '=', 'aluno.ed47_i_codigo')
            ->join('matriculaserie', 'matriculaserie.ed221_i_matricula', '=', 'matricula.ed60_i_codigo')
            ->join('serie', 'serie.ed11_i_codigo', '=', 'matriculaserie.ed221_i_serie')
            ->whereRaw("aluno.ed47_i_codigo = {$aluno}")
            ->orderBy('matricula.ed60_d_datamatricula', 'desc')
            ->limit(1)
            ->get()->map(function ($aluno) {
                return (object) [
                    'dataNascimento' => $aluno->datanascimento,
                    'etapa' => $aluno->etapa,
                    'cadeirante' => $aluno->cadeirante
                ];
            });

            return new DBJsonResponse($alunoEspecial);
        } catch (Exception $exception) {
            db_query("rollback");
            throw new Exception("Falha ao retornar Dados de aluno especial " . $exception);
        }
    }

    public function getDeficienciasByAluno($aluno)
    {
        $result = [];
        $necessidadeAlunos = [];

        $aluno = Aluno::where('ed47_i_codigo', $aluno)
            ->with([
                'necessidadeSubdivisaoAluno.necessidadeSubdivisao.necessidade',
                'alunoNecessidade.necessidade'
            ])
            ->first();

        /**
         * Pesquisa e coleta dentro do dado Aluno, se alguma "deficiencia" possui
         * uma subvisão cadastrada no Banco de Dados
         */
        if ($aluno and $aluno->necessidadeSubdivisaoAluno->count() > 0) {
            $aluno->necessidadeSubdivisaoAluno->each(
                function ($necessidadeSubdivisaoAluno) use (&$result) {
                    if ($necessidadeSubdivisaoAluno->necessidadeSubdivisao->count() > 0) {
                        $necessidadeSubdivisaoAluno->necessidadeSubdivisao->each(
                            function ($necessidadeSubdivisao) use (&$result) {
                                if ($necessidadeSubdivisao->necessidade) {
                                    $result[] = (object) [
                                        'codigo'      => $necessidadeSubdivisao->necessidade->ed48_i_codigo,
                                        'deficiencia' => $necessidadeSubdivisao->necessidade->ed48_c_descr,
                                        'subdivisao'  => $necessidadeSubdivisao->ed185_descricao
                                    ];
                                }
                            }
                        );
                    }
                }
            );
        }

        /**
         * Pesquisa e coleta dentro do dano Aluno as "deficiencia" que não possuem
         * uma subdivisão cadastrada
         */
        if ($aluno and $aluno->alunoNecessidade->count()) {
            $aluno->alunoNecessidade->each(function ($alunoNecessidade) use (&$necessidadeAlunos) {
                if ($alunoNecessidade->necessidade) {
                    $necessidadeAlunos[] = (object) [
                        'codigo'      => $alunoNecessidade->necessidade->ed48_i_codigo,
                        'deficiencia' => $alunoNecessidade->necessidade->ed48_c_descr,
                        'subdivisao'  => ''
                    ];
                }
            });
        }

        /**
         * Retira dos dados coletados as informações que tem o mesmo código
         * Podendo assim tirar as duplicatas do array
         */
        foreach ($necessidadeAlunos as $necessidade) {
            $found = false;

            foreach ($result as $subnecessidade) {
                if ($necessidade->codigo == $subnecessidade->codigo) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $result[] = $necessidade;
            }
        }

        /**
         * Retorna o array_map com os resultados
         */
        $result = array_map(function ($necessidade) {
            return (object) [
                'deficiencia' => $necessidade->deficiencia,
                'subdivisao'  => $necessidade->subdivisao
            ];
        }, $result);

        return new DBJsonResponse($result);
    }

    /**
     * @throws Exception
     */
    public function atualizaContatoResponsavel(Request $request, AlunoService $alunoService)
    {
        $dados = new stdClass();
        $dados->id = $request->get('id');
        $dados->telefone_responsavel = preg_replace('/\D/', '', $request->get('telefone_responsavel'));
        $dados->email_responsavel = $request->get('email_responsavel');

        try {
            $alunoService->atualizaContatoResponsavel($dados);
            return new DBJsonResponse('', 'Contato salvo com sucesso!');
        } catch (Exception $e) {
            throw new Exception('N?o foi poss?vel atualizar o contato do respons?vel!' . ' ' . $e->getMessage());
        }
    }

    public function buscarAlunosMatriculadosPorEscola($escola)
    {
        $alunos = [];
        $turmas =
            Turma::join('calendario', 'ed52_i_codigo', '=', 'ed57_i_calendario')
                ->where('ed57_i_escola', '=', $escola)
                ->where('ed52_i_ano', '=', Carbon::now()->year)
                ->get();

        foreach ($turmas as $turma) {
            $matriculados =
                Matricula::join('aluno', 'ed47_i_codigo', '=', 'ed60_i_aluno')
                    ->where('ed60_i_turma', '=', $turma->ed57_i_codigo)
                    ->where('ed60_c_ativa', '=', 'S')
                    ->where('ed60_c_situacao', '=', 'MATRICULADO')
                    ->where('ed60_c_concluida', '=', 'N')
                    ->select(['ed47_i_codigo', 'ed47_v_nome'])
                    ->get();

            foreach ($matriculados as $matricula) {
                $alunos[] = (object)[
                    'codigo' => $matricula->ed47_i_codigo,
                    'nome' => trim($matricula->ed47_v_nome)
                ];
            }
        }
        usort($alunos, function ($a, $b) {
            return strcmp($a->nome, $b->nome);
        });

        return new DBJsonResponse($alunos);
    }
}
