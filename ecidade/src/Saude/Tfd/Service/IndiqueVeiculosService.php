<?php

namespace ECidade\Saude\Tfd\Service;

use ECidade\Saude\Tfd\Helper\ParametrosHelper;
use Exception;

class IndiqueVeiculosService
{
    /**
     * @param object $parametros
     * @return object
     * @throws Exception
     */
    public function salvarHoraSaida($parametros)
    {
        $retorno = (object)[
            'ids' => [],
            'cgs' => []
        ];

        $agendamentos = $this->getAgendamentos($parametros->data, $parametros->passageiros);
        if (!$this->validar($parametros, $agendamentos)) {
            return $retorno;
        }

        if (property_exists($parametros, 'idVeiculoDestino') && $parametros->idVeiculoDestino) {
            $this->invalidarPassageiros($parametros->idVeiculoDestino, $retorno);
        }

        foreach ($agendamentos as $id) {
            $this->alterarHorarioSaidaAgendamento($id, $parametros->hora);
        }

        return $retorno;
    }

    /**
     * @param string $dataSaida
     * @param array $passageiros
     * @return array
     */
    private function getAgendamentos($dataSaida, array $passageiros)
    {
        $passageiros = implode(', ', $passageiros);
        $dao = new \cl_tfd_agendasaida();
        $where = [];
        $where[] = "tf17_d_datasaida = '{$dataSaida}'";
        $where[] = "tf01_i_cgsund in ({$passageiros})";
        $where[] = "not exists (
            select 1 from tfd_situacaopedidotfd where tf28_i_pedidotfd = tf01_i_codigo and tf28_i_situacao != 1
        )";
        $sql = $dao->sql_query2('', 'tf17_i_codigo', '', implode(' AND ', $where));
        $rs = $dao->sql_record($sql);
        if ($dao->numrows == 0) {
            return [];
        }

        return \db_utils::makeCollectionFromRecord($rs, function ($dados) {
            return $dados->tf17_i_codigo;
        });
    }

    /**
     * @param object $parametros
     * @param array $agendamentos
     * @return bool
     * @throws Exception
     */
    private function validar($parametros, array $agendamentos)
    {
        if (ParametrosHelper::get()->obrigaHoraSaida || $parametros->hora == '' || !count($parametros->passageiros)) {
            return false;
        }
        if (!count($agendamentos)) {
            return false;
        }

        $dao = new \cl_tfd_veiculodestino();
        $whereOr = [];
        $whereOr[] = "tf18_i_veiculo = {$parametros->idVeiculo}";
        if ($parametros->idMotorista) {
            $whereOr[] = "tf18_i_motorista = {$parametros->idMotorista}";
        }
        $whereOr = implode(' OR ', $whereOr);

        $where = [];
        if (property_exists($parametros, 'idVeiculoDestino') && $parametros->idVeiculoDestino) {
            $where[] = "tf18_i_codigo != {$parametros->idVeiculoDestino}";
        }

        $where[] = "({$whereOr})";
        $where[] = "'{$parametros->data}' BETWEEN tf18_d_datasaida and tf18_d_dataretorno";
        $where[] = "'{$parametros->hora}'::time BETWEEN tf18_c_horasaida::time and tf18_c_horaretorno::time";
        $where[] = "not exists (select 1 from cancelamentoviagem where tf41_veiculodestino = tf18_i_codigo)";
        $where = implode(' AND ', $where);
        $sql = $dao->sql_query_file('', '1', '', $where);
        $dao->sql_record($sql);
        if ($dao->numrows > 0) {
            throw new Exception('O veículo/motorista já possui agendamento no horario informado.');
        }

        return true;
    }

    /**
     * @param integer $idVeiculoDestino
     * @param object $retorno
     * @throws Exception
     */
    private function invalidarPassageiros($idVeiculoDestino, &$retorno)
    {
        $dao = new \cl_tfd_passageiroveiculo;
        $where = "tf19_i_veiculodestino = {$idVeiculoDestino} and tf19_i_valido = 1";
        $sql = $dao->sql_query_file('', 'tf19_i_codigo, tf19_i_cgsund', '', $where);
        $rs = $dao->sql_record($sql);
        if ($dao->numrows == 0) {
            return;
        }
        foreach (\db_utils::getCollectionByRecord($rs) as $dados) {
            $dao->tf19_i_valido = 2;
            $dao->tf19_i_codigo = $dados->tf19_i_codigo;
            $dao->alterar($dados->tf19_i_codigo);
            if ($dao->erro_status == '0') {
                throw new Exception($dao->erro_msg);
            }
            $retorno->cgs[] = $dados->tf19_i_cgsund;
            $retorno->ids[] = $dados->tf19_i_codigo;
        }
    }

    /**
     * @param integer $id
     * @param string $horaSaida
     * @throws Exception
     */
    private function alterarHorarioSaidaAgendamento($id, $horaSaida)
    {
        $dao = new \cl_tfd_agendasaida();
        $dao->tf17_c_horasaida = $horaSaida;
        $dao->tf17_i_codigo = $id;
        $dao->alterar($id);
        if ($dao->erro_status == '0') {
            throw new Exception($dao->erro_msg);
        }
    }
}
