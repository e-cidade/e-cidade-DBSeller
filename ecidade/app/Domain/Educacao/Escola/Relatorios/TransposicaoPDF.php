<?php

namespace App\Domain\Educacao\Escola\Relatorios;

use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\CandidatoTransposicao;
use FpdfMultiCellBorder;

class TransposicaoPDF extends FpdfMultiCellBorder
{
    /**
     * @var CandidatoTransposicao[]
     */
    private $candidatos;

    /**
     * @param CandidatoTransposicao[] $candidatos
     */
    public function __construct($candidatos)
    {
        parent::__construct('P');
        $this->candidatos = $candidatos;
        global $head2;
        global $head4;
        global $head5;
        $head2 = 'CANDIDATOS TRANSPOSIÇÃO';
        $head4 = 'ETAPA: ' . $candidatos[0]->etapaDestino->ed11_c_descr;
    }

    protected function initPdf()
    {
        $this->mostrarRodape(true);
        $this->mostrarTotalDePaginas(true);
        $this->SetMargins(10, 8, 8);
        $this->Open();
        $this->SetAutoPageBreak(true, 10);
        $this->AliasNbPages();
        $this->SetFillColor(235);
        $this->exibeHeader(true);
    }

    protected function imprimir()
    {
        $fileName = 'tmp/transposicao_' . time() . '.pdf';
        $this->Output($fileName, false, true);
        return [
            "name" => "Transposição",
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
        $this->AddPage();
        foreach ($this->candidatos as $candidato) {
            $this->imprimirCandidato($candidato);
        }
        return $this->imprimir();
    }

    private function imprimirCabecalhoCandidato(CandidatoTransposicao $candidato)
    {
        $this->linhaEmBranco();
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 6, 'Código Aluno: ', 'TB', 0, 'L', 1);
        $this->Cell(20, 6, $candidato->aluno->getCodigo(), 'TB', 0, 'L', 1);
        $this->Cell(10, 6, 'Nome: ', 'TB', 0, 'L', 1);
        $this->Cell(142, 6, $candidato->aluno->getNome(), 'TB', 1, 'L', 1);
    }

    private function imprimirCandidato(CandidatoTransposicao $candidato)
    {
        $this->imprimirCabecalhoCandidato($candidato);
        $escola1 = '';
        if (!empty($candidato->escola1)) {
            $escola1 = $candidato->escola1->getNome();
        }
        $escola2 = '';
        if (!empty($candidato->escola2)) {
            $escola2 = $candidato->escola2->getNome();
        }
        $escola3 = '';
        if (!empty($candidato->escola3)) {
            $escola3 = $candidato->escola3->getNome();
        }

        $this->linhaOpcao('Opção 1: ', $escola1, $candidato->irmao1);
        $this->linhaOpcao('Opção 2: ', $escola2, $candidato->irmao2);
        $this->linhaOpcao('Opção 3: ', $escola3, $candidato->irmao3);
    }

    private function linhaEmBranco()
    {
        $this->Cell(189, 4, '', 0, 1, 'L');
    }

    private function linhaOpcao($labelOpcao, $escola, Aluno $irmaoNaEscola = null)
    {
        if (empty($escola)) {
            return;
        }

        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, $labelOpcao, 'B', 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(85, 5, $escola, 'B', 0, 'L', 0);

        $this->SetFont('Arial', 'B', 7);
        $this->Cell(22, 5, 'Irmão na Escola: ', 'B', 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $irmaoTexto = 'NÃO';
        if (!is_null($irmaoNaEscola)) {
            $irmaoTexto = "{$irmaoNaEscola->getCodigo()} - {$irmaoNaEscola->getNome()}";
        }
        $this->Cell(65, 5, $irmaoTexto, 'B', 1, 'L', 0);
    }
}
