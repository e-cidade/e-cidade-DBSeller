<?php

namespace App\Domain\Educacao\MatriculaOnline\Relatorios\Vagas;

use ECidade\File\Csv\Dumper\Dumper;

class RelatorioVagasParciaisCSV extends Dumper
{
    protected $dados = [];

    protected $cabecalho = [
        'escola' => 'ESCOLA - ENSINO',
        'etapa' => 'ETAPA',
        'turno' => 'TURNO',
        'turnoParcial' => 'TURNO PARCIAL',
        'vagasRestantes' => 'VAGAS RESTANTES'
    ];

    /**
     * @var array
     */
    private $mapaTurnos = [];

    /**
     * @param array $dados
     */
    public function __construct(array $dados)
    {
        $this->dados = $dados;
        $this->getLegendaTurnos($this->dados['todosTurnos']);
    }

    public function emitir()
    {
        $this->setCsvControl(";", '"');
        $fileName = 'tmp/previsao-vagas' . time() . '.csv';
        $this->dumpToFile($this->organizaDados(), $fileName);
        return [
            "name" => "Relatório de Vagas CSV",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function organizaDados()
    {
        $dadosImprimir = [];
        $dadosImprimir[] = $this->cabecalho;
        foreach ($this->dados['dadosEscolas'] as $dado) {
            $ensino = reset($dado->ensinos);
            $etapas = $ensino->etapas;
            if (count($etapas) > 0) {
                foreach ($etapas as $etapa) {
                    foreach ($etapa->turnos as $turno) {
                        if (is_array(reset($turno->vagas_fornecidas))) {
                            foreach ($turno->vagas_fornecidas as $codigo => $vagasFornecidas) {
                                foreach ($vagasFornecidas as $parcial => $vagaFornecida) {
                                    // Acessa as vagas ocupadas e disponíveis com base no mesmo índice
                                    $vagaOcupada = $turno->vagas_ocupadas[$codigo][$parcial];
                                    $vagaDisponivel = $turno->vagas_disponiveis[$codigo][$parcial];

                                    $linha = [
                                        utf8_encode($dado->nome) .
                                        ' - ' .
                                        utf8_encode($ensino->nome), // Nome Escola - Nome Ensino
                                        utf8_encode($etapa->etapa), // Nome Etapa
                                        utf8_encode($turno->turno), // Nome Turno
                                        utf8_encode($this->mapaTurnos[$parcial]), // Turno Parcial
                                        $vagaDisponivel, // Vagas Disponíveis
                                    ];

                                    $dadosImprimir[] = $linha;
                                }
                            }
                        } else {
                            $linha = [
                                utf8_encode($dado->nome) .
                                ' - ' .
                                utf8_encode($ensino->nome), // Nome Escola - Nome Ensino
                                utf8_encode($etapa->etapa), // Nome Etapa
                                utf8_encode($turno->turno), // Nome Turno
                                '-', // Turno Parcial
                                reset($turno->vagas_disponiveis), // Vagas Disponíveis
                            ];
                            $dadosImprimir[] = $linha;
                        }
                    }
                }
            } else {
                $dadosImprimir[] = 'NENHUMA ETAPA ENCONTRADA';
            }
        }
        return $dadosImprimir;
    }

    private function getLegendaTurnos($todosTurnos)
    {
        foreach ($todosTurnos as $turno) {
            $this->mapaTurnos[$turno->codigo_turno] = $turno->nome_turno;
        }
    }
}
