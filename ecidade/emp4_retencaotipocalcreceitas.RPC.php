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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oJson               = JSON::create();
$oRetorno            = new stdClass();
$oParam              = $oJson->parse(str_replace("\\","",$_POST["json"]));
$oRetorno->erro      = false;
$iCodigoInstituicao  = db_getSession("DB_instit");

try {

    db_inicio_transacao();

    switch ($oParam->exec) {

        case 'getConfiguracoesReceitas':

            $oDaoRetencaotipocalcreceiras = new cl_retencaotipocalcreceitas();
            $campos = "e17_sequencial as id, k02_codigo as codigo_receita, k02_descr as descricao_receita, e32_descricao as descricao_tipocalculo ";
            $sWhere = "e17_instit = {$iCodigoInstituicao} and e17_retencaotipocalc = {$oParam->oParams->iTipocalc}";
            $sSql   = $oDaoRetencaotipocalcreceiras->sql_query(null, $campos, null, $sWhere);
            $rsTipocalcreceitas = db_query($sSql);
            if ( ! $rsTipocalcreceitas ) {
                throw new Exception("Ocorreu algo inesperado ao buscar as configurações da receita por tipo de calculo de retenção.");
            }

            $oRetorno->aConfiguracoes = db_utils::getCollectionByRecord($rsTipocalcreceitas);

            break;

        case 'salvar':

            $oRetorno->erro    = false;
            $oRetorno->message = "Inclusão efetuada com sucesso.";
            $oDaoRetencaotipocalcreceiras = new cl_retencaotipocalcreceitas();

            $sWhere = "e17_instit = {$iCodigoInstituicao} and e17_retencaotipocalc = {$oParam->oParams->codigo_tipocalculo} and e17_receit = {$oParam->oParams->codigo_receita}";
            $sSql   = $oDaoRetencaotipocalcreceiras->sql_query_file(null,"*",null, $sWhere);
            $rsValidaReceita = db_query($sSql);
            if (pg_num_rows($rsValidaReceita) > 0) {
                throw new \Exception("Receita ja incluida para o tipo de calculo selecionado.");
            }

            $oDaoRetencaotipocalcreceiras->e17_instit = $iCodigoInstituicao;
            $oDaoRetencaotipocalcreceiras->e17_receit = $oParam->oParams->codigo_receita;
            $oDaoRetencaotipocalcreceiras->e17_retencaotipocalc = $oParam->oParams->codigo_tipocalculo;
            $oDaoRetencaotipocalcreceiras->incluir();
            if ($oDaoRetencaotipocalcreceiras->erro_status == '0') {
                throw new \Exception("Ocorreu algo inesperado ao incluir a configuração da receita por tipo de calculo de retenção. {$oDaoRetencaotipocalcreceiras->erro_msg}");
            }
            break;

        case 'excluir':

            $oRetorno->erro    = false;
            $oRetorno->message = "Exclusão efetuada com sucesso.";
            $oDaoRetencaotipocalcreceiras = new cl_retencaotipocalcreceitas();

            $oDaoRetencaotipocalcreceiras->excluir($oParam->oParams->id);
            if ($oDaoRetencaotipocalcreceiras->erro_status == '0') {
                throw new \Exception("Ocorreu algo inesperado ao incluir a configuração da receita por tipo de calculo de retenção. {$oDaoRetencaotipocalcreceiras->erro_msg}");
            }
            break;
    }

    db_fim_transacao(false);

} catch (Exception $e) {

    db_fim_transacao(true);
    $oRetorno->erro    = true;
    $oRetorno->message = $e->getMessage();
}
echo $oJson->stringify($oRetorno);