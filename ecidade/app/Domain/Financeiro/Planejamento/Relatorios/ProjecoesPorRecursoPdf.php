<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios;

use Illuminate\Database\Eloquent\Collection;

class ProjecoesPorRecursoPdf extends \ECidade\Pdf\Pdf
{

    private $projecoesDespesa;
    private $projecoesReceita;
    private $headers = [];

    public function emitir()
    {
        $this->init(false);
        $this->addTitulo('Projeções por recurso');
        foreach ($this->headers as $header) {
            $this->addTitulo($header);
        }
        $this->imprimir();

        $filename = sprintf('tmp/projecoes-por-recurso-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function setDados($projecoesDespesa, Collection $projecoesReceita)
    {
        $this->projecoesDespesa = $projecoesDespesa;
        $this->projecoesReceita = $projecoesReceita;
    }


    private function imprimir()
    {
        if (!empty($this->projecoesDespesa)) {
            $this->imprimirDespesa();
        }

        if ($this->projecoesReceita->count() > 0) {
            $this->imprimirReceita();
        }
    }

    private function imprimirDespesa()
    {
        $this->addPage();
        $this->setFont('arial', 'b', 8);
        $this->cell(190, 5, 'PROJEÇÕES DA DESPESA', 0, 1, 'C');
        foreach ($this->projecoesDespesa as $programa) {
            $this->setFont('arial', 'b', 8);
            $label = sprintf('Programa: %s - %s', $programa->formatado, $programa->nome);
            $this->cell(190, 5, $label, 0, 1);

            foreach ($programa->iniciativas as $iniciativa) {
                $this->setFont('arial', 'b', 8);

                $label = sprintf('Iniciativa: %s - %s', $iniciativa->formatado, $iniciativa->nome);
                $this->cell(190, 5, $label, 0, 1);

                $pinta = false;

                $this->cell(130, 5, 'Programática');
                $this->cell(20, 5, 'Recurso');
                $this->cell(20, 5, 'Gestão');
                $this->cell(20, 5, 'Siconfi', 0, 1);
                foreach ($iniciativa->detalhamentos as $detalhamento) {
                    $this->setFont('arial', '', 7);
                    $this->cell(130, 5, $detalhamento->estrutural, 0, 0, 'L', $pinta);
                    $this->cell(20, 5, "$detalhamento->recurso - $detalhamento->complemento", 0, 0, 'L', $pinta);
                    $this->cell(20, 5, "$detalhamento->gestao - $detalhamento->complemento", 0, 0, 'L', $pinta);
                    $this->cell(20, 5, "$detalhamento->siconfi - $detalhamento->complemento", 0, 1, 'L', $pinta);
                    $pinta = !$pinta;
                }
            }
        }
    }


    private function validaQuebraPagina()
    {
        if ($this->gety() > ($this->getH() - 15)) {
            $this->addPage();
        }
    }

    private function imprimirReceita()
    {
        $this->addPage();
        $this->setFont('arial', 'b', 8);
        $this->cell(190, 5, 'Projeções da receita', 0, 1);
        $pinta = false;

        $this->cell(130, 5, 'Estrutural');
        $this->cell(20, 5, 'Recurso');
        $this->cell(20, 5, 'Gestão');
        $this->cell(20, 5, 'Siconfi', 0, 1);
        foreach ($this->projecoesReceita as $receita) {
            $this->setFont('arial', '', 7);

            $natureza = $receita->getNaturezaOrcamento();
            $recurso = $receita->recurso;
            $fonteRecurso = $recurso->fonteRecurso($receita->anoorcamento);
            $siconfi = $fonteRecurso->codigo_siconfi;
            $gestao = $fonteRecurso->gestao;

            $this->cell(130, 5, $natureza->o57_fonte, 0, 0, 'L', $pinta);
            $this->cell(20, 5, "$recurso->o15_recurso - $recurso->o15_complemento", 0, 0, 'L', $pinta);
            $this->cell(20, 5, "$gestao - $recurso->o15_complemento", 0, 0, 'L', $pinta);
            $this->cell(20, 5, "$siconfi - $recurso->o15_complemento", 0, 1, 'L', $pinta);
            $pinta = !$pinta;
        }
    }
}
