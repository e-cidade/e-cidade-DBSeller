<?php

namespace ECidade\Educacao\Escola\Relatorios;

use DBDate;
use FpdfMultiCellBorder;

class VacinasRelatorioPDF extends FpdfMultiCellBorder
{
    /**
     * @var []
     */
    private $dados;

    public function __construct($dados, $tipo)
    {
        parent::__construct();
        $this->dados = $dados;

        global $head2;
        global $head3;
        if ($tipo == 1) {
            $head3 = "Todos profissionais";
        } elseif ($tipo == 2) {
            $head3 = "Apenas profissionais vacinados";
        } else {
            $head3 = "Apenas profissionais não vacinados";
        }
        $head2 = "Relatório de Vacinação";
    }

    protected function initPdf()
    {
        $this->mostrarRodape(true);
        $this->mostrarTotalDePaginas(true);
        $this->SetMargins(8, 8, 8);
        $this->Open();
        $this->SetAutoPageBreak(true, 10);
        $this->AliasNbPages();
        $this->SetFillColor(235);
        $this->SetFont('Arial', 'B', 9);
        $this->exibeHeader(true);
    }

    protected function imprimir()
    {
        $fileName = 'tmp/edu_vacinacao_' . time() . '.pdf';
        $this->Output($fileName, false, true);
        return [
            "name" => "Relatório de Vacinação PDF",
            "path" => $fileName
        ];
    }

    public function emitirPdf()
    {
        $this->initPdf();
        $this->AddPage('L');

        foreach ($this->dados as $escola) {
            $alturaNecessaria = (count(reset($escola->profissionais)) * 4) + 10;
            if ($this->GetAvailHeight() < $alturaNecessaria) {
                $this->AddPage('L');
            }

            $this->SetFont('Arial', 'B', 9);
            $this->Cell(120, 5, "Escola: {$escola->ed18_i_codigo} - {$escola->nome}");
            $this->Cell(75, 5, "Código Referência: {$escola->ed18_codigoreferencia}", 0, 1);

            $this->imprimirCabecalhoProfissionais();

            $linhaImpressa = 0;
            foreach ($escola->profissionais as $profissional) {
                $linhaImpressa++;
                $color = !($linhaImpressa % 2);

                $alturaCGM = count($profissional) * 4;
                if ($this->GetAvailHeight() < $alturaCGM) {
                    $this->AddPage('L');
                    $this->imprimirCabecalhoProfissionais();
                }

                $this->SetFillColor(240);
                $this->SetFont('Arial', '', 7);

                $this->Cell(15, $alturaCGM, $profissional[0]->matricula?:'--', 0, 0, 'C', $color);
                $this->Cell(10, $alturaCGM, $profissional[0]->numcgm, 0, 0, '', $color);
                $this->Cell(80, $alturaCGM, $profissional[0]->nome, 0, 0, '', $color);
                $this->Cell(70, $alturaCGM, mb_strimwidth($profissional[0]->nomesocial, 0, 40), 0, 0, '', $color);

                $x = $this->GetX();
                if (count($profissional) == 1 && is_null($profissional[0]->vacina)) {
                    $this->Cell(100, 4, 'Profissional sem registro de vacinas', 0, 1, 'C', $color);
                } else {
                    foreach ($profissional as $vacina) {
                        $this->SetX($x);
                        $this->Cell(55, 4, $vacina->vacina, 0, 0, '', $color);
                        $this->Cell(25, 4, $vacina->data_vacinacao, 0, 0, 'C', $color);
                        $this->Cell(20, 4, $vacina->dose, 0, 1, '', $color);
                    }
                }
            }

            $this->Cell(1, 4, '', 0, 1);
        }

        if (empty($this->dados)) {
            $this->Cell(195, 6, 'Nenhum registro encontrado', 1, 1, 'C');
        }
        return $this->imprimir();
    }

    private function imprimirCabecalhoProfissionais()
    {
        $this->SetFillColor(210);
        $this->SetFont('Arial', 'B', 7);

        $this->Cell(15, 5, 'Matricula', 0, 0, 'C', 1);
        $this->Cell(10, 5, 'CGM', 0, 0, '', 1);
        $this->Cell(80, 5, 'Nome', 0, 0, '', 1);
        $this->Cell(70, 5, 'Nome Social', 0, 0, '', 1);
        $this->Cell(55, 5, 'Vacina', 0, 0, '', 1);
        $this->Cell(25, 5, 'Data da Vacinação', 0, 0, 'C', 1);
        $this->Cell(20, 5, 'Dose', 0, 1, '', 1);
    }
}
