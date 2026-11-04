<?php

namespace App\Domain\Financeiro\Orcamento\Relatorios\Anexos;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class AnexoDoisResumoReceitaPdf extends Pdf
{
    /**
     * @var array
     */
    protected $resumo = [];

    protected $totalizaReceitasCorrentes = [];
    protected $totalizaReceitasCapital = [];
    protected $totalizaReceitasCorrentesIntra = [];
    protected $totalizaReceitasCapitalIntra = [];

    /**
     * @var DBConfig
     */
    protected $instituicaoEmissora;

    /**
     * @param array $titulos
     * @return $this
     */
    public function addTitulos(array $titulos)
    {
        foreach ($titulos as $titulo) {
            $this->addTitulo($titulo);
        }
        return $this;
    }

    /**
     * @param array $resumo
     * @return $this
     */
    public function addResumo(array $resumo)
    {
        $this->resumo = $resumo;
        return $this;
    }

    /**
     * @return array
     */
    public function emitir()
    {
        $this->imprimeCabecalho();
        $this->imprimeResumo();
        $this->imprimeResumoGeral();
        $this->imprimeAssinaturas();

        $filename = sprintf('tmp/anexo-2-receita-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeCabecalho()
    {
        $this->addPage();
        $this->bold();
        $this->cell(29, 8, 'Natureza', 0, 0, 'C', 1);
        $this->cell(90, 8, 'Descrição', 0, 0, 'C', 1);
        $this->cell(24, 8, 'Desdobramento', 0, 0, 'C', 1);
        $this->cell(24, 8, 'Fonte', 0, 0, 'C', 1);
        $this->multiCell(24, 4, 'Natureza da Receita', 0, 'L', 1);
        $this->setFont('Arial', '', '7');
    }

    public function addTotalizaReceitasCorrentes($totalizaReceitasCorrentes)
    {
        $this->totalizaReceitasCorrentes = $totalizaReceitasCorrentes;
        return $this;
    }

    public function addTotalizaReceitasCapital($totalizaReceitasCapital)
    {
        $this->totalizaReceitasCapital = $totalizaReceitasCapital;
        return $this;
    }

    public function addTotalizaReceitasCorrentesIntra($totalizaReceitasCorrentesIntra)
    {
        $this->totalizaReceitasCorrentesIntra = $totalizaReceitasCorrentesIntra;
        return $this;
    }

    public function addTotalizaReceitasCapitalIntra($totalizaReceitasCapitalIntra)
    {
        $this->totalizaReceitasCapitalIntra = $totalizaReceitasCapitalIntra;
        return $this;
    }

    /**
     * @return void
     */
    protected function imprimeResumo()
    {
        foreach ($this->resumo as $dado) {
            $this->quebraPagina();

            $descricao = $dado->descricao;
            if ($dado->nivel > 1) {
                $n = $dado->nivel * 2;
                $descricao = sprintf('%s%s', str_repeat(" ", $n), $dado->descricao);
            }
            $h = $this->nbLines(90, $descricao) * 4;
            $yInicial = $this->getY();

            $this->cell(29, $h, $dado->mascara, 0, 0, 'L');
            $this->multiCell(90, 4, $descricao, 0, 'L');
            $this->setXY(128, $yInicial);

            $desdobramento = $dado->desdobramento != 0 ? formataValorMonetario($dado->desdobramento) : '';
            $fonte = $dado->fonte != 0 ? formataValorMonetario($dado->fonte) : '';
            $catEconomica = $dado->categoriaEconomica != 0 ? formataValorMonetario($dado->categoriaEconomica) : '';

            $this->cell(24, $h, $desdobramento, 0, 0, 'R');
            $this->cell(24, $h, $fonte, 0, 0, 'R');
            $this->cell(24, $h, $catEconomica, 0, 0, 'R');
            $this->ln($h);
        }
    }

    /**
     * Imprime o resumo
     * @return void
     */
    protected function imprimeResumoGeral()
    {
        $this->addPage();
        $totalGeral = 0;

        if (!empty($this->totalizaReceitasCorrentes)) {
            $totalGeral += $this->imprimeTotais($this->totalizaReceitasCorrentes, 'Receitas Correntes');
        }
        if (!empty($this->totalizaReceitasCapital)) {
            $totalGeral += $this->imprimeTotais($this->totalizaReceitasCapital, 'Receitas de Capital');
        }
        if (!empty($this->totalizaReceitasCorrentesIntra)) {
            $label = 'Receitas Correntes Intra-orçamentárias';
            $totalGeral += $this->imprimeTotais($this->totalizaReceitasCorrentesIntra, $label);
        }
        if (!empty($this->totalizaReceitasCapitalIntra)) {
            $label = 'Receitas Capital Intra-orçamentárias:';
            $totalGeral += $this->imprimeTotais($this->totalizaReceitasCapitalIntra, $label);
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
        $this->quebraPaginaOutros();
        $this->bold();
        $this->cell(193, 4, $label, 0, 1, 'C', 1);
        $total = 0;
        $this->regular();
        foreach ($valores as $dado) {
            $this->quebraPaginaOutros();
            $h = $this->nbLines(160, $dado->descricao) * 4;
            $yInicial = $this->getY();

            $this->multiCell(162, 4, $dado->descricao, 0, 'L');
            $this->setXY(172, $yInicial);
            $this->cell(30, $h, formataValorMonetario($dado->valor), 0, 1, 'R');
            $total +=$dado->valor;
        }

        $this->bold();
        $this->cell(162, 4, "Total das {$label}:", 0, 0, 'R');
        $this->cell(30, 4, formataValorMonetario($total), 0, 1, 'R');
        return $total;
    }

    protected function quebraPaginaOutros()
    {
        if ($this->validaQuebraPagina()) {
            $this->addPage();
        }
    }

    private function imprimeAssinaturas()
    {
        $this->ln(30);
        assinaturasFinanceiro($this, $this->instituicaoEmissora, 'BG');
    }

    public function addInstituicaoEmissora(DBConfig $instituicaoEmissora)
    {
        $this->instituicaoEmissora = $instituicaoEmissora;
        return $this;
    }
}
