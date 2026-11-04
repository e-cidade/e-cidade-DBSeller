<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Relatorios;

use App\Domain\Configuracao\GeradorRelatorio\Contracts\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits\PodeMascarar;

class RelatorioTXT implements Relatorio
{
    use PodeMascarar;

    private $layout;

    private $campos;

    private $dados;

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
        $file = 'tmp/gerador_relatorio_' . time() . '.txt';
        foreach ($this->dados as $dado) {
            file_put_contents($file, $this->imprimirLinha($dado), FILE_APPEND);
        }

        return [
            'name' => $this->layout['nome'],
            'path' => ECIDADE_REQUEST_PATH . $file
        ];
    }

    /**
     * @param $dados
     * @return string
     * @throws \Exception
     */
    private function imprimirLinha($dados)
    {
        $str = '';
        foreach ($this->campos as $campo) {
            $valorCampo = $this->getValorMascarado($dados, $campo);
            $str .= str_pad(substr($valorCampo, 0, $campo['largura']), $campo['largura']);
        }

        return $str . "\n";
    }
}
