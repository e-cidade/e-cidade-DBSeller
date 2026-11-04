<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios;

abstract class AcompanhamentoPdf extends Pdf
{
    protected $wValor = 20;
    protected $wLinha = 280;
    protected $hPagina = 193;
    protected $filtros = [];
    protected $wIniciativa = 0;
    protected $wRegionalizacao = 40;
//    protected $wSubtitulo = 40;
    protected $wProduto = 33;
    protected $wMetaFisica = 35;
    protected $wValoresFonteRecuso = 53;
    protected $wExecucao = 20;

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
    }

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function headers($titulo)
    {
        parent::headers($titulo);

        $regionalizacao = $this->filtros['apresentaRegionalizacao'] == 1 ? 'Apresentar' : 'Não Apresentar';
        $produto = $this->filtros['apresentaProduto'] == 1 ? 'Apresentar' : 'Não Apresentar';
        $metaFisicas = $this->filtros['apresentaValoresMetaFisicas'] == 1 ? 'Apresentar' : 'Não Apresentar';
        $execucao = $this->filtros['apresentaValoresMetaFisicas'] == 1 ? 'Apresentar' : 'Não Apresentar';

        $this->addTitulo('');
        $this->addTitulo("Regionalização: {$regionalizacao}");
        $this->addTitulo("Produto: {$produto}");
        $this->addTitulo("Meta Física: {$metaFisicas}");
        $this->addTitulo("Execução: {$execucao}");

        if ($this->filtros['apresentaValoresMetaFisicas'] == 1) {
            $por = $this->filtros['execucaoPor'] === 'liquidado' ? 'Líquidado' : 'Empenhado';
            $this->addTitulo("Valor: {$por}");
        }
    }

    public function emitir($titulo)
    {
        $this->headers($titulo);
        $this->calculaColunas();
        $this->imprimirDados();

        $filename = sprintf('tmp/acompanhamento-ppa-ldo-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeLinha($label, $descricao)
    {
        $this->bold();
        $this->cell('20', $this->alturaLinha, $label, 'B');
        $this->regular();
        $this->cell('260', $this->alturaLinha, $descricao, 'B', 1);
    }

    protected function imprimeLinhaMultipla($label, $descricao)
    {
        $h = $this->alturaLinha * $this->nbLines(260, $descricao);
        $this->bold();
        $this->cell('20', $h, $label, 'B');
        $this->regular();
        $this->setFont('Arial', '', 8);
        $this->multiCell('260', $this->alturaLinha, $descricao, 'B', 'L');
    }

    protected function getAlturaLinha($iniciativa, $regionalizacao, $produto)
    {
        $linhas = [$this->nbLines($this->wIniciativa, $iniciativa)];
        if ($this->filtros['apresentaRegionalizacao'] == 1) {
            $linhas[] = $this->nbLines($this->wRegionalizacao, $regionalizacao);
        }
        if ($this->filtros['apresentaProduto'] == 1) {
            $linhas[] = $this->nbLines($this->wProduto, $produto);
        }

        $numeroLinhas = collect($linhas)->max();
        $numeroExercicios = 1;
        if ($this->isPPA) {
            $numeroExercicios = count($this->exercicios);
        }
        if ($numeroLinhas < $numeroExercicios) {
            $numeroLinhas = $numeroExercicios;
        }

        return $numeroLinhas * 4;
    }
}
