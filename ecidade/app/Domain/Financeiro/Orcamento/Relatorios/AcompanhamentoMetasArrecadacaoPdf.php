<?php

namespace App\Domain\Financeiro\Orcamento\Relatorios;

use App\Domain\Financeiro\Planejamento\Relatorios\MetasArrecadacaoPdf;

class AcompanhamentoMetasArrecadacaoPdf extends MetasArrecadacaoPdf
{
    /**
     * @var mixed
     */
    protected $exercicio;

    public function setDados(array $dados)
    {
        $this->dados = $dados;

        $this->alturaTotalValores = 4 * $this->quantidadeExercicios;
        $this->wValores = $this->wValor * $this->quantidadeExercicios;
        $this->wTitulo = $this->wLinha - $this->wValores;

        $this->exercicio = $this->dados['filtros']['exercicio'];
        $this->porBimestre = $this->dados['filtros']['periodicidade'] === 'bimestral';
        $this->porReceita = $this->dados['filtros']['agruparPor'] === 'receita';
        $this->tipoAgrupador = $this->dados['filtros']['agruparPor'];
    }

    public function headers($titulo)
    {
        $this->addTitulo($titulo);

        $this->addTitulo('Art. 13, da Lei Complementar 101/2000');
        $this->addTitulo("Exercício: {$this->exercicio}");
        $recurso = "Agrupado por: Recurso";
        if ($this->porReceita) {
            $recurso = "Agrupado por: Receita";
        }
        $this->addTitulo($recurso);

        $periocidade = "Periodicidade: Mensal";
        if ($this->porBimestre) {
            $periocidade = "Periodicidade: Bimestral";
        }
        $this->addTitulo($periocidade);

        if ($this->dados['filtros']['filtrouRecurso']) {
            $this->addTitulo("Filtrou recurso");
        }

        $this->headerOrigemEmentario();

        $this->wValores = $this->wValor * 7;
        $this->wReceita = $this->wLinha - $this->wValores;
        $this->wDescricaoReceita = $this->wReceita - $this->wValor;
    }

    public function emitir()
    {
        $this->headers('ACOMPANHAMENTO DAS METAS DE ARRECADAÇÃO DA RECEITA');
        $this->capa('METAS DE ARRECADAÇÃO DA RECEITA');

        $this->imprimeMetas();
        $this->imprimeFonteNotas();

        $filename = sprintf('tmp/meta-arrecadacao-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function capa($titulo)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 28);

        $this->SetY($this->yCapa);
        $this->MultiCell($this->wCapa, 12, $titulo, 0, 'R');
        $this->SetX(65);
        $this->SetFont('Arial', 'B', 21);
        $this->MultiCell($this->wCapa, 12, "ACOMPANHAMENTO DO CRONOGRAMA DE DESEMBOLSO", 0, 1, 'R');
        $this->SetX(65);
        $this->Cell($this->wCapa, 12, "EXERCÍCIO {$this->exercicio}", 0, 1);
    }
}
