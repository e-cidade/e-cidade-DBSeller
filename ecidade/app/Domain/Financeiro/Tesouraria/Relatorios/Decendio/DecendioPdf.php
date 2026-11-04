<?php

namespace App\Domain\Financeiro\Tesouraria\Relatorios\Decendio;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class DecendioPdf extends Pdf
{


    /**
     * @var array
     */
    private $dados = [];

    public function setDados(array $dados)
    {
        $this->dados = $dados;
        return $this;
    }

    /**
     * @param string $titulo
     * @param object $periodo
     * @param string $instituicao
     */
    public function headers($titulo, $periodo, $instituicao)
    {
        $this->addTitulo($titulo);
        $this->addTitulo('');
        $this->addTitulo(sprintf("PERÍODO: %s - %s", $periodo->dataInicial, $periodo->dataFinal));
        $this->addTitulo("INSTITUIÇÃO: {$instituicao}");
        return $this;
    }

    public function emitir()
    {
        $this->imprimeCorpo();
        $filename = sprintf('tmp/decendio-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeCabecalho()
    {
        $this->bold();
        $this->cell(25, $this->hLinha, 'Estrutural', 1, 0, 'C');
        $this->cell(20, $this->hLinha, 'Lançamento', 1, 0, 'C');
        $this->cell(97, $this->hLinha, 'Documento', 1, 0, 'C');
        $this->cell(20, $this->hLinha, 'Data', 1, 0, 'C');
        $this->cell(30, $this->hLinha, 'Valor', 1, 1, 'C');
        $this->regular();
    }

    private function imprimeCorpo()
    {
        foreach ($this->dados as $dado) {
            $this->addPage();
            $this->SetFont('Arial', 'B', 7);
            $this->imprimeSlip($dado);
        }
    }

    private function imprimeSlip($dado)
    {
        $this->imprimeTexto(12, 'Slip:', 20, $dado->slip);
        $this->imprimeTexto(10, 'Data:', 20, $dado->data);
        $this->imprimeTexto(22, 'Desdobramento:', 20, $dado->desdobramento);
        $this->imprimeTexto(10, 'Valor:', 20, formataValorMonetario($dado->valor), 1);

        $credito = $dado->credito;
        $debito = $dado->debito;

        $debito = sprintf('%s - %s - %s', $debito->reduzido, $debito->estrutural, $debito->descricao);
        $this->imprimeTexto(12, 'Débito:', 170, $debito, 1);

        $credito = sprintf('%s - %s - %s', $credito->reduzido, $credito->estrutural, $credito->descricao);
        $this->imprimeTexto(12, 'Crédito:', 170, $credito, 1);

        $this->multiCell($this->wLinhaP, $this->hLinha, $dado->observacao);
        $this->ln();
        $this->imprimeLancamentos($dado->lancamentos);
    }

    private function imprimeTexto($wLabel, $label, $wValor, $valor, $quebraLinha = 0)
    {
        $this->bold();
        $this->cell($wLabel, $this->hLinha, $label, 0, 0);
        $this->regular();
        $this->cell($wValor, $this->hLinha, $valor, 0, $quebraLinha);
    }

    private function imprimeLancamentos($lancamentos)
    {
        $this->bold();
        $this->cell($this->wLinhaP, $this->hLinha, 'Lançamentos de arrecadação de receita', 1, 1, 'C');
        $this->regular();

        $this->imprimeCabecalho();
        $total = 0;
        foreach ($lancamentos as $lancamento) {
            $total += $lancamento->valor;
            $this->quebraPagina();
            $this->cell(25, $this->hLinha, $lancamento->estrutural, 1, 0, 'C');
            $this->cell(20, $this->hLinha, $lancamento->lancamento, 1, 0, 'C');
            $this->cell(97, $this->hLinha, $lancamento->documento, 1, 0, 'L');
            $this->cell(20, $this->hLinha, $lancamento->data, 1, 0, 'C');
            $this->cell(30, $this->hLinha, formataValorMonetario($lancamento->valor), 1, 1, 'R');
        }
        $this->bold();
        $this->cell(162, $this->hLinha, 'Total', 1, 0, 'R');
        $this->cell(30, $this->hLinha, formataValorMonetario($total), 1, 1, 'R');
    }

    protected function quebraPagina()
    {
        if ($this->validaQuebraPagina()) {
            $this->addPage();
            $this->imprimeCabecalho();
        }
    }
}
