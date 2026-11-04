<?php

namespace App\Domain\Saude\Farmacia\Resources;

class InconsistenciaSaidaBnafarResource extends InconsistenciaBnafarResource
{
    /**
     * @param object $movimentacao
     * @return object
     */
    protected function toObject($movimentacao)
    {
        $data = parent::toObject($movimentacao);
        $data->data = db_formatar($movimentacao->data_saida, 'd');
        $data->tipoMovimentacao = self::campoToObject($movimentacao, 'movimentacao');
        $data->destino = $movimentacao->destino;
        $data->idDestino = self::campoToObject($movimentacao, 'id_estabelecimento');
        $data->nomeDestino = $movimentacao->nome_estabelecimento;

        return $data;
    }
}
