<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Exporters;

use ECidade\File\Csv\Dumper\Dumper;

class CsvExport extends Dumper
{
    private $dados;
    private $path;
    private $dadosImprimir = array();

    /**
     * Construtor
     *
     * @param object $dados Dados a serem convertidos
     * @param string $sNomeArquivo Nome do elemento root
     */
    public function __construct($dados, $path)
    {
        $this->dados = (array) $dados;
        $this->path  = $path;
    }

    /**
     * Adiciona os elementos de forma recursiva
     *
     * @param Dumper $parent
     * @param mixed $data
     * @return void
     */
    private function dump($data)
    {
        $headerDump = false;
        foreach ($data as $key => $value) {
            if (is_object($value) || is_array($value)) {
                if (!$this->isAssociativeArray($value)) {
                    foreach ($value as $subValue) {
                        foreach ($subValue as $properties) {
                            $aProperties = get_object_vars($properties);
                            $aProperties = $this->filterTagAttribute($aProperties);
                            if (!$headerDump) {
                                $this->dumpHeader($aProperties);
                                $headerDump = true;
                            }
                            $this->dumpBody($aProperties);
                        }
                    }
                }
            }
        }
    }

    private function dumpHeader($aProperties)
    {
        $this->dadosImprimir[] = array_keys($aProperties);
    }

    private function dumpBody($aProperties)
    {

        $aValues = [];
        foreach ($aProperties as $valueProperty) {
            $aValues[] = !is_object($valueProperty) && !is_array($valueProperty) ? $valueProperty :null;
        }
        $this->dadosImprimir[] = $aValues;
    }

    /**
     * Gera o csv de acordo com os dados do contrutor
     *
     * @return void
     */
    public function buildCSV()
    {
        $this->dump($this->dados);
        $this->dumpToFile($this->dadosImprimir, $this->path);
    }

    private function isAssociativeArray($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function filterTagAttribute($aProperties)
    {
        
        $filterProperties = [];
        foreach ($aProperties as $key => $value) {
            if ($key != 'attributes') {
                $filterProperties[$key] = $value;
            }
        }
        
        return $filterProperties;
    }
}
