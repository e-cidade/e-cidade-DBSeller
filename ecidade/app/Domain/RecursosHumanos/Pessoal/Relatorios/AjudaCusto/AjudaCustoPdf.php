<?php

namespace App\Domain\RecursosHumanos\Pessoal\Relatorios\AjudaCusto;

use ECidade\Pdf\Pdf;

class AjudaCustoPdf extends Pdf
{
    protected $data;

    public function __construct(array $data)
    {
        parent::__construct('L');
        $this->data = $data;
        $this->addTitulo('Relatório de ajuda de custo');
    }

    public function emitir()
    {
        $this->initEmissao();
        $this->setFillColor(200);
        $this->imprimir();
        $fileName = 'tmp/ajuda-custo.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório de Ajuda de Custo PDF",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function initEmissao()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader();
    }

    public function imprimir()
    {
        $this->cabecalho();
        $this->setFont('Arial', '', 7);
        foreach ($this->data as $impressao) {
            $this->cell(15, 5, $impressao['matricula'], 1, 0, 'C');
            $this->cell(50, 5, $impressao['nome'], 1, 0, 'C');
            $this->cell(16, 5, $impressao['cpf_servidor'], 1, 0, 'C');
            $this->cell(50, 5, $impressao['nome_dependente'], 1, 0, 'C');
            $this->cell(16, 5, $impressao['cpf_dependente'], 1, 0, 'C');
            $this->cell(30, 5, $impressao['local'], 1, 0, 'C');
            $this->cell(30, 5, $impressao['especializacao'], 1, 0, 'C');
            $this->cell(60, 5, $impressao['unidade_ensino'], 1, 0, 'C');
            $this->cell(12, 5, $impressao['quantidade'], 1, 1, 'C');
        }
    }

    public function cabecalho()
    {
        $this->addPage();
        $this->setFont('Arial', 'B', 7);
        $this->cell(15, 5, 'MATRICULA', 1, 0, 'C', 1);
        $this->cell(50, 5, 'NOME', 1, 0, 'C', 1);
        $this->cell(16, 5, 'CPF', 1, 0, 'C', 1);
        $this->cell(50, 5, 'DEPENDENTE', 1, 0, 'C', 1);
        $this->cell(16, 5, 'CPF DEP.', 1, 0, 'C', 1);
        $this->cell(30, 5, 'LOCAL', 1, 0, 'C', 1);
        $this->cell(30, 5, 'ESPECIALIZACAO', 1, 0, 'C', 1);
        $this->cell(60, 5, 'UNIDADE DE ENSINO', 1, 0, 'C', 1);
        $this->cell(12, 5, 'QUANT.', 1, 1, 'C', 1);
    }
}
