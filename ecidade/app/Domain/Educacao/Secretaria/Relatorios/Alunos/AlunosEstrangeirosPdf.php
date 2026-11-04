<?php

namespace App\Domain\Educacao\Secretaria\Relatorios\Alunos;

use ECidade\Pdf\Pdf;

class AlunosEstrangeirosPdf extends Pdf
{
    protected $dados;
    public function __construct(array $dados)
    {
        parent::__construct();
        $this->dados = $dados;
        $this->addTitulo('Relatório Alunos Estrangeiros');
        $this->addTitulo('');

        if (count($dados) > 1) {
            $this->addTitulo('Escola: TODAS');
        } else {
            $escola = "Escola: {$dados[0]->escola}";
            $this->addTitulo($escola);
        }
        $ano = "Ano Letivo: {$dados[0]->ano}";
        $this->addTitulo($ano);
        $total = "Total de Alunos: {$this->calculaTotalAlunos()}";
        $this->addTitulo($total);
    }

    public function cabecalho()
    {
        $this->setFont('Arial', '', 8);
        $this->cell(60, 5, 'NOME', 1, 0, 'C', 1);
        $this->cell(25, 5, 'SEXO', 1, 0, 'C', 1);
        $this->cell(27, 5, 'NASCIMENTO', 1, 0, 'C', 1);
        $this->cell(27, 5, 'MATRÍCULA', 1, 0, 'C', 1);
        $this->cell(28, 5, 'TRANSFERÊNCIA', 1, 0, 'C', 1);
        $this->cell(16, 5, 'ETAPA', 1, 0, 'C', 1);
        $this->cell(12, 5, 'FREQ', 1, 1, 'C', 1);
    }

    public function emitir()
    {
        $this->initPdf();
        $this->imprimeDados();
        $fileName = 'tmp/alunos_estrangeiros-' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Relatório de Alunos Estrangeiros PDF",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setMargins(8, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader();
    }

    public function imprimeDados()
    {
        foreach ($this->dados as $dado) {
            $this->addPage();
            $this->setFillColor(200);
            $this->cell(195, 5, $dado->escola, 1, 1, 'C', 1);
            $this->cabecalho();
            $this->setFont('Arial', '', 7);
            $this->setMargins(8, 8, 8);
            $fill = 0;
            $this->setFillColor(230);
            if (count($dado->alunosEstrangeiros) > 0) {
                foreach ($dado->alunosEstrangeiros as $aluno) {
                    $this->cell(60, 5, $aluno->nome, 1, 0, 'C', $fill);
                    $this->cell(25, 5, $aluno->sexo, 1, 0, 'C', $fill);
                    $this->cell(27, 5, $aluno->dataNascimento, 1, 0, 'C', $fill);
                    $this->cell(27, 5, $aluno->dataMatricula, 1, 0, 'C', $fill);
                    $this->cell(28, 5, $aluno->dataTransferencia, 1, 0, 'C', $fill);
                    $this->cell(16, 5, $aluno->etapa, 1, 0, 'C', $fill);
                    $this->cell(12, 5, $aluno->percentualFrequencia . '%', 1, 1, 'C', $fill);
                    $fill = !$fill;
                }
            } else {
                $this->cell(195, 5, 'NENHUM ALUNO ESTRANGEIRO NESTA ESCOLA', 1, 1, 'C', 0);
            }
        }
    }

    public function calculaTotalAlunos()
    {
        $total = 0;
        foreach ($this->dados as $dado) {
            $total += count($dado->alunosEstrangeiros);
        }
        return $total;
    }
}
