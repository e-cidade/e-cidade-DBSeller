<?php

namespace App\Domain\Educacao\Secretaria\Relatorios\Alunos;

use ECidade\File\Csv\Dumper\Dumper;

class AlunosEstrangeirosCSV extends Dumper
{
    protected $dados = [];

    protected $cabecalho = [
        'nome' => 'NOME',
        'sexo' => 'SEXO',
        'dataNascimento' => 'NASCIMENTO',
        'dataMatricula' => 'MATRICULA',
        'dataTransferencia' => 'TRANSFERENCIA',
        'etapa' => 'ETAPA',
        'percentualFrequencia' => 'FREQ'
    ];

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function emitir()
    {
        $this->setCsvControl(";", '"');
        $filename = sprintf('tmp/alunos-estrangeiros-%s.csv', time());
        $this->dumpToFile($this->organizarDados(), $filename);
        return [
            "name" => "Relatório de Alunos Estrangeiros CSV",
            "path" => $filename,
            'pathExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function organizarDados()
    {
        $dadosImprimir = [];
        $numeroAlunos = 0;
        foreach ($this->dados as $dados) {
            $numeroAlunos += count($dados->alunosEstrangeiros);
        }
        foreach ($this->dados as $dados) {
            $dadosImprimir[] = utf8_encode($dados->escola);
            $dadosImprimir[] = 'Numero de Alunos: '. $numeroAlunos;
            $dadosImprimir[] = "Ano Letivo: {$dados->ano}";
            $dadosImprimir[] = $this->cabecalho;
            if (count($dados->alunosEstrangeiros) > 0) {
                foreach ($dados->alunosEstrangeiros as $aluno) {
                    $linha = [
                        $aluno->nome,
                        $aluno->sexo,
                        $aluno->dataNascimento,
                        $aluno->dataMatricula,
                        $aluno->dataTransferencia,
                        utf8_encode($aluno->etapa),
                        $aluno->percentualFrequencia . '%',
                    ];

                    $dadosImprimir[] = $linha;
                }
            } else {
                $dadosImprimir[] = 'NENHUM ALUNO ESTRANGEIRO NESTA ESCOLA';
            }
            $dadosImprimir[] = '';
        }
        return $dadosImprimir;
    }
}
