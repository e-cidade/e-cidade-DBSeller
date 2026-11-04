<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Conferencia;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class MatrizAtributoFpPdf extends Pdf
{
    /**
     * @var array
     */
    private $dados;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->init(false);
    }

    public function headers($exercicio)
    {
        $this->addTitulo('Consistência Matriz');
        $this->addTitulo('Lista dos reduzidos sem o atributo FP');
        $this->addTitulo('');
        $this->addTitulo("Exercício: $exercicio");
    }

    public function imprimir()
    {
        $this->processa();
        $filename = sprintf('tmp/matriz-contas-sem-fp-%s.pdf', time());
        $this->Output($filename, 'F');
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    public function setDados(array $dados)
    {
        $this->dados = $dados;
    }

    private function processa()
    {
        foreach ($this->dados as $instituicao) {
            $this->addPage();
            $this->bold();
            $nome = sprintf('%s - %s', $instituicao->codigo, $instituicao->nome);
            $this->cell($this->wLinhaP, $this->hLinha, $nome, 0, 1);
            $this->regular();
            $this->subCabecalho();
            foreach ($instituicao->contas as $conta) {
                if ($this->validaQuebraPagina()) {
                    $this->addPage();
                    $this->subCabecalho();
                }

                $this->cell(25, $this->hLinha, $conta->conta);
                $this->cell(30, $this->hLinha, $conta->estrutural);
                $this->cell(15, $this->hLinha, $conta->reduzido);
                $this->cell(122, $this->hLinha, $conta->nome, 0, 1);
            }
        }
    }

    protected function imprimeCabecalho()
    {
    }

    private function subCabecalho()
    {
        $this->bold();
        $this->cell(25, $this->hLinha, 'Conta PCASP', 0, 0, 'L', 1);
        $this->cell(30, $this->hLinha, 'Estrutural', 0, 0, 'L', 1);
        $this->cell(15, $this->hLinha, 'Reduzido', 0, 0, 'L', 1);
        $this->cell(122, $this->hLinha, 'Nome', 0, 1, 'L', 1);
        $this->regular();
    }
}
