<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_app::import("configuracao.DBEstrutura");
db_app::import("orcamento.TribunalEstrutura");
db_app::import("orcamento.Recurso");

$clruas = new cl_ruas;
$clruascep = new cl_ruascep;
$clcfiptu = new cl_cfiptu;
$clruastipo = new cl_ruastipo;
$oDaoRuasanterior = new cl_ruas;
$oDaoRuashistorico = new cl_ruashistorico();

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);

$oJson = new services_json();
$oParam = $oJson->decode(db_stdClass::db_stripTagsJson(str_replace("\\", "", $oPost->json)));
$iAnoUsu = db_getsession('DB_anousu');
/**
 * Instânciamos o ojeto que retorna se há erros ou não na operação
 */
$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->message = "";
//$oRetorno->aDados  = null;

if (isset($oParam->finalidaderecurso)) {
    $sFinalidadeRecurso = utf8_decode($oParam->finalidaderecurso);
}

switch ($oParam->sExec) {

    case "getCep":
        $sql = "select * from ruascep where j29_codigo = {$oParam->j29_codigo}";
        $rsDetalhe = db_query($sql);
        $oRetorno->aDados = array();

        if (pg_numrows($rsDetalhe) > 0) {
            for ($iDetalhe = 0; $iDetalhe < (pg_numrows($rsDetalhe)); $iDetalhe++) {
                $oRetorno->aDados[] = db_utils::fieldsMemory($rsDetalhe, $iDetalhe);
            }
        }
        break;
    case "buscaCepUnico":
        $SqlBuscaDetalhamento = "select * from ruascep WHERE j29_codigo = {$oParam->iIdCep} AND j29_inicio = {$oParam->iIdInicio}";
        $rsBuscaDetalhamento = db_query($SqlBuscaDetalhamento);
        $oRetorno->aDados = pg_fetch_object($rsBuscaDetalhamento);
        break;

    case "salvar":

        try {

            $sqlruascep = "SELECT * FROM ruascep WHERE j29_codigo = {$oParam->j29_codigo} AND j29_inicio = {$oParam->j29_inicio}";
            $resultruascep = db_query($sqlruascep);
            $linharuascep = pg_num_rows($resultruascep);

            if ($linharuascep > 0) {
                throw new Exception(" Nº Início: $oParam->j29_inicio, já cadastrado para o Cód Logradouro: $oParam->j29_codigo.");
            }

            $sql = "INSERT INTO ruascep (j29_codigo, j29_inicio, j29_final,j29_cep,j29_lado) VALUES ($oParam->j29_codigo,$oParam->j29_inicio,$oParam->j29_final,'$oParam->j29_cep',$oParam->j29_lado)";
            $rs = db_query($sql);
        } catch (Exception $ex) {
            $oRetorno->message = $ex->getMessage();
            $oRetorno->status = 0;
        }
        break;

    case "excluir":

        $sql = "DELETE FROM ruascep WHERE j29_codigo = {$oParam->iIdCep} AND j29_inicio = {$oParam->iIdInicio} ";
        $rs = db_query($sql);
        break;

    case "alterar":
        try {

            if (empty($oParam->j29_final)) {
                throw new \Exception("Campo Nº Final é obrigatório");
            }

            if (empty($oParam->j29_cep)) {
                throw new \Exception("Campo CEP é obrigatório");
            }


            /**
             * Salva os dados anteriores antes da alteração
             */
            $sSqlRuasAnterior = $oDaoRuasanterior->sql_query_file($oParam->j29_codigo);
            $rsRuasAnterior = $oDaoRuasanterior->sql_record($sSqlRuasAnterior);

            if ($oDaoRuasanterior->numrows > 0) {

                $oRuaAnterior = db_utils::fieldsMemory($rsRuasAnterior, 0);

                if (levenshtein($oRuaAnterior->j14_nome, $j14_nome) > 3) {
                    $oDaoRuashistorico->j136_ruas = $oRuaAnterior->j14_codigo;
                    $oDaoRuashistorico->j136_ruastipo = $oRuaAnterior->j14_tipo;
                    $oDaoRuashistorico->j136_lei = $oRuaAnterior->j14_lei;
                    $oDaoRuashistorico->j136_datalei = $oRuaAnterior->j14_dtlei;
                    $oDaoRuashistorico->j136_nomeanterior = $oRuaAnterior->j14_nome;
                    $oDaoRuashistorico->j136_dataalteracao = date('Y-m-d', db_getsession('DB_datausu'));
                    $oDaoRuashistorico->incluir(null);

                    if ($oDaoRuashistorico->erro_status == '0') {
                        $sqlerro = true;
                        $erro_msg = $oDaoRuashistorico->erro_msg;
                    }
                }


            }

            $clruas->alterar($oParam->j29_codigo);

            $sql = "UPDATE ruascep SET j29_final = {$oParam->j29_final}, j29_cep='{$oParam->j29_cep}' , j29_lado ={$oParam->j29_lado}   WHERE j29_codigo = {$oParam->j29_codigo} AND j29_inicio = {$oParam->j29_inicio}  ";
            $rs = db_query($sql);
        } catch (\Exception $ex) {
            $oRetorno->message = $ex->getMessage();
            $oRetorno->status = 0;
        }

        break;
}
echo JSON::create()->stringify($oRetorno);
