<?php

namespace App\Domain\Financeiro\Orcamento\Relatorios\Anexos;

use ECidade\File\Csv\Dumper\Dumper;

class AnexoDoisResumoReceitaCsv extends Dumper
{
    /**
     * @var array
     */
    protected $resumo = [];
    protected $titulos = [];

    protected $totalizaReceitasCorrentes = [];
    protected $totalizaReceitasCapital = [];
    protected $totalizaReceitasCorrentesIntra = [];
    protected $totalizaReceitasCapitalIntra = [];


    protected $cabecalhoResumo = [
        "Conta",
        "Nome",
        "Desdobramento",
        "Fonte",
        "Natureza da Receita",
    ];

    public function addTitulos(array $titulosRelatorio)
    {
        $this->titulos = $titulosRelatorio;
        return $this;
    }

    public function addResumo($resumo)
    {
        $this->resumo = $resumo;
        return $this;
    }

    public function addTotalizaReceitasCorrentes($totalizaReceitasCorrentes)
    {
        $this->totalizaReceitasCorrentes = $totalizaReceitasCorrentes;
        return $this;
    }

    public function addTotalizaReceitasCapital($totalizaReceitasCapital)
    {
        $this->totalizaReceitasCapital = $totalizaReceitasCapital;
        return $this;
    }

    public function addTotalizaReceitasCorrentesIntra($totalizaReceitasCorrentesIntra)
    {
        $this->totalizaReceitasCorrentesIntra = $totalizaReceitasCorrentesIntra;
        return $this;
    }

    public function addTotalizaReceitasCapitalIntra($totalizaReceitasCapitalIntra)
    {
        $this->totalizaReceitasCapitalIntra = $totalizaReceitasCapitalIntra;
        return $this;
    }

    public function emitir()
    {
        $this->setCsvControl(';', '"');
        $filename = sprintf('tmp/anexo-2-resumo-%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            'csv' => $filename,
            'csvLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function organizarDados()
    {
        $dadosImprimir = [];
        foreach ($this->titulos as $titulo) {
            $dadosImprimir[] = ['', $titulo];
        }
        $dadosImprimir[] = [];
        $dadosImprimir[] = $this->cabecalhoResumo;

        foreach ($this->resumo as $dado) {
            $desdobramento = $dado->desdobramento != 0 ? formataValorMonetario($dado->desdobramento) : '';
            $fonte = $dado->fonte != 0 ? formataValorMonetario($dado->fonte) : '';
            $catEconomica = $dado->categoriaEconomica != 0 ? formataValorMonetario($dado->categoriaEconomica) : '';

            $dadosImprimir[] = [
                $dado->mascara,
                $dado->descricao,
                $desdobramento,
                $fonte,
                $catEconomica
            ];
        }

        $dadosImprimir[] = [];
        $dadosImprimir[] = ['Resumo Geral'];

        $totalGeral = 0;

        if (!empty($this->totalizaReceitasCorrentes)) {
            $dadosImprimir[] = [];
            $totalGeral += $this->imprimeTotais($dadosImprimir, $this->totalizaReceitasCorrentes, 'Receitas Correntes');
        }
        if (!empty($this->totalizaReceitasCapital)) {
            $dadosImprimir[] = [];
            $totalGeral += $this->imprimeTotais($dadosImprimir, $this->totalizaReceitasCapital, 'Receitas de Capital');
        }
        if (!empty($this->totalizaReceitasCorrentesIntra)) {
            $dadosImprimir[] = [];
            $label = 'Receitas Correntes Intra-orçamentárias';
            $totalGeral += $this->imprimeTotais($dadosImprimir, $this->totalizaReceitasCorrentesIntra, $label);
        }
        if (!empty($this->totalizaReceitasCapitalIntra)) {
            $dadosImprimir[] = [];
            $label = 'Receitas Capital Intra-orçamentárias:';
            $totalGeral += $this->imprimeTotais($dadosImprimir, $this->totalizaReceitasCapitalIntra, $label);
        }
        $dadosImprimir[] = [];

        $dadosImprimir[] = ['Total Geral:', formataValorMonetario($totalGeral)];

        return $dadosImprimir;
    }

    private function imprimeTotais(&$dadosImprimir, $valores, $label)
    {
        $total = 0;
        $dadosImprimir[] = ['Receitas Correntes'];
        foreach ($valores as $dado) {
            $dadosImprimir[] = [$dado->descricao, formataValorMonetario($dado->valor)];
            $total +=$dado->valor;
        }

        return $total;
    }
}
