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

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\ProcedimentoResultadoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\ResultadoParcial;
use App\Domain\Educacao\Escola\Models\Diario;
use App\Domain\Educacao\Escola\Strategies\ResultadoParcial\ResultadoParcialStrategy;
use ArredondamentoNota;
use AvaliacaoAproveitamento;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Matricula;
use ValorAproveitamentoNota;

class ResultadoParcialService
{
    /**
     * @var ResultadoParcialStrategy
     */
    private $resultadoParcialStrategy;

    /**
     * @var ResultadoParcialStrategy
     */
    private $resultadoPeriodoStrategy;

    public function setResultadoParcialStrategy(ResultadoParcialStrategy $resultadoParcialStrategy)
    {
        $this->resultadoParcialStrategy = $resultadoParcialStrategy;
    }

    public function setResultadoPeriodoStrategy(ResultadoParcialStrategy $resultadoPeriodoStrategy)
    {
        $this->resultadoPeriodoStrategy = $resultadoPeriodoStrategy;
    }

    /**
     * @param AproveitamentoAvaliacaoParcial[] $aproveitamentosAluno
     */
    public function calcularResultadoParcial(array $aproveitamentosAluno, $resultadoParcialCalculado)
    {
        $resultado = $this->resultadoParcialStrategy->calcular($aproveitamentosAluno);
        $resultadoParcialCalculado->ed343_valornota = $resultado;
        return $resultadoParcialCalculado;
    }

    /**
     * @param ResultadoParcialInterface $resultadoParcial
     * @return float|null
     */
    public function calcularResultadoPeriodo(ResultadoParcialInterface $resultadoParcial)
    {
        $resultado = $this->resultadoPeriodoStrategy->calcularResultadoPeriodo($resultadoParcial);
        if (!is_null($resultado)) {
            $ano = $resultadoParcial->diario->calendario->getAno();
            return ArredondamentoNota::formatar($resultado, $ano);
        }
        return null;
    }

    /**
     * @param Diario $diario
     * @param ProcedimentoResultadoParcial $procedimentoResultadoParcial
     * @return ResultadoParcial|Builder|Model
     */
    public function getResultadoParcialAluno(
        Diario $diario,
        ProcedimentoResultadoParcial $procedimentoResultadoParcial
    ) {
        $resultadoParcial = ResultadoParcial::query()
            ->where('ed343_diario', $diario->ed95_i_codigo)
            ->where('ed343_procresultadoparcial', $procedimentoResultadoParcial->ed342_codigo)
            ->first();

        if (!is_null($resultadoParcial)) {
            return $resultadoParcial;
        }

        return ResultadoParcial::create([
            'ed343_diario' => $diario->ed95_i_codigo,
            'ed343_procresultadoparcial' => $procedimentoResultadoParcial->ed342_codigo,
            'ed343_valornota' => null,
            'ed343_valornivel' => '',
            'ed343_recuperacaonota' => null,
            'ed343_recuperacaonivel' => ''
        ]);
    }

    public function alunoAtingiuMinimo(ResultadoParcial $resultado, $nota)
    {
        $formaAvaliacao = $resultado->procedimentoResultadoParcial->procedimentoAvaliacao->formaAvaliacao;
        if (is_null($nota)) {
            return false;
        }
        return (float)$nota >= (float)$formaAvaliacao->ed37_c_minimoaprov;
    }

    /**
     * @param ResultadoParcial $resultado
     * @param Matricula $matricula
     * @return AvaliacaoAproveitamento
     * @throws Exception
     */
    public function getAproveitamentoDiario(ResultadoParcial $resultado, Matricula $matricula)
    {
        $regencia = \RegenciaRepository::getRegenciaByCodigo($resultado->diario->ed95_i_regencia);
        $periodoAvaliacao = $resultado->procedimentoResultadoParcial->procedimentoAvaliacao->periodoAvaliacao;
        $disciplina = $matricula->getDiarioDeClasse()->getDisciplinasPorRegencia($regencia);

        return $disciplina->getAvaliacoesPorOrdemSequencial($periodoAvaliacao->ed09_i_sequencia);
    }

    /**
     * @param ResultadoParcial $resultado
     * @param Matricula $matricula
     * @param $notaFinalPeriodo
     * @return void
     * @throws Exception
     */
    public function encerrarPeriodoAluno(ResultadoParcial $resultado, Matricula $matricula, $notaFinalPeriodo)
    {
        $regencia = \RegenciaRepository::getRegenciaByCodigo($resultado->diario->ed95_i_regencia);
        $periodoAvaliacao = $resultado->procedimentoResultadoParcial->procedimentoAvaliacao->periodoAvaliacao;
        $disciplina = $matricula->getDiarioDeClasse()->getDisciplinasPorRegencia($regencia);
        $aproveitamentoDiario = $disciplina->getAvaliacoesPorOrdemSequencial($periodoAvaliacao->ed09_i_sequencia);

        $valorAproveitamento = new ValorAproveitamentoNota($notaFinalPeriodo);
        $valorAproveitamento->setAproveitamentoReal($notaFinalPeriodo);

        if (!is_null($aproveitamentoDiario)) {
            $aproveitamentoDiario->setValorAproveitamento($valorAproveitamento);
            $aproveitamentoDiario->setAproveitamentoMinimo($this->alunoAtingiuMinimo($resultado, $notaFinalPeriodo));
        } else {
            throw new Exception("Não foi possível salvar a Nota de Aproveitamento da turma");
        }

        $disciplina->salvar();

        $resultado->ed343_encerrado = true;
        $resultado->save();
    }

    public function reabrirPeriodoAluno(ResultadoParcial $resultado, Matricula $matricula)
    {
        $resultado->ed343_encerrado = false;
        $resultado->save();
    }
}
