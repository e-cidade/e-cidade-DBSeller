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

namespace ECidade\Educacao\Escola\Relatorios\DiarioClasse;

use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\DadosDiarioClasse;
use Etapa;
use Exception;

/**
 * Class DiarioClasseTurmaEscolarizacaoPdf
 * @package ECidade\Educacao\Escola\Relatorios\DiarioClasse
 */
class DiarioClasseTurmaEscolarizacaoPdf extends DiarioClassePdf
{
    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @throws Exception
     */
    protected function cabecalho(DadosDiarioClasse $dadosDiarioClasse, $request = null)
    {

        $this->cabecalhoDadosInstituicao($dadosDiarioClasse);
        $calendario = $dadosDiarioClasse->getCalendario();
        $turma = $dadosDiarioClasse->getTurma();

        $posicaoX = 105;
        $tamanhoLinha = 100;
        $tamanhoLinhaBorda = 85;

        $this->SetXY($posicaoX, 8);

        $this->Cell($tamanhoLinha, 4, "Curso: {$turma->getCurso()->getNome()}", 0, 0, "L");
        $this->Cell($tamanhoLinhaBorda, 4, "Calendário: {$calendario->getDescricao()}", 0, 1, "L");

        $this->SetXY($posicaoX, 12);
        $this->Cell($tamanhoLinha, 4, "Turma: {$turma->getNome()}", 0, 0, "L");
        if (strlen($this->getNomeEtapas($turma->getEtapas())) > 40) {
            $this->SetFont('Arial', '', '6');
        }
        $this->Cell($tamanhoLinhaBorda, 4, "Etapas: {$this->getNomeEtapas($turma->getEtapas())}", 0, 1, "L");
        $this->SetFont('Arial', '', '7');
        $this->SetXY($posicaoX, 16);
        $periodo = $dadosDiarioClasse->getAvaliacaoPeriodica()->getPeriodoAvaliacao()->getDescricao();
        $this->Cell($tamanhoLinha, 4, "Período: {$periodo}", 0, 0, "L");

        $exibirAulasDadas = $this->exibirAulasDadas;

        if ($exibirAulasDadas) {
            $aulasDadas = $dadosDiarioClasse->getAulasDadas();
            $textoAulasDadas = $aulasDadas == 0 ? "0" : $aulasDadas;

            // Aqui eu verifico se a frequência é manual
            if ($request->input('registroManual') == '1') {
                $textoAulasDadas = "____";
            }
            
            $this->Cell($tamanhoLinhaBorda, 4, "Aulas Dadas: $textoAulasDadas", 0, 1, "L");
        }
        $this->SetXY($posicaoX, 20);
        $disciplina = $dadosDiarioClasse->getDisciplina()->getDisciplina();

        if ($this->exibirTodasDisciplinas) {
            $this->Cell($tamanhoLinha, 4, "Disciplinas: TODAS", 0, 0, "L");
        } elseif ($this->pautaUnica) {
            $this->Cell($tamanhoLinha, 4, "Disciplina: PAUTA ÚNICA", 0, 0, "L");
        } else {
            $this->Cell($tamanhoLinha, 4, "Disciplina: {$disciplina->getDescricaoCompleta()}", 0, 0, "L");
        }

        $this->Cell($tamanhoLinhaBorda, 4, "Turno: {$dadosDiarioClasse->getTurno()->getDescricao()}", 0, 1, "L");
        $this->SetXY($posicaoX, 24);
        if (!$this->pautaUnica) {
            $nome_regente = mb_strimwidth($dadosDiarioClasse->getNomeRegente(), 0, 115);
            $this->Cell($tamanhoLinha, 4, "Regente: {$nome_regente}", 0, 1, "L");
        }

        $this->roundedrect(8, 8, 280, 20, 2, '', '1234');
        $this->SetXY(8, 30);
    }

    /**
     * @param Etapa[] $etapas
     * @return string
     */
    private function getNomeEtapas(array $etapas)
    {
        $nome = [];
        foreach ($etapas as $etapa) {
            $nome[] = $etapa->getNome();
        }
        return implode(' / ', $nome);
    }
}
