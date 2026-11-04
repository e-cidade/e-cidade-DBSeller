<?php

namespace App\Domain\Financeiro\Orcamento\Relatorios\Anexos;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class AnexoTresPdf extends Pdf
{
    /**
     * @var array com o ementario a ser impresso
     */
    private $ementario = [];

    public function addTitulos(array $titulos)
    {
        foreach ($titulos as $titulo) {
            $this->addTitulo($titulo);
        }
        return $this;
    }

    /**
     * @param array $ementario
     * @return $this
     */
    public function addEmentario(array $ementario)
    {
        $this->ementario = $ementario;
        return $this;
    }

    /**
     * @return array
     */
    public function emitir()
    {
        $this->imprimeCabecalho();
        foreach ($this->ementario as $dado) {
            $this->quebraPagina();
            $this->regular();
            $this->cell(25, 4, $dado->natureza, 0, 0, 'L');
            $this->multiCell(170, 4, $dado->nome, 0, 'L');
        }

        $filename = sprintf('tmp/anexo-3-receita-%s.pdf', time());
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
        $this->cell(25, 4, 'Natureza', 0, 0, 'L', 1);
        $this->cell(170, 4, 'Descrição', 0, 1, 'L', 1);
    }
}
