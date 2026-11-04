<?php

namespace App\Domain\RecursosHumanos\Pessoal\Relatorios\Rubrica;

use ECidade\Pdf\Pdf;

class ObservacaoRubricaPdf extends Pdf
{
    protected $data;

    public function __construct(array $data)
    {
        parent::__construct('L');
        $this->data = $data;
        $this->addTitulo('Relatório de Observação de rubrica');
    }

    public function emitir()
    {
        $this->initEmissao();
        $this->setFillColor(200);
        $this->imprimir();
        $fileName = 'tmp/observacao-rubrica.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório de Histórico de Rubrica PDF",
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
            if (empty($impressao['observacao'])) {
                continue;
            }
            $this->cell(15, 5, $impressao['matricula'], 1, 0, 'C');
            $this->cell(50, 5, $impressao['nome'], 1, 0, 'C');
            $this->cell(15, 5, $impressao['codigo_rubrica'], 1, 0, 'C');
            $this->cell(50, 5, $impressao['descricao_rubrica'], 1, 0, 'C');
            $this->cell(20, 5, $impressao['tipo_folha'], 1, 0, 'C');
            $this->cell(130, 5, $impressao['observacao'], 1, 1, 'C');
        }
    }

    public function cabecalho()
    {
        $this->addPage();
        $this->setFont('Arial', 'B', 7);
        $this->cell(15, 5, 'MATRICULA', 1, 0, 'C', 1);
        $this->cell(50, 5, 'NOME', 1, 0, 'C', 1);
        $this->cell(15, 5, 'RUBRICA', 1, 0, 'C', 1);
        $this->cell(50, 5, 'DESCRICAO', 1, 0, 'C', 1);
        $this->cell(20, 5, 'FOLHA', 1, 0, 'C', 1);
        $this->cell(130, 5, 'HISTÓRICO', 1, 1, 'C', 1);
    }
}
