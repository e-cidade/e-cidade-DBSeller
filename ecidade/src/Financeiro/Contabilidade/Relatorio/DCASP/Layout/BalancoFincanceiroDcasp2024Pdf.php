<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\DCASP\Layout;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;
use cl_assinatura;

class BalancoFincanceiroDcasp2024Pdf extends Pdf
{
    /**
     * @var array
     */
    private $dados;

    protected $wDescricao = 115;
    protected $wValor = 38.4;
    protected $imprimeQuadroAuxiliar = false;

    /**
     * A = Analítico
     * S = Sintético
     * @var string
     */
    protected $tipoImpressao;

    /**
     * @var string
     */
    private $notaExplicativa;

    public function setDados(array $dados)
    {
        $this->dados = $dados;
        return $this;
    }

    /**
     * @param int $exercicio
     * @param string $mes
     * @param string $instituicao
     * @param string $tipo
     * @return BalancoFincanceiroDcasp2024Pdf
     */
    public function headers($exercicio, $mes, $instituicao, $tipo)
    {
        $this->addTitulo('BALANÇO FINANCEIRO');
        $this->addTitulo('');
        $this->addTitulo(sprintf("EXERCÍCIO: %s", $exercicio));
        $this->addTitulo(sprintf("PERÍODO: %s", $mes));
        $this->addTitulo("INSTITUIÇÃO: {$instituicao}");
        $this->addTitulo("TIPO: {$tipo}");
        return $this;
    }

    public function emitir()
    {
        $this->SetAutoPageBreak(true, 10);

        $this->imprimirDados();

        $this->escreverNotaExplicativa();
        $this->escreveAssinatura();

        $filename = sprintf('tmp/balanco_financeiro-%s.pdf', time());
        $this->Output('I', $filename);

        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function escreverCabecalho($label = null)
    {
        $this->addPage();
        $this->bold();
        $this->cell($this->wDescricao, $this->hLinha, $label, 'TB', 0, 'C');
        $this->cell($this->wValor, $this->hLinha, 'Exercício Atual', 'LTB', 0, 'C');
        $this->cell($this->wValor, $this->hLinha, 'Exercício Anterior', 'LTB', 1, 'C');
        $this->regular();
    }

    private function escreveAssinatura()
    {
        if ($this->validaQuebraPagina()) {
            $this->addPage();
        }
        $this->regular();
        $oAssinatura = new cl_assinatura();
        $this->ln(18);
        assinaturas($this, $oAssinatura, 'BG', false, false);
    }
//
    private function escreverNotaExplicativa()
    {
        if (!empty($this->notaExplicativa)) {
            $this->ln();
            $this->multiCell($this->wLinhaP, $this->hLinha, $this->notaExplicativa);
        }
    }
    public function imprimeQuadroAuxiliar($quadroAuxiliar)
    {
        $this->imprimeQuadroAuxiliar = $quadroAuxiliar;
    }

    public function setTipoImpressao($tipoImpressao)
    {
        $this->tipoImpressao = $tipoImpressao;
    }

    public function setNotaExplicativa($notaExplicativa)
    {
        $this->notaExplicativa = $notaExplicativa;
    }

    protected function imprimeCabecalho()
    {
    }

    private function imprimirDados()
    {
        $this->imprimirQuadro('INGRESSOS', $this->dados['ingressos']);
        $this->imprimirQuadro('DISPÊNDIOS', $this->dados['dispendios']);
        if ($this->imprimeQuadroAuxiliar) {
            $this->imprimirQuadroAuxiliar($this->dados['quadro_auxiliar']);
        }
    }

    private function imprimirQuadro($label, $linhas)
    {
        $this->escreverCabecalho($label);
        foreach ($linhas as $linha) {
            if ($this->validaQuebraPagina()) {
                $this->escreverCabecalho($label);
            }

            if ($linha->totalizadora) {
                $this->bold();
            }

            $descricao = str_repeat(' ', $linha->nivel * 2) . $linha->descricao;

            $this->Cell($this->wDescricao, $this->hLinha, $descricao, 'R', 0, 'L');
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($linha->exercicio_corrente), 'LR', 0, 'R');
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($linha->exercicio_anterior), 'L', 1, 'R');
            $this->regular();

            if ($this->tipoImpressao === 'A' && !empty($linha->porRecurso)) {
                $this->imprimirDadosRecurso($linha);
            }
        }
    }

    private function imprimirDadosRecurso($linha)
    {
        ksort($linha->porRecurso);
        foreach ($linha->porRecurso as $item) {
            $descricao = str_repeat(' ', $linha->nivel * 2) . $item->descricao;
            $this->Cell($this->wDescricao, $this->hLinha, $descricao, 'R', 0, 'L');
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($item->exercicio_corrente), 'LR', 0, 'R');
            $this->Cell($this->wValor, $this->hLinha, formataValorMonetario($item->exercicio_anterior), 'L', 1, 'R');
        }
    }


    private function cabecalhoQuadroAuxiliar()
    {
        $this->addPage();
        $this->bold();
        $yInicial = $this->getY();
        $yFinal = $yInicial + 16;
        $xInicial = $this->getX();
        $larguraDescricao = $this->wLinhaP * 0.30;
        $larguraValores = $this->wLinhaP * 0.35;
        $larguraValor = $larguraValores / 3;
        $this->cell($larguraDescricao, 16, 'Especificação', 'TBR', 0, 'C');
        $this->cell($larguraValores, $this->hLinha, 'Exercício Atual', '1', 0, 'C');
        $this->cell($larguraValores, $this->hLinha, 'Exercício Anterior', 'TBL', 1, 'C');
        $x = $xInicial + $larguraDescricao;
        $yAtual = $this->getY();
        $this->setXY($x, $yAtual);
        $this->line($this->getX(), $yAtual, $this->getX(), $yFinal);
        $this->multiCell($larguraValor, $this->hLinha, "Receita\nOrçamentaria\n(a)", 0, 'C');
        $x += $larguraValor;
        $this->setXY($x, $yAtual);
        $this->line($this->getX(), $yAtual, $this->getX(), $yFinal);
        $this->multiCell($larguraValor, $this->hLinha, "Dedução da\nReceita\nOrçamentaria (b)", 0, 'C');
        $x += $larguraValor;
        $this->setXY($x, $yAtual);
        $this->line($this->getX(), $yAtual, $this->getX(), $yFinal);
        $this->multiCell($larguraValor, $this->hLinha, "Saldo\n(c) = (a-b)", 0, 'C');
        $x += $larguraValor;
        $this->setXY($x, $yAtual);
        $this->line($this->getX(), $yAtual, $this->getX(), $yFinal);
        $this->multiCell($larguraValor, $this->hLinha, "Receita\nOrçamentaria\n(d)", 0, 'C');
        $x += $larguraValor;
        $this->setXY($x, $yAtual);
        $this->line($this->getX(), $yAtual, $this->getX(), $yFinal);
        $this->multiCell($larguraValor, $this->hLinha, "Dedução da\nReceita\nOrçamentaria e)", 0, 'C');
        $x += $larguraValor;
        $this->setXY($x, $yAtual);
        $this->line($this->getX(), $yAtual, $this->getX(), $yFinal);
        $this->multiCell($larguraValor, $this->hLinha, "Saldo\n(f) = (d-e)", 0, 'C');


        $this->line($this->getX(), $yFinal, $this->wLinhaP + 10, $yFinal);
        $this->setXY($this->getX(), $yFinal);
    }

    private function imprimirQuadroAuxiliar($linhas)
    {
        $this->cabecalhoQuadroAuxiliar();

        $larguraDescricao = $this->wLinhaP * 0.30;
        $larguraValores = $this->wLinhaP * 0.35;
        $larguraValor = $larguraValores / 3;

        $totalArrecadacaoCorrente = 0;
        $totalDeducaoCorrente = 0;
        $totalGeralCorrente = 0;
        $totalArrecadacaoAnterior = 0;
        $totalDeducaoAnterior = 0;
        $totalGeralAnterior = 0;

        $somar = [2, 3, 11];
        $this->regular();
        foreach ($linhas as $linha) {
            if ($linha->totalizadora) {
                $this->bold();
            }

            $descricao = $linha->descricao;
            if ($linha->nivel > 1) {
                $descricao = str_repeat(' ', $linha->nivel * 2) . $linha->descricao;
            }

            $nLinhas = $this->nbLines($larguraDescricao, $descricao);
            $hLinhaValores = $nLinhas * $this->hLinha;

            $yInicial = $this->gety();

            $this->multiCell($larguraDescricao, $this->hLinha, $descricao, 'R', 'L');
            $this->setXY($this->getX() + $larguraDescricao, $yInicial);

            $arrecadacaoCorrente = $linha->arrecadacao_corrente;
            $totalCorrente = $arrecadacaoCorrente - ($linha->deducao_corrente * -1);
            $arrecadacaoAnterior = $linha->arrecadacao_anterior;
            $totalAnterior = $arrecadacaoAnterior - ($linha->deducao_anterior * -1);
            $this->Cell($larguraValor, $hLinhaValores, formataValorMonetario($arrecadacaoCorrente), 'LR', 0, 'R');
            $this->Cell($larguraValor, $hLinhaValores, formataValorMonetario($linha->deducao_corrente), 'LR', 0, 'R');
            $this->Cell($larguraValor, $hLinhaValores, formataValorMonetario($totalCorrente), 'LR', 0, 'R');
            $this->Cell($larguraValor, $hLinhaValores, formataValorMonetario($arrecadacaoAnterior), 'LR', 0, 'R');
            $this->Cell($larguraValor, $hLinhaValores, formataValorMonetario($linha->deducao_anterior), 'LR', 0, 'R');
            $this->Cell($larguraValor, $hLinhaValores, formataValorMonetario($totalAnterior), 'L', 1, 'R');
            $this->regular();

            if (in_array($linha->ordem, $somar)) {
                $totalArrecadacaoCorrente += $arrecadacaoCorrente;
                $totalDeducaoCorrente += $linha->deducao_corrente;
                $totalGeralCorrente += $totalCorrente;
                $totalArrecadacaoAnterior += $arrecadacaoAnterior;
                $totalDeducaoAnterior += $linha->deducao_anterior;
                $totalGeralAnterior += $totalAnterior;
            }
        }

        $this->bold();
        $this->Cell($larguraDescricao, $this->hLinha, 'Total', 'R', 0, 'L');
        $this->Cell($larguraValor, $this->hLinha, formataValorMonetario($totalArrecadacaoCorrente), 'LR', 0, 'R');
        $this->Cell($larguraValor, $this->hLinha, formataValorMonetario($totalDeducaoCorrente), 'LR', 0, 'R');
        $this->Cell($larguraValor, $this->hLinha, formataValorMonetario($totalGeralCorrente), 'LR', 0, 'R');
        $this->Cell($larguraValor, $this->hLinha, formataValorMonetario($totalArrecadacaoAnterior), 'LR', 0, 'R');
        $this->Cell($larguraValor, $this->hLinha, formataValorMonetario($totalDeducaoAnterior), 'LR', 0, 'R');
        $this->Cell($larguraValor, $this->hLinha, formataValorMonetario($totalGeralAnterior), 'L', 1, 'R');
    }
}
