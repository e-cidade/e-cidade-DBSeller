<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios\LOA;

use App\Domain\Financeiro\Planejamento\Relatorios\Pdf;

class AnexoDoisResumoReceitaPdf extends Pdf
{
    protected $wReceita = 29;
    protected $wDescricao = 90;
    protected $wValor = 24;

    protected $titulo = 'Anexo 2 - Resumo da Receita';

    public function emitir()
    {
        $this->headers($this->titulo);
        $this->headerOrigemEmentario();
        $this->capa($this->titulo);
        $this->imprimeDados();
        $this->imprimeAssinaturas();

        $filename = sprintf('tmp/anexo-2-resumo-receita-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function validaQuebraPagina()
    {
        if ($this->getAvailHeight() < 6) {
            return true;
        }
        return false;
    }

    public function quebraPagina()
    {
        if ($this->validaQuebraPagina()) {
            $this->addPage();
        }
    }

    private function imprimeDados()
    {
        $this->imprimeCabecalho();
        foreach ($this->dados['dados'] as $resumo) {
            if ($this->validaQuebraPagina()) {
                $this->imprimeCabecalho();
            }
            $this->imprimeLinha($resumo);
        }

        $this->imprimeResumoGeral();
    }

    private function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->cell($this->wReceita, 8, 'Natureza', 1, 0, 'C', 1);
        $this->cell($this->wDescricao, 8, 'Descrição', 1, 0, 'C', 1);
        $this->cell($this->wValor, 8, 'Desdobramento', 1, 0, 'C', 1);
        $this->cell($this->wValor, 8, 'Fonte', 1, 0, 'C', 1);
        $this->multiCell($this->wValor, 4, "Natureza da Receita", 1, 'C', 1);
        $this->SetFont('Arial', '', 6);
    }

    private function imprimeLinha($resumo)
    {
        $resumo->sintetico ? $this->bold() : $this->regular();

        $descricao = $resumo->descricao;
        if ($resumo->nivel > 1) {
            $n = $resumo->nivel * 2;
            $descricao = sprintf('%s%s', str_repeat(" ", $n), $resumo->descricao);
        }
        $h = $this->nbLines($this->wDescricao, $descricao) * 5;
        $yInicial = $this->getY();

        $this->cell($this->wReceita, $h, $resumo->mascara, 0, 0, 'L');
        $this->multiCell($this->wDescricao, 5, $descricao, 0, 'L');
        $x = $this->wDescricao + $this->wReceita + 10;
        $this->setXY($x, $yInicial);

        $desdobramento = $resumo->desdobramento != 0 ? formataValorMonetario($resumo->desdobramento) : '';
        $fonte = $resumo->fonte != 0 ? formataValorMonetario($resumo->fonte) : '';
        $catEconomica = $resumo->categoriaEconomica != 0 ? formataValorMonetario($resumo->categoriaEconomica) : '';

        $this->cell($this->wValor, $h, $desdobramento, 0, 0, 'R');
        $this->cell($this->wValor, $h, $fonte, 0, 0, 'R');
        $this->cell($this->wValor, $h, $catEconomica, 0, 0, 'R');
        $this->ln($h);
    }

    private function imprimeResumoGeral()
    {
        $this->addPage();
        $totalGeral = 0;

        if (!empty($this->dados['totalizaReceitasCorrentes'])) {
            $totalGeral += $this->imprimeTotais($this->dados['totalizaReceitasCorrentes'], 'Receitas Correntes');
        }
        if (!empty($this->dados['totalizaReceitasCapital'])) {
            $totalGeral += $this->imprimeTotais($this->dados['totalizaReceitasCapital'], 'Receitas de Capital');
        }
        if (!empty($this->dados['totalizaReceitasCorrentesIntra'])) {
            $label = 'Receitas Correntes Intra-orçamentárias';
            $totalGeral += $this->imprimeTotais($this->dados['totalizaReceitasCorrentesIntra'], $label);
        }
        if (!empty($this->dados['totalizaReceitasCapitalIntra'])) {
            $label = 'Receitas Capital Intra-orçamentárias:';
            $totalGeral += $this->imprimeTotais($this->dados['totalizaReceitasCapitalIntra'], $label);
        }
        $this->ln();
        $this->bold();
        $this->cell(162, 4, 'Total Geral:', 0, 0, 'R', 1);
        $this->cell(30, 4, formataValorMonetario($totalGeral), 0, 1, 'R', 1);
    }

    /**
     *
     * @param $valores
     * @param $label
     * @return int
     */
    private function imprimeTotais($valores, $label)
    {
        $this->quebraPagina();
        $this->bold();
        $this->cell(193, 4, $label, 0, 1, 'C', 1);
        $total = 0;
        $this->regular();
        foreach ($valores as $dado) {
            $this->quebraPagina();
            $h = $this->nbLines(160, $dado->descricao) * 4;
            $yInicial = $this->getY();

            $this->multiCell(162, 4, $dado->descricao, 0, 'L');
            $this->setXY(172, $yInicial);
            $this->cell(30, $h, formataValorMonetario($dado->valor), 0, 1, 'R');
            $total += $dado->valor;
        }

        $this->bold();
        $this->cell(162, 4, "Total das {$label}:", 0, 0, 'R');
        $this->cell(30, 4, formataValorMonetario($total), 0, 1, 'R');
        return $total;
    }
}
