<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

class LinhasRelatorioLegal
{
    private $codigo;

    private $linhas = [];

    /**
     * RREO / RGF
     * @var string
     */
    private $tipo;


    public function __construct($codigo, $tipo)
    {
        $this->codigo = $codigo;
        $this->tipo = strtolower($tipo);
    }

    public function getLinhas()
    {
        if (empty($this->linhas)) {
            $this->constructLinhasRelatorio();
        }

        return $this->linhas;
    }

    /**
     * Retorna as linhas manuais de um arquivo.
     * @return array
     */
    public function getLinhasManuais()
    {
        $linhasManuais = [];
        foreach ($this->getLinhas() as $linha) {
            if ($linha->valorManual) {
                $colunas = [];
                foreach ($linha->colunas as $coluna) {
                    if ($coluna->permiteEdicaoManual) {
                        $colunas[] = $coluna;
                    }
                }

                $linha->colunas = $colunas;
                $linhasManuais[] = $linha;
            }
        }
        return $linhasManuais;
    }

    private function constructLinhasRelatorio()
    {
        if (empty($this->linhas)) {
            $path = "financeiro/lrf/{$this->tipo}/{$this->codigo}.json";
            $fullpath = storage_path($path);
            if (!file_exists($fullpath)) {
                throw new \Exception(sprintf(
                    'Não foi encontrado o arquivo de configuração do relatório do %s de código %s',
                    $this->tipo,
                    $this->codigo
                ));
            }
            $linhas = \JSON::create()->parse(file_get_contents($fullpath));
            foreach ($linhas as $linha) {
                $this->linhas[$linha->ordem] = $linha;
            }
        }

        return $this->linhas;
    }
}
