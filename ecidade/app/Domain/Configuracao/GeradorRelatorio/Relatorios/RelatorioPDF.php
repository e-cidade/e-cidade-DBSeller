<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Relatorios;

use App\Domain\Configuracao\GeradorRelatorio\Contracts\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeMascarar;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeQuebrar;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeTotalizar;
use ECidade\Pdf\Pdf;

class RelatorioPDF extends Pdf implements Relatorio
{
    use PodeTotalizar, PodeQuebrar, PodeMascarar;

    private $layout;

    private $campos;

    private $dados;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    public function setLayout(array $layout)
    {
        $this->layout = $layout;
    }

    public function setCampos(array $campos)
    {
        $this->campos = $campos;
    }

    public function setDados(array $dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function emitir()
    {
        $this->imprimir();

        $fileName = 'tmp/gerador_relatorio_' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            'name' => $this->layout['nome'],
            'path' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    /**
     * @param string $orientation
     * @param string $size
     * @param int $rotation
     * @return void
     *
     * @override
     */
    public function addPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::addPage($this->layout['orientacao'], $this->layout['formato'], $rotation);
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function imprimir()
    {
        $this->initPdf();

        $this->setFont('ARIAL', '', 7);

        $fill = true;
        foreach ($this->dados as $dado) {
            $this->imprimirQuebra($dado);
            $this->imprimirLinha($dado, $fill = !$fill);
        }

        $this->imprimirTotalizador();
    }

    private function initPdf()
    {
        $this->controleQuebras = [];
        $this->totalizador = [];
        $this->addTitulo($this->layout['nome']);
        $this->setMargins(
            $this->layout['margem']['esquerda'],
            $this->layout['margem']['superior'],
            $this->layout['margem']['direita']
        );

        $this->init(false);
        $this->setAutoPageBreak(true, $this->layout['margem']['inferior'] + 15);
        $this->addPage();

        $this->imprimirQuebra($this->dados[0], true);
    }

    private function imprimirQuebra($dados, $imprimiCabecalho = false)
    {
        if (!$imprimiCabecalho) {
            $this->ln(4);
        }

        $tab = 0;
        $xInicial = $this->getX();
        $this->setFont('ARIAL', 'B', 8);
        foreach ($this->verificaQuebra($dados, $this->campos) as $quebra) {
            if ($quebra !== '') {
                $this->cell(200, 5, $quebra, 0, 1);
                $imprimiCabecalho = true;
            }

            $tab += 4;
            $this->setX($xInicial + $tab);
        }

        $this->setX($xInicial);
        if ($imprimiCabecalho) {
            $this->imprimirCabecalho();
        }

        $this->setFont('ARIAL', '', 8);
    }

    private function imprimirCabecalho()
    {
        $this->setFont('ARIAL', 'B', 8);

        foreach ($this->campos as $campo) {
            if ($campo['quebra']) {
                continue;
            }

            $width = $campo['largura'];
            $text = $campo['alias'] ?: $campo['nome'];
            $alinhamento = strtoupper($campo['alinhamentoCabecalho']);
            $this->cell($width, 5, $text, 1, 0, $alinhamento, true);
        }
        $this->ln();
    }

    /**
     * @param $dados
     * @param $fill
     * @return void
     * @throws \Exception
     */
    private function imprimirLinha($dados, $fill = false)
    {
        $height = $this->getTamanhoLinha($dados);
        if ($this->getAvailableHeight() < $height) {
            $this->addPage();
            $this->imprimirCabecalho();
            $this->setFont('ARIAL', '', 7);
        }

        $y = $this->getY();
        $x = $this->getX();
        if ($fill) {
            $this->rect($x, $y, $this->getLarguraTotalConfigurada(), $height, 'F');
        }

        foreach ($this->campos as $campo) {
            $this->count($campo, $dados->{$campo['nome']});
            if ($campo['quebra']) {
                continue;
            }

            $x = $this->getX();
            $valorCampo = $this->getValorMascarado($dados, $campo);
            $numeroLinhas = $this->nbLines($campo['largura'], $valorCampo);
            $alinhamento = strtoupper($campo['alinhamento']);
            if ($numeroLinhas > 1) {
                $this->multiCell($campo['largura'], 4, $valorCampo, 0, $alinhamento);
                $this->setXY($x + $campo['largura'], $y);
            } else {
                $this->cell($campo['largura'], $height, $valorCampo, 0, 0, $alinhamento);
            }
        }
        $this->ln($height);
    }

    private function imprimirTotalizador()
    {
        if (empty($this->totalizador)) {
            return;
        }

        if ($this->getAvailableHeight() < 13) {
            $this->addPage();
        }

        $totalWidth = $this->getTotalWidth();
        $this->setFont('ARIAL', 'B', 10);
        $this->cell($totalWidth, 5, 'TOTAIS', 0, 1);
        $this->imprimirCabecalhoTotalizador();

        $this->setFont('ARIAL', '', 7);
        foreach ($this->totalizador as $total) {
            if ($this->getAvailableHeight() < 4) {
                $this->addPage();
                $this->imprimirCabecalhoTotalizador();
                $this->setFont('ARIAL', '', 7);
            }

            $this->cell((40 / 100) * $totalWidth, 4, $total->campo); // 40%
            $this->cell((30 / 100) * $totalWidth, 4, $total->tipo); // 30%
            $this->cell((30 / 100) * $totalWidth, 4, $total->valor, 0, 1, 'C'); // 30%
        }
    }

    private function imprimirCabecalhoTotalizador()
    {
        $totalWidth = $this->getTotalWidth();

        $this->setFont('ARIAL', 'B', 8);
        $this->cell((40 / 100) * $totalWidth, 5, 'CAMPO', 1, 0, 'C'); // 40%
        $this->cell((30 / 100) * $totalWidth, 5, 'TIPO', 1, 0, 'C'); // 30%
        $this->cell((30 / 100) * $totalWidth, 5, 'VALOR', 1, 1, 'C'); // 30%
    }

    private function getTotalWidth()
    {
        return $this->getPageWidth() - $this->getLeftMargin() - $this->getRightMargin();
    }

    private function getLarguraTotalConfigurada()
    {
        return array_reduce($this->campos, function ($total, $campo) {
            $soma = $campo['quebra'] ? 0 : $campo['largura'];
            return $total + $soma;
        }, 0);
    }

    /**
     * @param $dados
     * @return float|int
     * @throws \Exception
     */
    private function getTamanhoLinha($dados)
    {
        $tamanho = 4;
        foreach ($this->campos as $campo) {
            if ($campo['quebra']) {
                continue;
            }

            $valorCampo = $this->getValorMascarado($dados, $campo);
            $tamanhoCampo = $this->nbLines($campo['largura'], $valorCampo) * 4;
            if ($tamanhoCampo > $tamanho) {
                $tamanho = $tamanhoCampo;
            }
        }

        return $tamanho;
    }
}
