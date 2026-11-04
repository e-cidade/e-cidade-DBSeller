<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Relatorios;

use App\Domain\Configuracao\GeradorRelatorio\Contracts\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeMascarar;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeQuebrar;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeTotalizar;
use ECidade\File\Csv\Dumper\Dumper;

class RelatorioCSV extends Dumper implements Relatorio
{
    use PodeTotalizar, PodeQuebrar, PodeMascarar;

    /**
     * @var array
     */
    private $layout;

    /**
     * @var array
     */
    private $campos;

    /**
     * @var array
     */
    private $dados;

    public function __construct()
    {
        $this->setCsvControl();
    }

    /**
     * @param array $layout
     * @return RelatorioCSV
     */
    public function setLayout(array $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param array $campos
     * @return RelatorioCSV
     */
    public function setCampos(array $campos)
    {
        $this->campos = $campos;
        return $this;
    }

    /**
     * @param array $dados
     * @return RelatorioCSV
     */
    public function setDados(array $dados)
    {
        $this->dados = $dados;
        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function emitir()
    {
        $enclosure = $this->layout['delimitarTexto'] ? '"' : null;
        $this->setCsvControl(';', $enclosure);
        $output = $this->imprimir();
        return [
            'name' => $this->layout['nome'],
            'path' => ECIDADE_REQUEST_PATH . $output
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function imprimir()
    {
        $fileName = 'tmp/gerador_relatorio_' . time() . '.csv';
        return $this->write(function () {
            return $this->montaDados();
        }, $fileName);
    }

    /**
     * @return \Generator
     * @throws \Exception
     */
    private function montaDados()
    {
        foreach ($this->initCsv() as $linha) {
            yield $linha;
        }

        foreach ($this->dados as $dados) {
            foreach ($this->montaQuebra($dados) as $linha) {
                yield $linha;
            }
            yield $this->montaLinha($dados);
        }

        foreach ($this->montaTotalizador() as $linha) {
            yield $linha;
        }
    }

    /**
     * @return \Generator
     */
    private function initCsv()
    {
        $this->controleQuebras = [];
        $this->totalizador = [];
        return $this->montaQuebra($this->dados[0], true);
    }

    /**
     * @param $dados
     * @param $imprimiCabecalho
     * @return \Generator
     */
    private function montaQuebra($dados, $imprimiCabecalho = false)
    {
        $array = [];
        foreach ($this->verificaQuebra($dados, $this->campos) as $quebra) {
            if (!$imprimiCabecalho && $quebra !== '') {
                yield []; // coloca uma linha em branco para separar os dados
            }

            $array[] = $quebra;
            if ($quebra !== '') {
                $imprimiCabecalho = true;
                yield $array;
                $array[count($array) - 1] = ''; //limpa o valor para não aparecer nas outras quebras
            }
        }

        if ($imprimiCabecalho && $this->layout['imprimirCabecalho']) {
            yield $this->montaCabecalho();
        }
    }

    /**
     * @param $dados
     * @return array
     * @throws \Exception
     */
    private function montaLinha($dados)
    {
        $linha = [];
        foreach ($this->campos as $campo) {
            $valorCampo = $this->getValorMascarado($dados, $campo);
            $this->count($campo, $valorCampo);
            if ($campo['quebra']) {
                $linha[] = '';
                continue;
            }

            $linha[] = $valorCampo;
        }

        return $linha;
    }

    /**
     * @return \Generator|void
     */
    private function montaTotalizador()
    {
        if (empty($this->totalizador)) {
            return;
        }

        yield [];
        yield ['TOTAIS'];
        yield ['CAMPO', 'TIPO', 'VALOR'];
        foreach ($this->totalizador as $total) {
            yield $total;
        }
    }

    /**
     * @return array
     */
    private function montaCabecalho()
    {
        $cabecalho = [];
        foreach ($this->campos as $campo) {
            if ($campo['quebra']) {
                $cabecalho[] = '';
                continue;
            }
            $cabecalho[] = $campo['alias'] ?: $campo['nome'];
        }

        return $cabecalho;
    }
}
