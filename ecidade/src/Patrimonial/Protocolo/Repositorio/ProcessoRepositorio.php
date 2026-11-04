<?php

namespace ECidade\Patrimonial\Protocolo\Repositorio;

use db_utils;
use DBException;
use ECidade\Patrimonial\Protocolo\Modelo\Processo;

/**
 * Class ProcessoRepositorio
 * @package ECidade\Patrimonial\Protocolo\Repositorio
 */
class ProcessoRepositorio
{

    /**
     * @var integer
     */
    const INSTITUICAO = 1;

    /**
     * @var integer
     */
    const ITEM_MENU   = 2175; //    2182

    /**
     * @var integer
     */
    const MODULO_MENU = 604;

    /**
     * @param $codigo
     * @return Processo
     * @throws DBException
     */
    public static function encontrar($codigo)
    {
        $query = "SELECT * FROM ultimas_movimentacoes_processos_vencidos WHERE codigo_processo = {$codigo}";

        $resultado = db_query($query);

        if (!$resultado) {
            throw new DBException('Não foi possível buscar o processo ' . $codigo);
        }

        $resultado = db_utils::fieldsMemory($resultado, 0);

        return new Processo(
            $resultado->codigo_processo,
            $resultado->data_criacao,
            $resultado->ultima_data,
            $resultado->ultima_hora,
            $resultado->descricao_departamento,
            $resultado->nome,
            $resultado->login,
            $resultado->assunto,
            $resultado->codigo_departamento,
            $resultado->codigo_usuario,
            $resultado->numero_processo,
            $resultado->ano_processo
        );
    }

    /**
     * Retorna tipo do prazo do envio, se busca fixo ou pega do andamento padrao
     *
     * @return mixed
     * @throws DBException
     */
    public static function getTipoEnvioMensageria()
    {
        $rsMensageriaProcesso = db_query("SELECT * FROM mensageriaprocesso LIMIT 1");

        if (!$rsMensageriaProcesso) {
            throw new DBException('Não foi possível buscar  tipo do prazo.');
        }

        $oNotificacao = db_utils::fieldsMemory($rsMensageriaProcesso, 0);

        return $oNotificacao->p101_tipoprazo;
    }

    /**
     * Methodo que busca no banco todos processos vencidos
     *
     * @return array
     * @throws DBException
     */
    public static function vencidos()
    {

        $tipoEscolha = self::getTipoEnvioMensageria();

        $diasPadrao = "";

        $tipoPadrao = "(
                SELECT p101_diasprazo 
                FROM mensageriaprocesso 
                LIMIT 1
            )";

        if ($tipoEscolha) {
            $tipoPadrao = "
                 (CASE
                     WHEN (coalesce(
                                      (SELECT p53_dias
                                       FROM andpadrao
                                       WHERE p53_codigo = p58_codigo
                                         AND p53_coddepto = codigo_departamento
                                       LIMIT 1), 0) > 0) THEN
                            (SELECT p53_dias
                             FROM andpadrao
                             WHERE p53_codigo = p58_codigo
                               AND p53_coddepto = codigo_departamento
                             LIMIT 1)
                     ELSE {$tipoPadrao}
                 END)
           ";

            $diasPadrao = "," . $tipoPadrao . " AS prazodias";
        }

        $query = "
            SELECT * {$diasPadrao}
            FROM ultimas_movimentacoes_processos_vencidos
            WHERE TO_CHAR(NOW(), 'YYYY-MM-DD') = TO_CHAR(ultima_data + {$tipoPadrao}, 'YYYY-MM-DD');
        ";

        $resultado = db_query($query);

        if (!$resultado) {
            throw new DBException('Não foi possível buscar os processos vencidos.');
        }

        return db_utils::makeCollectionFromRecord($resultado, function ($processoRaw) {
            return new Processo(
                $processoRaw->codigo_processo,
                $processoRaw->data_criacao,
                $processoRaw->ultima_data,
                $processoRaw->ultima_hora,
                $processoRaw->descricao_departamento,
                $processoRaw->nome,
                $processoRaw->login,
                $processoRaw->assunto,
                $processoRaw->codigo_departamento,
                $processoRaw->codigo_usuario,
                $processoRaw->numero_processo,
                $processoRaw->ano_processo,
                (isset($processoRaw->prazodias) ? $processoRaw->prazodias : null)
            );
        });
    }

    public static function chunkByDepartamento(
        $sTabela,
        $aDepartamentos,
        \Closure $fRetorno,
        $aCampos = array('*'),
        $aFiltros = array(),
        $aOrdem = array()
    ) {
        $sCampos = implode(', ', $aCampos);
        $sDepartamentos = implode(', ', $aDepartamentos);
        $sQuery = "SELECT {$sCampos} FROM {$sTabela} ";
        $aDepartamentosSelecionados = array();

        if ($aDepartamentos || $aFiltros) {
            $sQuery .= ' WHERE ';
        }

        self::construirFiltro($aFiltros, $sQuery);

        if ($aDepartamentos) {
            $sQuery .= " codigo_departamento IN ({$sDepartamentos})";
        }

        if ($aOrdem) {
            $sQuery .= ' ORDER BY ' . implode(', ', $aOrdem);
        }

        $rsProcessos = db_query($sQuery);

        if (!$rsProcessos) {
            throw new DBException('Não foi possível buscar os processos vencidos.');
        }

        db_utils::makeCollectionFromRecord($rsProcessos, function ($oProcesso) use (&$aDepartamentosSelecionados) {
            $aDepartamentosSelecionados[$oProcesso->codigo_departamento][] = $oProcesso;
        });

        $aDepartamentosNaoEncontrados = array_diff($aDepartamentos, array_keys($aDepartamentosSelecionados));

        foreach ($aDepartamentosNaoEncontrados as $iDepartamentoNaoEncontrado) {
            $aDepartamentosSelecionados[$iDepartamentoNaoEncontrado] = array();
        }

        foreach ($aDepartamentosSelecionados as $iDepartamento => $aProcessos) {
            $oDepartamento = new \DBDepartamento($iDepartamento);
            $fRetorno($aProcessos, $oDepartamento);
            unset($aProcessos);
        }

        return true;
    }

    public static function totalVencidosPorDepartamento($aDepartamentos, $aFiltros)
    {
        $sDepartamentos = implode(', ', $aDepartamentos);

        $sQuery = '';

        self::construirFiltro($aFiltros, $sQuery);

        $sQuery = "
            SELECT COUNT(codigo_processo) AS total, 
                   codigo_departamento, 
                   descricao_departamento,
                   (SELECT ARRAY_TO_JSON(ARRAY_AGG(DISTINCT db_usuarios.nome)) 
                    FROM gestaodepartamentoprocesso
                    LEFT JOIN db_usuarios ON id_usuario = p103_db_usuarios 
                    WHERE p103_db_depart = codigo_departamento) AS responsaveis
            FROM ultimas_movimentacoes_processos_vencidos
            WHERE {$sQuery} codigo_departamento IN ({$sDepartamentos})
            GROUP BY codigo_departamento, descricao_departamento
            ORDER BY descricao_departamento
        ";

        $rsVencidos = db_query($sQuery);

        if (!$rsVencidos) {
            throw new DBException('Erro ao buscar os processos vencidos do(s) departamento(s).');
        }

        return db_utils::getCollectionByRecord($rsVencidos);
    }

    public static function getDiasAndamentoPadrao($iTipoCodigo, $iDepartamento)
    {
        if (empty($iTipoCodigo) || empty($iDepartamento)) {
            return null;
        }

        $sQuery = "SELECT * FROM  andpadrao WHERE  p53_codigo = {$iTipoCodigo}  AND  ";
        $sQuery .= "p53_coddepto = {$iDepartamento} order by p53_ordem asc";

        $rsAndamentoPadrao = db_query($sQuery);

        if (!$rsAndamentoPadrao) {
            throw new DBException('Erro ao buscar o andamento padrao.');
        }

        return db_utils::getCollectionByRecord($rsAndamentoPadrao);
    }

    private static function construirFiltro($aFiltros, &$sQuery)
    {
        $iContadorFiltros = 1;
        $iQuantidadeFiltros = count($aFiltros);

        foreach ($aFiltros as $sCampo => $sValor) {
            if (is_array($sValor)) {
                $sCampoTabela = $sValor[0];
                $sOperador = $sValor[1];
                $sValorCampo = $sValor[2];
            } else {
                $sCampoTabela = $sCampo;
                $sOperador = '=';
                $sValorCampo = $sValor;
            }

            $sQuery .= " {$sCampoTabela} {$sOperador} {$sValorCampo}";

            if ($iContadorFiltros <= $iQuantidadeFiltros) {
                $sQuery .= ' AND';
            }

            $iContadorFiltros++;
        }
    }

    /**
     * Busca
     *
     * @param $iCodigoDepartamento
     * @return array
     */
    public static function getUsersDepartmentByPermissionMenuReceive($iCodigoDepartamento)
    {

        $anousu  = date("Y");

        $modulo  = self::MODULO_MENU;  // statico dicionario de dados
        $item    = self::ITEM_MENU; // statico dicionario de dados item de menu do Recebimento processo

        $instit  = self::INSTITUICAO;

        $sSql  = " SELECT * FROM db_permissao";
        $sSql .=" INNER JOIN db_usuarios ON db_usuarios.id_usuario  = db_permissao.id_usuario ";
        $sSql .=" INNER JOIN db_depusu   ON  db_usuarios.id_usuario = db_depusu.id_usuario ";

        $sWherePermissao  = " coddepto       = {$iCodigoDepartamento}";
        $sWherePermissao .= "  and id_item        = {$item}";
        $sWherePermissao .= " and anousu     = {$anousu}";
        $sWherePermissao .= " and id_instit  = {$instit}";
        $sWherePermissao .= " and id_modulo  = {$modulo}";

        $sSql .= 'WHERE  ' . $sWherePermissao;

        $rsPermissao = db_query($sSql);

        $return  = pg_fetch_all($rsPermissao);

        return $return;
    }


    /**
     * @param $codigoDepartamento
     * @param array $codigosMenu
     * @return \stdClass[]
     * @throws DBException
     */
    public static function getUsuariosComAcessoEmDepartamentoMenu($codigoDepartamento, array $codigosMenu = array(2182))
    {

        $resBuscaUsuarios = db_query("
            select x.*
              from (
                   select distinct U.id_usuario, nome, login
                     from db_usuarios U
                          inner join db_depusu D    on U.id_usuario  = D.id_usuario
                          inner join db_permissao P on U.id_usuario  = P.id_usuario
                                                   and P.id_item     in (".implode(',', $codigosMenu).")
                    where D.coddepto = {$codigoDepartamento} and  usuarioativo = 1
                   union
                   select distinct U.id_usuario, nome, login
                     from db_usuarios U
                          inner join db_depusu D    on U.id_usuario  = D.id_usuario
                          inner join db_permherda H on U.id_usuario  = H.id_usuario
                          inner join db_permissao P on P.id_usuario  = H.id_perfil
                                                   and P.id_item     in (".implode(',', $codigosMenu).")
                    where D.coddepto = {$codigoDepartamento} and usuarioativo = 1) as x
            order by nome
        ");

        if (!$resBuscaUsuarios) {
            $sMsg = "Não foi possível consultar os usuarios com permissão de acesso ao menu e departamento.";
            throw new \DBException($sMsg);
        }
        return \db_utils::getCollectionByRecord($resBuscaUsuarios);
    }
}
