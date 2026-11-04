<?php

namespace App\Domain\Saude\Farmacia\Resources;

class InconsistenciaDispensacaoBnafarResource extends InconsistenciaBnafarResource
{
    /**
     * @param object $movimentacao
     * @return object
     */
    protected function toObject($movimentacao)
    {
        $data = parent::toObject($movimentacao);
        $data->data = db_formatar($movimentacao->data_dispensacao, 'd');
        $data->descricao = 'ENTREGA DE MEDICAMENTO';
        $data->idPaciente = $movimentacao->id_paciente;
        $data->nomePaciente = $movimentacao->nome_paciente;
        $data->cpfPaciente = self::campoToObject($movimentacao, 'cpf_paciente');
        $data->cnsPaciente = self::campoToObject($movimentacao, 'cns_paciente');

        $data->cpfPaciente->valor = strlen($data->cpfPaciente->valor) === 11 ? $data->cpfPaciente->valor : '';
        $data->cnsPaciente->valor = strlen($data->cnsPaciente->valor) === 15 ? $data->cnsPaciente->valor : '';

        return $data;
    }
}
