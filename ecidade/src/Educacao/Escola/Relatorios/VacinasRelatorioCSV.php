<?php

namespace ECidade\Educacao\Escola\Relatorios;

use ECidade\File\Csv\Dumper\Dumper;

class VacinasRelatorioCSV extends Dumper
{
    private $dados = [];

    public function __construct(array $dados)
    {
        $this->setCsvControl(';', '"');
        $this->dados = $dados;
    }

    public function emitirCsv()
    {
        $filename = sprintf('tmp/relatrio-vacinacao-%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            'name' => 'Relatório de Vacinação CSV',
            'path' => $filename
        ];
    }

    private function organizarDados()
    {
        $dadosImprimir = [$this->cabecalho()];

        foreach ($this->dados as $dado) {
            $dadosImprimir[] = [
                $dado->ed18_i_codigo,
                $dado->ed18_codigoreferencia,
                trim($dado->escola),
                $dado->matricula?:'--',
                $dado->numcgm,
                $dado->nome,
                $dado->nomesocial,
                $dado->vacina?:'Profissional sem registro de vacinas',
                $dado->ed181_data,
                $dado->dose
            ];
        }

        return $dadosImprimir;
    }

    private function cabecalho()
    {
        return [
            'Código',
            'Cód.Referência',
            'Escola',
            'Matrícula',
            'CGM',
            'Nome',
            'Nome Social',
            'Vacina',
            'Data da Vacinação',
            'Dose'
        ];
    }
}
