<?php

namespace App\Domain\RecursosHumanos\Pessoal\Relatorios\Rubrica;

use ECidade\File\Csv\Dumper\Dumper;

class ObservacaoRubricaCsv extends Dumper
{
    protected $data;

    public function __construct(array $data)
    {
        $this->setCsvControl(";", '"');
        $this->data = $data;
    }

    public function emitir()
    {
        $fileName = 'tmp/observacao-rubrica'.time().'.csv';
        $this->dumpToFile($this->organizarDados(), $fileName);

        return [
            "name" => "Relatório de histórico de Rubrica CSV",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function organizarDados()
    {
        $dadosImprimir = [$this->cabecalho()];
        foreach ($this->data as $impressao) {
            if (empty($impressao['observacao'])) {
                continue;
            }
            $dadosImprimir[] = [
                $impressao['matricula'],
                $impressao['nome'],
                $impressao['codigo_rubrica'],
                $impressao['descricao_rubrica'],
                $impressao['tipo_folha'],
                $impressao['observacao']
            ];
        }
        return $dadosImprimir;
    }

    private function cabecalho()
    {
        return [
            'MATRICULA',
            'NOME',
            'RUBRICA',
            'DESCRICAO',
            'FOLHA',
            'HISTÓRICO'
        ];
    }
}
