<?php

namespace App\Domain\Saude\Farmacia\Resources;

class InconsistenciaEntradaBnafarResource extends InconsistenciaBnafarResource
{
    /**
     * @param object $movimentacao
     * @return object
     */
    protected function toObject($movimentacao)
    {
        $data = parent::toObject($movimentacao);
        $data->data = db_formatar($movimentacao->data_entrada, 'd');
        $data->tipoMovimentacao = self::campoToObject($movimentacao, 'movimentacao');
        $data->origem = $movimentacao->origem;
        $data->idOrigem = self::campoToObject($movimentacao, 'id_estabelecimento');
        $data->nomeOrigem = $movimentacao->nome_estabelecimento;
        $data->nota = self::campoToObject($movimentacao, 'numero_documento');
        $data->dataNota = db_formatar($movimentacao->data_documento, 'd');
        $data->codigoTransferencia = $movimentacao->codigo_transferencia;
        $data->dataTransferencia = db_formatar($movimentacao->data_transferencia, 'd');

        return $data;
    }
}
