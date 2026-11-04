<?php

namespace App\Domain\Tributario\ISSQN\Services\SimplesNacional;

use App\Domain\Tributario\ISSQN\Repository\SimplesNacional\AtualizaDataCadastrosRepository;

class AtualizaDataCadastrosService
{
    private $repository;

    public function __construct(AtualizaDataCadastrosRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Metodo para salvar dados de dia e horario referente a atualizacao
     * automatica de cadastro simples nacional
     *
     * @param Integer $frequenciaAtualizacoes
     * @param Integer $diaDaSemana
     * @param Integer $diaDoMes
     * @param String $horario
     * @param String $idUsuario
     */
    public function setFrequenciaAtualizacoes(
        $frequenciaAtualizacoes,
        $diaDoMes,
        $diaDaSemana,
        $horario,
        $idUsuario
    ) {
        $usuarioAdministrador = $this->repository->validaUsuarioLogado($idUsuario);

        if (!$usuarioAdministrador) {
            return;
        }

        return $this->repository->setFrequenciaAtualizacoes(
            $frequenciaAtualizacoes,
            $diaDoMes,
            $diaDaSemana,
            $horario
        );
    }

    /**
     * Metodo para buscar dados de dia e horario referente a atualizacao
     * automatica de cadastro simples nacional
     *
     * @param String $idUsuario
     */
    public function getHorarioAtualizacoes($idUsuario)
    {
        $retorno = $this->repository->getHorarioAtualizacoes();
        $usuarioAdministrador = $this->repository->validaUsuarioLogado($idUsuario);
        $retorno->usuarioAdministrador = $usuarioAdministrador;

        return $retorno;
    }
}
