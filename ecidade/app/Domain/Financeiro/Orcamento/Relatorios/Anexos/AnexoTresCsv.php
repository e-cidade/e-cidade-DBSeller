<?php

namespace App\Domain\Financeiro\Orcamento\Relatorios\Anexos;

use ECidade\File\Csv\Dumper\Dumper;

class AnexoTresCsv extends Dumper
{
    protected $ementario = [];
    protected $titulos = [];

    protected $cabecalho = [
        "conta" => "Conta",
        "nome" => "Nome",
    ];

    public function setEmentario(array $dados)
    {
        $this->ementario = $dados;
        return $this;
    }

    public function emitir()
    {
        $this->setCsvControl(';', '"');
        $filename = sprintf('tmp/anexo-3-%s.csv', time());
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
        $dadosImprimir[] = $this->cabecalho;

        foreach ($this->ementario as $dado) {
            $dadosImprimir[] = [
                $dado->natureza,
                $dado->nome,
            ];
        }
        return $dadosImprimir;
    }

    public function setTitulos(array $titulos)
    {
        $this->titulos = $titulos;
        return $this;
    }
}
