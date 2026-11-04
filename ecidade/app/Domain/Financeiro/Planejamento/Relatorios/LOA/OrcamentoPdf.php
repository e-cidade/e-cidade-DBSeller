<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios\LOA;

use App\Domain\Financeiro\Planejamento\Relatorios\Pdf;

class OrcamentoPdf extends Pdf
{
    protected $wReceita = 30;
    protected $wDescricao = 204;
    protected $wRecurso = 20;
    protected $wValor = 25;
    protected $wTotal = 254;
    protected $valorTotal = 0;

    protected $yCapa = 100;
    protected $wCapa = 270;

    protected $titulo = 'Projeções do Orçamento';

    public function emitir()
    {
        $this->headers($this->titulo);
        $this->headerOrigemEmentario();
        $this->capa($this->titulo);
        $this->imprimeDados();
        $this->imprimeAssinaturas();

        $filename = sprintf('tmp/projecao-receita-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->Cell($this->wReceita, 4, 'Natureza', 1, 0, 'C');
        $this->Cell($this->wDescricao, 4, 'Descrição', 1, 0, 'C');
        $this->Cell($this->wRecurso, 4, 'Rec.- Compl.', 1, 0, 'C');
        $this->Cell($this->wValor, 4, 'Previsto', 1, 1, 'C');
        $this->SetFont('Arial', '', 6);
    }

    private function imprimeDados()
    {
        $exercicios = $this->dados['planejamento']['exercicios'];
        $this->imprimeCabecalho();
        foreach ($this->dados['dados'] as $receita) {
            if ($this->getAvailHeight() < 6) {
                $this->imprimeCabecalho();
            }
            $this->imprimeLinha($receita, $exercicios);
        }
        $this->imprimeTotalizador();
    }

    private function imprimeLinha($receita, $exercicios)
    {
        $this->SetFont('Arial', '', 6);
        if ($receita->sintetico) {
            $this->SetFont('Arial', 'B', 6);
        }

        $this->cell($this->wReceita, 5, $receita->estrutural, 1, 0, 'C');

        $content = $receita->descricao;
        if ($this->getFonteSizeByWidthCell($this->wDescricao, 6, $content) < 5) {
            $content = substr($content, 0, 63) . '...';
        }
        $this->cellAdapt(6, $this->wDescricao, 5, $content, 1, 0, 'L');
        $this->cell($this->wRecurso, 5, "{$receita->recurso} - {$receita->complemento}", 1, 0, 'C');

        foreach ($exercicios as $exercicio) {
            $this->imprimeValor($receita->{"valor_{$exercicio}"});
        }

        $this->ln();
    }

    protected function imprimeValor($valor, $h = 5)
    {
        $this->Cell($this->wValor, $h, formataValorMonetario($valor), 1, 0, 'R');
    }

    private function imprimeTotalizador()
    {
        $this->SetFont('Arial', 'B', 7);
        $this->cell($this->wTotal, 5, "Total", 1, 0, 'R');
        $exercicios = $this->dados['planejamento']['exercicios'];
        foreach ($exercicios as $exercicio) {
            $this->imprimeValor($this->dados['totalizador'][$exercicio]);
        }
    }
}
