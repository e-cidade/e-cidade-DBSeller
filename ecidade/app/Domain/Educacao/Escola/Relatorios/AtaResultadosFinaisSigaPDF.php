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

namespace App\Domain\Educacao\Escola\Relatorios;

use ECidade\Pdf\Pdf;
use Etapa;
use Exception;
use InstituicaoRepository;
use Matricula;
use Turma;

class AtaResultadosFinaisSigaPDF extends Pdf
{
    private $dadosRelatorios = [];

    public function __construct($dados)
    {
        parent::__construct();
        $this->dadosRelatorios = $dados;
    }

    private function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setMargins(8, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFillColor(235);
        $this->setFont('Arial', 'B', 9);
        $this->setExibeBrasao(true);
        $this->exibeHeader(true, \Fpdf\Pdf::HEADER_ESCOLA);
    }

    protected function imprimir()
    {
        $fileName = 'tmp/ata_siga_' . time() . '.pdf';
        $this->output('F', $fileName);
        return [
            "name" => "Relatório Ata de Resultados Finais - SIGA",
            "path" => $fileName,
            "file" => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function emitirPdf()
    {
        $this->initPdf();

        foreach ($this->dadosRelatorios as $dados) {
            /** @var Turma $turma */
            $turma = $dados->turma;
            /** @var Etapa $etapa */
            $etapa = $dados->etapa;

            $this->addTitulo('ATA DE RESULTADOS FINAIS - SIGA', 'titulo');
            $this->addTitulo('', 'subtitulo');
            $this->addTitulo("Turma: {$turma->getDescricao()}", 'turma');
            $this->addTitulo("Etapa: {$etapa->getNome()}", 'etapa');
            $anoLetivo = $turma->getCalendario()->getAnoExecucao();
            $this->addTitulo("Ano Letivo: {$anoLetivo}", 'anoLetivo');
            $this->addTitulo("Curso: {$turma->getBaseCurricular()->getCurso()->getNome()}", 'curso');
            $this->addTitulo("Carga Horária: {$turma->getCargaHoraria($etapa)}", "cargaHoraria");
            $this->addPage();

            $this->ln();
            $this->setFont('Arial', 'B', 9);
            $this->cell(0, 10, 'ATA DE RESULTADOS FINAIS - SIGA', 0, 1, 'C');

            $this->setFont('Arial', '', 8);
            $calendario = $turma->getCalendario();
            $dataFinal = $calendario->getDataFinal();
            $dia = $dataFinal->getDia();
            $mes = \DBDate::getMesExtenso($dataFinal->getMes());
            $ano = $dataFinal->getAno();
            $oPrefeitura = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));

            $descricao = "Aos {$dia} dias do mês de {$mes} de {$ano}, conclui-se a apuração final ";
            $descricao .= " do rendimento escolar, nos termos da Lei 9.394/96 ";
            $descricao .= " e do Regimento Escolar da Rede Municipal de Educação de  {$oPrefeitura->getMunicipio()}, ";
            $descricao .= " da turma {$turma->getDescricao()} (Projeto de Correção de Fluxo). ";

            $this->multiCell(0, 5, $descricao, 0, 'L');

            $this->cabecalho($anoLetivo);
            $this->imprimirAlunos($dados->alunos);
            $this->imprimirAssinaturas();
        }

        return $this->imprimir();
    }

    private function cabecalho($anoCalendario)
    {
        $this->setFont('Arial', 'b', 6);
        $this->ln();
        $this->cell(10, 12, 'Nº', 1, 0, 'C');
        $this->cell(90, 12, 'NOME DO ESTUDANTE', 1, 0, 'C');
        $this->cell(30, 12, 'DATA DE NASCIMENTO', 1, 0, 'C');
        $this->setFont('Arial', 'b', 5);
        $this->cell(30, 12, "RESULTADO FINAL EM {$anoCalendario}", 1, 0, 'C');
        $anoPosterior = $anoCalendario + 1;
        $this->multiCell(35, 4, "ANO ESCOLAR DO ENSINO FUNDAMENTAL QUE DEVERÁ CURSAR EM {$anoPosterior}", 1, 'C');
    }

    /**
     * @param $alunos
     * @return void
     */
    private function imprimirAlunos($alunos)
    {
        $this->setFont('Arial', '', 6);
        foreach ($alunos as $aluno) {
            if ($this->getAvailableHeight() < 60) {
                $this->addPage();
            }
            /** @var Matricula $matricula */
            $matricula = $aluno->matricula;
            $resultadoFinal = $matricula->isConcluida() ? $aluno->resultadoFinal : '-';
            $etapaDestino = $matricula->isConcluida() ? $aluno->etapaDestino : '-';
            $this->cell(10, 4, $matricula->getNumeroOrdemAluno(), 1, 0, 'C');
            $this->cell(90, 4, $matricula->getAluno()->getNome(), 1, 0, 'L');
            $dataNascimento = \DBDate::converter($matricula->getAluno()->getDataNascimento());
            $this->cell(30, 4, $dataNascimento, 1, 0, 'C');
            $this->cell(30, 4, $resultadoFinal, 1, 0, 'C');
            $this->cell(35, 4, $etapaDestino, 1, 1, 'C');
        }

        while ($this->getAvailableHeight() > 60) {
            $this->cell(10, 4, '', 1, 0, 'C');
            $this->cell(90, 4, '', 1, 0, 'L');
            $this->cell(30, 4, '', 1, 0, 'C');
            $this->cell(30, 4, '', 1, 0, 'C');
            $this->cell(35, 4, '', 1, 1, 'C');
        }
    }

    /**
     * @return void
     */
    private function imprimirAssinaturas()
    {
        $this->ln();
        $this->ln();
        $this->ln();
        $this->setFont('Arial', '', 8);
        $this->cell(60, 5, "_______________________________", 0, 0, 'C');
        $this->cell(70, 5, "_______________________________________", 0, 0, 'C');
        $this->cell(65, 5, "_______________________________", 0, 1, 'C');
        $this->setFont('Arial', 'b', 6);
        $this->cell(60, 5, "Assinatura do Professor", 0, 0, 'C');
        $this->cell(70, 5, "Assinatura do Responsável/Orientador Pedagógico", 0, 0, 'C');
        $this->cell(65, 5, "Assinatura e carimbo do Diretor", 0, 1, 'C');
    }
}
