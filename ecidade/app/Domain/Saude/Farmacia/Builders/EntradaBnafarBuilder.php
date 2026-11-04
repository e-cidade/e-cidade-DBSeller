<?php

namespace App\Domain\Saude\Farmacia\Builders;

class EntradaBnafarBuilder extends BnafarBuilder
{
    protected function buildCaracterizacao()
    {
        $tiposEntrada = [
            '1' => 'E-D', // Doação
            '3' => 'E-AE', // Ajuste de estoque
            '4' => 'E-EVENTUAL', // Eventual
            '5' => 'E-O', // Entrada ordinária
            '6' => 'E-PER', // Permuta
            '7' => 'E-T', // Transferência/remanejamento
            '8' => 'E-SI' // Saldo de implantação
        ];
        $dado = $this->dados->first();

        $distribuidor = $dado->cnes_estabelecimento;
        if (empty($distribuidor)) {
            $distribuidor = $dado->cnpj_estabelecimento;
        }

        $numeroDocumento = $dado->numero_documento;
        if ($numeroDocumento == '' && $dado->movimentacao == '7') {
            $numeroDocumento = $dado->codigo_transferencia;
        }

        $tipoEntrada = array_key_exists($dado->movimentacao, $tiposEntrada) ? $tiposEntrada[$dado->movimentacao] : '';

        return (object)[
            'cnesCnpjDistribuidor' => $distribuidor,
            'codigoOrigem' => $dado->codigo_origem,
            'dataEntrada' => $dado->data_entrada,
            'numeroDocumento' => $numeroDocumento,
            'tipoEntrada' => $tipoEntrada
        ];
    }

    protected function buildItem($item)
    {
        $data = parent::buildItem($item);
        $data->valorUnitario = $item->valor_unitario;

        return $data;
    }
}
