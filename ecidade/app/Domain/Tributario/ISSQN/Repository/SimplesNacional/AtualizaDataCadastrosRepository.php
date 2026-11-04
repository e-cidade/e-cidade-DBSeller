<?php

namespace App\Domain\Tributario\ISSQN\Repository\SimplesNacional;

class AtualizaDataCadastrosRepository
{
    /**
     * Metodo para salvar dados de dia e horario referente a atualizacao
     * automatica de cadastro simples nacional
     *
     * @param Integer $frequenciaAtualizacoes
     * @param Integer $diaDaSemana
     * @param Integer $diaDoMes
     * @param String $horario
     */
    public function setFrequenciaAtualizacoes($frequenciaAtualizacoes, $diaDoMes, $diaDaSemana, $horario)
    {
        $setFrequenciaAtualizacoes = $frequenciaAtualizacoes ?
            "q60_frequenciaatualizacaosimplesnacional = {$frequenciaAtualizacoes},"
            :
            '';
        $setDiaDoMes = $diaDoMes ?
            "q60_diamensalatualizacaosimplesnacional = {$diaDoMes},"
            :
            '';
        $setDiaDaSemana = $diaDaSemana ?
            "q60_diasemanalatualizacaosimplesnacional = {$diaDaSemana},"
            :
            '';
        $setHorario = $horario ?
            "q60_horaatualizacaosimplesnacional = '{$horario}',"
            :
            '';

        $setters = trim($setFrequenciaAtualizacoes . $setDiaDoMes . $setDiaDaSemana . $setHorario, ',');

        $atualizado = \DB::select(
            \DB::raw(
                "update parissqn 
                 set
                    {$setters} returning *"
            )
        );

        return $atualizado;
    }

    /**
     * Metodo para buscar dados de dia e horario referente a atualizacao
     * automatica de cadastro simples nacional
     */
    public function getHorarioAtualizacoes()
    {
        $resultado = \DB::table('parissqn')->select(
            'q60_frequenciaatualizacaosimplesnacional as frequencia',
            'q60_diamensalatualizacaosimplesnacional as diaDoMes',
            'q60_diasemanalatualizacaosimplesnacional as diaDaSemana',
            'q60_horaatualizacaosimplesnacional as horario'
        )->get()[0];

        return  $resultado;
    }

    /**
     * Metodo para realizar a validacao do usuario logado no sistema
     * a fim de definir se ele pode ou nao definir a frequencia de
     * atualizacao de cadastros
     *
     * @param String $idUsuario
     * @return Boolean
     */
    public function validaUsuarioLogado($idUsuario)
    {
        $resultado = \DB::select(\DB::raw("
            select
              case
                when id is null then false
                else true
              end as usuarioadministrador
            from
              (
                select
                  db_usuarios.id_usuario as id
                from
                  db_usuarios
                  inner join db_usuacgm on db_usuarios.id_usuario = db_usuacgm.id_usuario
                  inner join cgm on db_usuacgm.cgmlogin = cgm.z01_numcgm
                where
                  z01_cgccpf = '05238851000190'
                  and usuarioativo = 1
                  and usuext = 0
                  and administrador = 1
                  and db_usuarios.id_usuario = {$idUsuario}
              ) as buscaUsuarios
        "));

        $usuarioAdministrador = count($resultado) > 0
            ? (property_exists($resultado[0], 'usuarioadministrador')
                ? ($resultado[0]->usuarioadministrador
                    ? true
                    : false)
                : false)
            : false;

        return $usuarioAdministrador;
    }
}
