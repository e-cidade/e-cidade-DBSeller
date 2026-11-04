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

use Aluno;
use App\Domain\Educacao\Escola\Models\CandidatoTransposicao;
use App\Domain\Educacao\Escola\Models\CriterioOrdenacaoTransposicao;
use App\Domain\Educacao\Escola\Models\CriterioTransposicao;
use App\Domain\Educacao\Escola\Models\DesignadoTransposicao;
use App\Domain\Educacao\Escola\Models\Transposicao;
use ECidade\Educacao\Escola\Repository\AlunoRepository;
use MatriculaRepository;

class TransposicaoService
{
    public function getCriteriosOrdenacao($ano, $etapa)
    {
        $criterios = CriterioTransposicao::all();
        $criteriosOrdenacao = [];
        $ordemAnterior = 0;
        foreach ($criterios as $criterio) {
            $criterioOrdenacao = CriterioOrdenacaoTransposicao::where('ed352_ano', $ano)
                ->where('ed352_etapa', $etapa)
                ->where('ed352_criterio', $criterio->ed351_codigo)
                ->first();

            if (is_null($criterioOrdenacao)) {
                $criterioOrdenacao = new CriterioOrdenacaoTransposicao();
                $criterioOrdenacao->ed352_ano = $ano;
                $criterioOrdenacao->ed352_etapa = $etapa;
                $criterioOrdenacao->criterio()->associate($criterio);
                $criterioOrdenacao->ed352_ordem = $ordemAnterior + 1;
                $criterioOrdenacao->ed352_peso = 1;
                $criterioOrdenacao->save();
            }
            $criterioOrdenacao->load('criterio');
            $criteriosOrdenacao[$criterioOrdenacao->ed352_ordem] = $criterioOrdenacao->toArray();

            $ordemAnterior = $criterioOrdenacao->ed352_ordem;
        }
        ksort($criteriosOrdenacao);
        return array_values($criteriosOrdenacao);
    }

    public function salvarCriteriosOrdenacao($criteriosSalvar)
    {
        $ordem = 0;
        $criteriosOrdenacao = [];
        foreach ($criteriosSalvar as $criterioSalvar) {
            $criterioOrdenacao = CriterioOrdenacaoTransposicao::find($criterioSalvar->ed352_codigo);
            $criterioOrdenacao->ed352_ordem = $ordem += 1;
            $criterioOrdenacao->save();
            $criterioOrdenacao->load('criterio');
            $criteriosOrdenacao[$criterioOrdenacao->ed352_ordem] = $criterioOrdenacao;
        }
        ksort($criteriosOrdenacao);
        return array_values($criteriosOrdenacao);
    }

    /**
     * @throws Exception
     * @throws \DBException
     */
    public function buscarInscritos($anoTransposicao)
    {
        $candidatosTransposicao = CandidatoTransposicao::with(
            'etapaDestino',
            'escola1',
            'escola2',
            'escola3',
            'escolaOrigem'
        )->ano($anoTransposicao)->get();

        $transposicao = Transposicao::ano($anoTransposicao)->first();
        $inscritos = [];
        $etapas = [];
        foreach ($candidatosTransposicao as $candidatoTransposicao) {
            $opcoes = TransposicaoService::organizarOpcoes($candidatoTransposicao);
            $etapa = \EtapaRepository::getEtapaByCodigo($candidatoTransposicao->ed349_etapa);
            foreach ($opcoes as $opcao) {
                if (empty($opcao->escola)) {
                    continue;
                }
                $matricula =
                    MatriculaRepository::getMatriculaAtivaPorAluno(new Aluno($candidatoTransposicao->ed349_aluno));
                if (is_null($matricula)) {
                    continue;
                }
                $aluno = AlunoRepository::find($candidatoTransposicao->ed349_aluno);

                $inscricao = (object)[
                    "escola_codigo" => $opcao->escola->getCodigo(),
                    "escola_nome" => $opcao->escola->getNome(),
                    "etapa_codigo" => $etapa->getCodigo(),
                    "etapa" => $etapa->getNome(),
                    "opcao" => $opcao->opcao,
                    "aluno_codigo" => $aluno->getCodigo(),
                    "aluno_nome" => $aluno->getNome(),
                    "aluno_datanascimento" => $aluno->getDataNascimento(),
                    "aluno_necessidadeespecial" => count($aluno->getNecessidadesEspeciais()) > 0,
                    "irmao" => !empty($opcao->irmao),
                    "servidor" => !empty($opcao->servidor),
                    "bolsafamilia" => $aluno->isBolsafamilia(),
                    "designado" => false,
                    "aluno" => $aluno,
                    "candidatoOpcoes" => $candidatoTransposicao,
                ];
                $designado = DesignadoTransposicao::where('ed354_aluno', $inscricao->aluno_codigo)
                    ->where('ed354_transposicao', $transposicao->ed353_codigo)
                    ->where('ed354_opcao', $inscricao->opcao)
                    ->first();

                if (!empty($designado)) {
                    $inscricao->designado = !empty($designado);
                    $inscricao->pontuacao = $designado['ed354_pontuacao'];
                }
                $inscritos[] = $inscricao;
            }
            if (!array_key_exists($etapa->getCodigo(), $etapas)) {
                $etapas[$etapa->getCodigo()] = $etapa;
            }
        }
        $inscritos['etapas'] = $etapas;

        return $inscritos;
    }

    /**
     * @param CandidatoTransposicao $candidatoTransposicao
     * @return array
     */
    public static function organizarOpcoes(CandidatoTransposicao $candidatoTransposicao)
    {
        $opcoes = [];
        for ($i = 1; $i <= 3; $i++) {
            $opcoes[] = (object)[
                'opcao' => $i,
                'escola' => $candidatoTransposicao["escola{$i}"],
                'irmao' => $candidatoTransposicao["irmao{$i}"],
                'servidor' => $candidatoTransposicao["ed349_servidor{$i}"]
            ];
        }
        return $opcoes;
    }
}
