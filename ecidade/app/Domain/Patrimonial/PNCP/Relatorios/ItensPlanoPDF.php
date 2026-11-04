<?php

namespace App\Domain\Patrimonial\PNCP\Relatorios;

use ECidade\Pdf\Pdf;

class ItensPlanoPDF extends Pdf
{
    /**
     * @var array
     */
    private $dados;
    private $quantidade = 0;
    private $valorUnitario = 0;
    private $valorTotal = 0;
    private $valorOrcamento = 0;


    public function __construct()
    {
        parent::__construct('L');
        $this->addTitulo('Relátorio de Itens do Plano de Contratação');
    }

    public function addDados(array $dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return string[]
     */
    public function emitir()
    {
        $this->init(false);

        $this->montaCabecalho();
        $linhaImpressa = 0;
        foreach ($this->dados as $item) {
            $linhaImpressa++;
            $color = !($linhaImpressa % 2);

            if ($this->getAvailHeight() < 5) {
                $this->montaCabecalho();
                $this->SetFont('Arial', '', 8);
            }
            $this->montaLinha($item, $color);
            $this->somador($item->quantidade, $item->valorUnitario, $item->valorTotal, $item->valorOrcamentoExercicio);
        }

        $this->Cell(1, 5, '', 0, 1);
        $this->totalizador($this->quantidade, $this->valorUnitario, $this->valorTotal, $this->valorOrcamento);

        return $this->imprimir();
    }

    /**
     * @param $qtd
     * @param $vlrUni
     * @param $vlrTotal
     * @param $vlrOrcamento
     * @return void
     */
    private function somador($qtd, $vlrUni, $vlrTotal, $vlrOrcamento)
    {
        $this->quantidade += $qtd;
        $this->valorUnitario += $vlrUni;
        $this->valorTotal += $vlrTotal;
        $this->valorOrcamento += $vlrOrcamento;
    }

    /**
     * @param $item
     * @param $cor
     * @return void
     */
    private function montaLinha($item, $cor)
    {
        $posicao = $this->configuraPosicao($item->descricao);
        $this->SetFont('Arial', '', 7);
        $this->Cell(12, $posicao['h'], $item->numeroItem, 0, 0, 'C', $cor);
        $this->multicell(72, 5, $item->descricao, 0, 'L', $cor);
        $this->setXY(94, $posicao['y']);
        $this->Cell(30, $posicao['h'], $item->categoriaItemDescricao, 0, 0, 'C', $cor);
        $this->Cell(35, $posicao['h'], $item->unidadeFornecimento, 0, 0, 'C', $cor);
        $this->Cell(20, $posicao['h'], $item->quantidade, 0, 0, 'C', $cor);
        $this->Cell(25, $posicao['h'], number_format($item->valorUnitario, 2, ',', '.'), 0, 0, 'R', $cor);
        $this->Cell(25, $posicao['h'], number_format($item->valorTotal, 2, ',', '.'), 0, 0, 'R', $cor);
        $this->Cell(25, $posicao['h'], number_format($item->valorOrcamentoExercicio, 2, ',', '.'), 0, 0, 'R', $cor);
        $this->Cell(35, $posicao['h'], $item->unidadeRequisitante, 0, 1, 'C', $cor);
    }

    /**
     * @param $quantidade
     * @param $valorUnitario
     * @param $valorTotal
     * @param $valorOrcamento
     * @return void
     */
    private function totalizador($quantidade, $valorUnitario, $valorTotal, $valorOrcamento)
    {
        $this->SetFont('Arial', 'b', 8);
        $this->cell(149, 5, "Totalizador:", 1, 0, "R", 1);
        $this->SetFont('Arial', 'b', 7);
        $this->cell(20, 5, $quantidade, 1, 0, "C", 1);
        $this->cell(25, 5, number_format($valorUnitario, 2, ',', '.'), 1, 0, "R", 1);
        $this->cell(25, 5, number_format($valorTotal, 2, ',', '.'), 1, 0, "R", 1);
        $this->cell(25, 5, number_format($valorOrcamento, 2, ',', '.'), 1, 0, "R", 1);
        $this->cell(35, 5, "", 1, 0, "C", 1);
    }

    /**
     * @param $string
     * @return array
     */
    private function configuraPosicao($string)
    {
        $linhas = $this->nbLines(72, $string);
        $h = $linhas > 1 ? 5 * $linhas : 5;
        $y = $this->getY();

        return [
            'y' => $y,
            'h' => $h,
        ];
    }

    /**
     * @return void
     */
    private function montaCabecalho()
    {
        $this->addPage();
        $this->SetFillColor(210);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(12, 5, 'Item', 1, 0, 'C', 1);
        $this->Cell(72, 5, 'Descrição Item', 1, 0, 'C', 1);
        $this->Cell(30, 5, 'Categoria Item', 1, 0, 'C', 1);
        $this->Cell(35, 5, 'Unidade Fornecimento', 1, 0, 'C', 1);
        $this->Cell(20, 5, 'Quantidade', 1, 0, 'C', 1);
        $this->Cell(25, 5, 'Valor Unitário', 1, 0, 'C', 1);
        $this->Cell(25, 5, 'Valor Total', 1, 0, 'C', 1);
        $this->Cell(25, 5, 'Valor Orçamento', 1, 0, 'C', 1);
        $this->Cell(35, 5, 'Unidade Requisitante', 1, 1, 'C', 1);
    }

    /**
     * @return string[]
     */
    private function imprimir()
    {
        $fileName = 'tmp/itens_do_plano' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relátorio de Itens do Plano de Contratação",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }
}
