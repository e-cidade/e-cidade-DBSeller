<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios\LOA;

use App\Domain\Financeiro\Planejamento\Relatorios\Pdf;

class AnexoTresPdf extends Pdf
{
    protected $wReceita = 30;
    protected $wDescricao = 165;
    protected $titulo = 'Anexo 3 - Fontes da Receita ';

    public function emitir()
    {
        $this->headers($this->titulo);
        $this->headerOrigemEmentario();
        $this->capa($this->titulo);
        $this->imprimeDados();
        $this->imprimeAssinaturas();

        $filename = sprintf('tmp/anexo-3-receita-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function imprimeDados()
    {
        $this->imprimeCabecalho();
        foreach ($this->dados['dados'] as $dado) {
            $this->validaQuebraPagina();
            $dado->sintetico ? $this->bold() : $this->regular();

            $this->cell($this->wReceita, 5, $dado->estrutural, 0, 0, 'L');
            $this->multiCell($this->wDescricao, 5, $dado->descricao, 0, 'L');
        }
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->setFont('Arial', 'B', 7);
        $this->cell($this->wReceita, 5, 'Natureza', 0, 0, 'L', 1);
        $this->cell($this->wDescricao, 5, 'Descrição', 0, 1, 'L', 1);
    }

    protected function validaQuebraPagina()
    {
        if ($this->getAvailHeight() < 10) {
            $this->imprimeCabecalho();
        }
    }
}
