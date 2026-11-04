<?php

namespace App\Domain\Saude\Farmacia\Builders;

class SaidaBnafarBuilder extends BnafarBuilder
{
    protected function buildCaracterizacao()
    {
        $tiposSaida = [
            '1' => 'S-D', // Doação
            '2' => 'S-PE', // Perda
            '3' => 'S-AE', // Ajuste de estoque
            '7' => 'S-TR', // Transferência/remanejamento
            '9' => 'S-VV', // Validade vencida
            '10' => 'S-DD', // Distribuição
            '11' => 'S-EE', // Devolução de empréstimo
            '12' => 'S-E', // Empréstimo
            '13' => 'S-AS', // Apreensão sanitária
            '14' => 'S-AEA', // Amostra, exposição e análise
            '15' => 'S-DEP' // Devolução de entrada de produto
        ];
        $dado = $this->dados->first();
        $tipoSaida = array_key_exists($dado->movimentacao, $tiposSaida) ? $tiposSaida[$dado->movimentacao] : '';

        $estabelecimentoDestino = $dado->cnpj_estabelecimento;
        if (empty($estabelecimentoDestino)) {
            $estabelecimentoDestino = $dado->cnes_estabelecimento;
        }

        return (object)[
            'codigoOrigem' => $dado->codigo_origem,
            'dataSaida' => $dado->data_saida,
            'estabelecimentoDestino' => $estabelecimentoDestino,
            'tipoSaida' => $tipoSaida
        ];
    }
}
