<?php

namespace App\Domain\RecursosHumanos\Pessoal\Relatorios\AjudaCusto;

use ECidade\File\Csv\Dumper\Dumper;

class AjudaCustoCsv extends Dumper
{
    protected $data;

    public function __construct(array $data)
    {
        $this->setCsvControl(";", '"');
        $this->data = $data;
    }

    public function emitir()
    {
        $fileName = 'tmp/ajuda-custo'.time().'.csv';
        $this->dumpToFile($this->organizarDados(), $fileName);

        return [
            "name" => "Relatório de Ajuda de Custo CSV",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function organizarDados()
    {
        $dadosImprimir = [$this->cabecalho()];
        foreach ($this->data as $impressao) {
            $dadosImprimir[] = [
                $impressao['matricula'],
                $impressao['nome'],
                $impressao['cpf_servidor'],
                $impressao['nome_dependente'],
                $impressao['cpf_dependente'],
                $impressao['local'],
                $impressao['especializacao'],
                $impressao['graduacao'],
                $impressao['unidade_ensino'],
                $impressao['quantidade']
            ];
        }
        return $dadosImprimir;
    }
    private function cabecalho()
    {
        return [
            'MATRICULA',
            'NOME',
            'CPF',
            'DEPENDENTE',
            'CPF DEPENDENTE',
            'LOCAL',
            'ESPECIALIZACAO',
            'GRADUACAO',
            'UNIDADE DE ENSINO',
            'QUANT.'
        ];
    }
}
