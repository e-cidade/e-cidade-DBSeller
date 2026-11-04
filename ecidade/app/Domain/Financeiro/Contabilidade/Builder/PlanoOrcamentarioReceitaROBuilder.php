<?php

namespace App\Domain\Financeiro\Contabilidade\Builder;

use App\Domain\Financeiro\Contabilidade\Contracts\PlanoContasOrcamentarioInterface;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use Carbon\Carbon;
use Exception;

class PlanoOrcamentarioReceitaROBuilder extends PlanoOrcamentarioReceitaBuilder
{
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
        $dados[] = $dado;
        $dados[] = $this->criaDeducao($dado);

        return $dados;
    }
}
