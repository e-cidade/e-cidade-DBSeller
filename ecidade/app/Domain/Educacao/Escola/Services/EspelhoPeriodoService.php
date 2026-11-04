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

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Models\Diario;
use App\Domain\Educacao\Escola\Repositories\AproveitamentoAvaliacaoParcialRepository;
use App\Domain\Educacao\Escola\Repositories\AvaliacaoParcialRepository;
use Etapa;
use Exception;
use Periodo;
use PeriodoAvaliacao;
use Turma;

class EspelhoPeriodoService
{
    /**
     * @var Turma
     */
    private $turma;

    /**
     * @var Etapa
     */
    private $etapa;

    private $periodo;

    /**
     * @var boolean
     */
    private $somenteAlunosAtivos = false;

    /**
     * @param Turma $turma
     * @param Etapa $etapa
     * @param $periodo
     */
    public function __construct(Turma $turma, Etapa $etapa, $periodo)
    {
        $this->turma = $turma;
        $this->etapa = $etapa;
        $this->periodo = $periodo;
    }

    /**
     * @param $somenteAlunosAtivos
     * @return $this
     */
    public function setSomenteAlunosAtivos($somenteAlunosAtivos)
    {
        $this->somenteAlunosAtivos = $somenteAlunosAtivos;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getDados($regenciasFiltrar = [])
    {
        $dadosRelatorio = (object)[
            "turma" => $this->turma,
            "etapa" => $this->etapa,
            "periodo" => $this->periodo,
            "disciplinas" => [],
            "alunos" => []
        ];

        $regencias = $this->turma->getDisciplinasPorEtapa($this->etapa);
        if (count($regenciasFiltrar) > 0) {
            $regencias = array_filter($regencias, function ($regencia) use ($regenciasFiltrar) {
                return in_array($regencia->getCodigo(), $regenciasFiltrar);
            });
        }

        $matriculas = $this->turma->getAlunosMatriculadosNaTurmaPorSerie($this->etapa);

        if ($this->somenteAlunosAtivos) {
            foreach ($matriculas as $key => $matricula) {
                if ($matricula->getSituacao() != 'MATRICULADO') {
                    unset($matriculas[$key]);
                }
            }
        }

        foreach ($matriculas as $matricula) {
            $dadosRelatorio->alunos[$matricula->getCodigo()] = (object)[
                "matricula" => $matricula,
                "aproveitamentos" => [],
                "resultados" => []
            ];
        }

        foreach ($regencias as $regencia) {
            $periodoAvaliacao = new PeriodoAvaliacao($this->periodo->ed09_i_codigo);

            $avaliacoesParciaisService = new AvaliacoesParciaisService();
            $procedimentoResultadoParcial = $avaliacoesParciaisService
                ->getResultadoRegenciaPeriodo($regencia, $periodoAvaliacao);

            $disciplina = (object)[
                "regencia" => $regencia,
                "avaliacoes" => [],
                "resultadoParcial" => $procedimentoResultadoParcial,
                "recuperacao" => null,
            ];

            $resultadoParcialService = new ResultadoParcialService();

            $avaliacaoParcialRepository = new AvaliacaoParcialRepository();
            $avaliacoes = $avaliacaoParcialRepository->getAvaliacoesByRegenciaPeriodo($regencia, $this->periodo);

            $aproveitamentoAvaliacaoParicalRepository = new AproveitamentoAvaliacaoParcialRepository();

            $disciplina->avaliacoes = $avaliacoes;

            foreach ($avaliacoes as $avaliacao) {
                foreach ($matriculas as $matricula) {
                    // A sessão é necessário para caso o getDiarioDeClasse()
                    // crie um novo diário deve ser comitado na sessão antiga
                    require_once(modification("dbforms/db_funcoes.php"));
                    db_inicio_transacao();
                    $diarioAvaliacaoDisciplina = $matricula->getDiarioDeClasse()
                        ->getDisciplinasPorRegencia($regencia);
                    db_fim_transacao();
                    $codigoDiario = $diarioAvaliacaoDisciplina->getCodigoDiario();
                    $aproveitamento = $aproveitamentoAvaliacaoParicalRepository
                        ->getAproveitamentoAluno($codigoDiario, $avaliacao);
                    $dadosRelatorio->alunos[$matricula->getCodigo()]->aproveitamentos[] = $aproveitamento;

                    $diario = Diario::find($codigoDiario);
                    $resultadoParcialAluno = $resultadoParcialService
                        ->getResultadoParcialAluno($diario, $procedimentoResultadoParcial);
                    $dadosRelatorio->alunos[$matricula->getCodigo()]
                        ->resultados[$resultadoParcialAluno->ed343_codigo] = $resultadoParcialAluno;
                }
            }

            $dadosRelatorio->disciplinas[] = $disciplina;
        }

        return $dadosRelatorio;
    }
}
