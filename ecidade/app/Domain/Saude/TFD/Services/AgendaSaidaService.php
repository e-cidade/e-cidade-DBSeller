<?php

namespace App\Domain\Saude\TFD\Services;

use App\Domain\Saude\TFD\Factories\ViagensPorMotoristaFactory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Classe responsável pelas regras de negócios relacionadas a tabela tfd_agendasaida
 * @package App\Domain\Saude\TFD\Services
 */
class AgendaSaidaService
{
    /**
     * Monta os dados para o relatório agrupando as viagens por motorista
     * @param Collection $viagens
     * @throws Exception
     * @return \App\Domain\Saude\TFD\Contracts\ViagensPorMotorista
     */
    public function gerarRelatorioViagensPorMotorista(Collection $viagens, $tipo = '')
    {
        $dados = [];

        foreach ($viagens as $viagem) {
            $passageiros = $viagem->pedido->passageiros;
            foreach ($passageiros as $passageiro) {
                $veiculoDestino = $passageiro->veiculoDestino;
                if (!isset($veiculoDestino->motorista)) {
                    continue;
                }

                if (!array_key_exists($veiculoDestino->tf18_i_motorista, $dados)) {
                    $dados[$veiculoDestino->tf18_i_motorista] = (object)[
                        'id' => $veiculoDestino->motorista->ve05_codigo,
                        'nome' => $veiculoDestino->motorista->cgm->z01_nome,
                        'viagens' => [],
                    ];
                }

                $veiculo = $veiculoDestino->veiculo;
                $index = "{$viagem->tf17_d_datasaida}{$veiculoDestino->tf18_i_destino}{$veiculo->ve01_placa}";
                $viagens = $dados[$veiculoDestino->tf18_i_motorista]->viagens;
                if (!array_key_exists($index, $viagens)) {
                    $viagens[$index] = (object)[
                        'destino' => $veiculoDestino->destino->tf03_c_descr,
                        'data' => db_formatar($viagem->tf17_d_datasaida, 'd'),
                        'veiculo' => $veiculo->modelo->ve22_descr,
                        'placa' => $veiculo->ve01_placa,
                        'passageiros' => 0,
                        'km' => $veiculoDestino->destino->tf03_f_distancia
                    ];
                }
                $viagens[$index]->passageiros++;
                $dados[$veiculoDestino->tf18_i_motorista]->viagens = $viagens;
                unset($viagens);
            }
        }

        usort($dados, function ($a, $b) {
            return strcmp($a->nome, $b->nome);
        });

        return ViagensPorMotoristaFactory::getRelatorio($tipo, $dados);
    }
}
