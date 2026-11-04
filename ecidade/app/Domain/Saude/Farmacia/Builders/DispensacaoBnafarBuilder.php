<?php

namespace App\Domain\Saude\Farmacia\Builders;

class DispensacaoBnafarBuilder extends BnafarBuilder
{
    protected function buildBody()
    {
        return (object)[
            'codigo' => null,
            'usuarioSus' => $this->buildUsuarioSus(),
            'estabelecimentoDispensador' => $this->buildEstabelecimento(),
            'caracterizacao' => $this->buildCaracterizacao(),
            'itens' => $this->buildItens(20)
        ];
    }

    protected function buildEstabelecimento()
    {
        return (object)[
            'cnes' => $this->cnes
        ];
    }

    protected function buildCaracterizacao()
    {
        $dado = $this->dados->first();
        return (object)[
            'codigoOrigem' => $dado->codigo_origem,
            'dataDispensacao' => $dado->data_dispensacao
        ];
    }

    private function buildUsuarioSus()
    {
        $dado = $this->dados->first();
        if ($dado->cpf_paciente && strlen($dado->cpf_paciente) == 11) {
            return (object)['cpf' => $dado->cpf_paciente];
        }

        return (object)['cns' => $dado->cns_paciente];
    }
}
