<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios;

class NotaLancamentoPdf extends Pdf
{

    private $dados;

    public function headers()
    {
        $this->addTitulo('Nota de Lançamento Manual');
        $this->addTitulo('');
        $this->addTitulo("Lote: {$this->dados->lote}");
        $this->addTitulo("Data: " . db_formatar($this->dados->data, 'd'));
        $this->addTitulo("Emissor: " .$this->dados->emissor);
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $this->headers();
        $this->imprimir();

        $filename = sprintf('tmp/nota-lancamento-manual-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->bold();
        $this->Cell($this->wLinhaP, $this->hLinha, 'Informações do(s) Lançamento(s)', 0, 1, 'C', 1);
        $this->regular();
    }

    private function imprimir()
    {
        $this->imprimeCabecalho();
        foreach ($this->dados->lancamentos as $lancamento) {
            $this->quebraPagina();
            $this->bold();
            $this->Cell(20, $this->hLinha, 'Lançamento:', 0, 0, 'L');
            $this->regular();
            $this->Cell(20, $this->hLinha, $lancamento->lancamento, 0, 0, 'L');

            $this->bold();
            $this->Cell(10, $this->hLinha, 'Valor:', 0, 0, 'L');
            $this->regular();
            $this->Cell(20, $this->hLinha, db_formatar($lancamento->valor, 'f'), 0, 0, 'L');

            $this->bold();
            $this->Cell(15, $this->hLinha, 'Histórico:', 0, 0, 'L');
            $this->regular();
            $historico = sprintf('%s - %s', $lancamento->historico->codigo, $lancamento->historico->descricao);
            $this->Cell(112, $this->hLinha, $historico, 0, 1, 'L');

            $this->bold();
            $this->Cell(20, $this->hLinha, 'Documento:', 0, 0, 'L');
            $this->regular();
            $documento = sprintf('%s - %s', $lancamento->documento->documento, $lancamento->documento->descricao);
            $this->Cell(172, $this->hLinha, $documento, 0, 1, 'L');


            // contas crédito/débito
            $this->bold();
            $this->Cell(15, $this->hLinha, 'Natureza:', 'BR', 0, 'C');
            $this->Cell(150, $this->hLinha, 'Conta:', 'BLR', 0, 'C');
            $this->Cell(25, $this->hLinha, 'Recurso:', 'BL', 1, 'C');

            $this->regular();

            $this->imprmimeConta($lancamento->debito, $lancamento->exercicio, 'D');
            $this->imprmimeConta($lancamento->credito, $lancamento->exercicio, 'C');


            // Observação
            $this->imprimeObservacao($lancamento);
            $this->line($this->getX(), $this->getY(), $this->wLinhaP +10, $this->getY());
            $this->ln();
        }
    }

    private function imprmimeConta($conta, $exercicio, $natureza)
    {
        $credito = sprintf('%s - %s', $conta->estrutural, $conta->descricao);
        $tipoDescricao = (isNiteroi()?10:8);
        $recursoCredito =descricaoCompletaRecurso($conta->recurso, $exercicio, $tipoDescricao);
        
        $this->Cell(15, $this->hLinha, $natureza, 'BR', 0, 'C');
        $this->Cell(150, $this->hLinha, $credito, 'BLR', 0, 'L');
        $this->Cell(25, $this->hLinha, $recursoCredito, 'BL', 1, 'L');
    }

    /**
     * @param $lancamento
     * @return void
     */
    public function imprimeObservacao($lancamento)
    {
        $observacao = $lancamento->observacao;

        if (!empty($lancamento->empenho->numemp)) {
            $observacao .= ' Vinculado ao empenho com sequencial: ' . $lancamento->empenho->numemp;
        }
        if (!empty($lancamento->dotacao->reduzido)) {
            $observacao .= '  Vinculado a dotação: ' . $lancamento->dotacao->reduzido;
        }

        if (!empty($lancamento->cgm->numcgm)) {
            $observacao .= '  Vinculado ao CGM: ' . $lancamento->cgm->numcgm;
        }
        if (!empty($lancamento->receita->reduzido)) {
            $observacao .= '  Vinculado a Receita: ' . $lancamento->receita->reduzido;
        }
        $this->bold();
        $this->Cell($this->wLinhaP, $this->hLinha, 'Observação do Lançamento', 0, 1, 'L', 0);
        $this->regular();
        $this->multiCell($this->wLinhaP, $this->hLinha, $observacao);
    }
}
