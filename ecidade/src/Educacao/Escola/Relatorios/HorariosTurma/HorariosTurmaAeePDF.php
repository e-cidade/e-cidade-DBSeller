<?php


namespace ECidade\Educacao\Escola\Relatorios\HorariosTurma;

use FpdfMultiCellBorder;
use stdClass;
use DBDate;
use db_utils;
use ECidade\Educacao\Escola\Service\NotificacaoTransferenciaService;
use ECidade\Pdf\Pdf;
use Escola;
use funcao;

class HorariosTurmaAeePDF
{

    private $dados;
    private $pdf;
    public function __construct($dados)
    {
        $this->setDados($dados);
        $this->pdf = new Pdf();
        $this->pdf->addTitulo('Quadro de Horários Turmas AEE', 0);
        $this->pdf->addTitulo('Turma: ' . $this->dados['turma'], 1);
    }

    public function emitir()
    {
        $this->pdf->exibeHeader(true, 1);
        $this->pdf->mostrarEmissor();
        $this->impimir();
        $fileName = 'tmp/horario_turma_aee' . time() . '.pdf';
        $this->pdf->output('F', $fileName);
        return ECIDADE_REQUEST_PATH . $fileName;
    }

    public function header()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFillColor(205);
        $this->pdf->SetFont('Arial', 'B', 8);
        
        $this->pdf->Cell(25, 5, 'DIA', 1, 0, 'C', 1);
        $this->pdf->Cell(35, 5, 'HORÁRIO', 1, 0, 'C', 1);
        $this->pdf->Cell(55, 5, 'ATIVIDADE', 1, 0, 'C', 1);
        $this->pdf->Cell(77, 5, 'PROFISSIONAL', 1, 1, 'C', 1);
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    public function impimir()
    {
        $fill = true;
        $this->header();
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetFillColor(235);

        foreach ($this->dados['dados'] as $dados) {
            $fill = !$fill;
            $nomeProfissional = substr($dados->profissional, 0, 45);
            $this->pdf->Cell(25, 5, $dados->dia, 1, 0, 'C', $fill);
            $this->pdf->Cell(35, 5, $dados->horario, 1, 0, 'C', $fill);
            $this->pdf->Cell(55, 5, $dados->atividade, 1, 0, 'C', $fill);
            $this->pdf->Cell(77, 5, $nomeProfissional, 1, 1, 'C', $fill);
        }
    }
}
