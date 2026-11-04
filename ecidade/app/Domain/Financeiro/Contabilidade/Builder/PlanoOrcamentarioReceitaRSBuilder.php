<?php

namespace App\Domain\Financeiro\Contabilidade\Builder;

use App\Domain\Financeiro\Contabilidade\Contracts\PlanoContasOrcamentarioInterface;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use Carbon\Carbon;
use Exception;

class PlanoOrcamentarioReceitaRSBuilder extends PlanoOrcamentarioReceitaBuilder
{
    protected $tipos = [
        1 => 'Principal',
        2 => 'Multas e Juros de Mora',
        3 => 'Dívida Ativa',
        4 => 'Multas e Juros de Mora da Dívida Ativa',
        5 => 'Multas',
        6 => 'Juros de Mora',
        7 => 'Multas da Dívida Ativa',
        8 => 'Juros de Mora da Dívida Ativa',
    ];

    public function build()
    {
        $dados = [];
        $dado = [];
        $colunasMapeadas = $this->layout->colunasMapper();
        foreach ($this->layout->colunasImportar() as $indexColuna) {
            $dado[$colunasMapeadas[$indexColuna]] = $this->linha[$indexColuna];
        }

        $dado['exercicio'] = $this->exercicio;
        $dado['uniao'] = $this->plano === PlanoContas::PLANO_UNIAO;
        $dado['conta'] = str_replace('.', '', $dado['conta']);
        $dado['nome'] = str_replace("\n", '', $dado['nome']);
        $dado['desdobramento4'] = $dado['desdobramento4'] === '' ? '00' : $dado['desdobramento4'];
        $dado['desdobramento5'] = $dado['desdobramento5'] === '' ? '00' : $dado['desdobramento5'];
        $dado['desdobramento6'] = $dado['desdobramento6'] === '' ? '00' : $dado['desdobramento6'];
        if (isset($dado['funcao'])) {
            $dado['funcao'] = str_replace("\n", '', $dado['funcao']);
        }
        if (isset($dado['sintetica'])) {
            $dado['sintetica'] = $this->sintetica($dado['sintetica']);
        }

        $dado['desdobramento2'] = str_pad($dado['desdobramento2'], 2, '0', STR_PAD_LEFT);
        $dado['desdobramento4'] = str_pad($dado['desdobramento4'], 2, '0', STR_PAD_LEFT);
        $dado['desdobramento5'] = str_pad($dado['desdobramento5'], 2, '0', STR_PAD_LEFT);
        $dado['desdobramento6'] = str_pad($dado['desdobramento6'], 2, '0', STR_PAD_LEFT);

        $dado['created_at'] = Carbon::now();
        $dado['updated_at'] = Carbon::now();

        $dado['classe'] = 4;
        if (strpos($dado['conta'], 'X')) {
            foreach ($this->tipos as $codigo => $tipo) {
                $copia = $dado;
                $copia['tipo'] = $codigo;
                $copia['conta'] = str_replace('X', $codigo, $copia['conta']);
                $copia['nome'] = str_replace('[TIPO]', $tipo, $copia['nome']);
                $dados[] = $copia;

                if ($dado['categoria'] != 9) {
                    $intra = $this->criaIntra($copia);
                    $dados[] = $intra;
                }
            }
        } else {
            $dados[] = $dado;

            if ($dado['categoria'] != 9) {
                $intra = $this->criaIntra($dado);
                $dados[] = $intra;
            }
        }

        return $dados;
    }
}
