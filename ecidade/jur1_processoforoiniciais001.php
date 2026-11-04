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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("classes/db_processoforoinicial_classe.php"));

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clprocessoforoinicial     = new cl_processoforoinicial;
$daoHonorariosParcelamento = new cl_honorariosparcelamento();
$db_opcao              = 2;
$db_botao              = false;
$lSqlErro              = false;

if (isset($oPost->incluir)) {

    if (!$lSqlErro) {

        db_inicio_transacao();

        /**
         * Verificamos se a inicial já está vinculada a algum processo ativo;
         */
        $sSqlVerificaInicial = $clprocessoforoinicial->sql_query(null,
                                      "v70_codforo,
                                                                  v70_sequencial",
          null,
          "v71_inicial = {$oPost->v71_inicial}
                                                                  and v71_anulado is false"
        );
        $rsVerificaInicial = $clprocessoforoinicial->sql_record($sSqlVerificaInicial);
        if ($clprocessoforoinicial->numrows > 0) {

            $oDadosProcesso = db_utils::fieldsMemory($rsVerificaInicial, 0);
            $sMsgErro    = "Inicial {$oPost->v71_inicial} já vinculada ao processo {$oDadosProcesso->v70_codforo}";
            $sMsgErro   .= "(Código Processo sistema: {$oDadosProcesso->v70_sequencial})";
            $lSqlErro   = true;
        } else {
            $clprocessoforoinicial->v71_id_usuario   = db_getsession('DB_id_usuario');
            $clprocessoforoinicial->v71_inicial      = $oPost->v71_inicial;
            $clprocessoforoinicial->v71_processoforo = $oPost->v71_processoforo;
            $clprocessoforoinicial->v71_data         = date('Y-m-d', db_getsession('DB_datausu'));
            $clprocessoforoinicial->v71_anulado      = 'false';
            $clprocessoforoinicial->incluir(null);
            $sMsgErro   = $clprocessoforoinicial->erro_msg;
            if ($clprocessoforoinicial->erro_status == 0) {
                $lSqlErro = true;
            }

            $v71_sequencial = $clprocessoforoinicial->v71_sequencial;
        }

        ###########################################

        //DELETA A INICIAL DA TABELA
        $daoHonorariosParcelamento->excluir(null, "ar43_inicial = {$oPost->v71_inicial}");

        if($daoHonorariosParcelamento->erro_status == '0') 
        {
            $lSqlErro = true;
            $sMsgErro = $daoHonorariosParcelamento->erro_msg;
        }

        //VERIFICA SE JÁ EXISTE O PROCESSO NA TABELA
        $sql      = $daoHonorariosParcelamento->sql_query_file(null, "ar43_sequencial", null, "ar43_processoforo = {$oPost->v71_processoforo}");
        $processo = db_query($sql);

        //SE O PROCESSO NÃO EXISTIR, INSERE A INICIAL
        if(pg_num_rows($processo) == 0)
        {
          $daoHonorariosParcelamento->ar43_sequencial = null;
          $daoHonorariosParcelamento->ar43_processoforo = $oPost->v71_processoforo;
          $daoHonorariosParcelamento->ar43_inicial = null;
          $daoHonorariosParcelamento->ar43_numeroparcelas = 1;
          $daoHonorariosParcelamento->incluir(null);

          if($daoHonorariosParcelamento->erro_status == '0') 
          {
            $lSqlErro = true;
            $sMsgErro = $daoHonorariosParcelamento->erro_msg;
          }
        }

        ###########################################

        db_fim_transacao($lSqlErro);
    }
} else if (isset($oPost->excluir)) {

    if (!$lSqlErro) {

        db_inicio_transacao();

        $clprocessoforoinicial->v71_sequencial   = $oPost->v71_sequencial;
        $clprocessoforoinicial->v71_anulado      = 'true';
        $clprocessoforoinicial->alterar($clprocessoforoinicial->v71_sequencial);

        /*$sMsgErro   = "Usuário: \\n";
        $sMsgErro  .= " Inicial Anulada com Sucesso \\n";
        $sMsgErro  .= " Valores : {$clprocessoforoinicial->v71_sequencial} ";*/

        $oParms = new stdClass();
        $oParms->iProcessoForoInicial = $clprocessoforoinicial->v71_sequencial;
        $sMsgErro = _M('tributario.juridico.db_frmprocessoforoinicial.anulada_com_sucesso', $oParms);

        if ($clprocessoforoinicial->erro_status == 0) {

            $sMsgErro = $clprocessoforoinicial->erro_msg;
            $lSqlErro = true;
        }

        ###########################################

        //VERIFICAR SE EXISTE MAIS DE UMA INICIAL NO PROCESSO ALÉM DA QUE VAI SER EXCLUIDA
        $where  = "v71_processoforo = {$oPost->v71_processoforo}"; 
        $where .= "AND v71_inicial  != {$oPost->v71_inicial}";

        $sql             = $clprocessoforoinicial->sql_query_file(null, "v71_sequencial", null, $where);
        $inicialProcesso = db_query($sql);

        //SE NÃO TIVER MAIS DE UMA INICIAL ALÉM DA QUE VAI SER EXCLUIDA, EXCLUI O PROCESSO
        if(pg_num_rows($inicialProcesso) == 0)
        {
            $daoHonorariosParcelamento->excluir(null, "ar43_processoforo = {$oPost->v71_processoforo}");

            if($daoHonorariosParcelamento->erro_status == '0') 
            {
                $lSqlErro = true;
                $sMsgErro = $daoHonorariosParcelamento->erro_msg;
            }
        }

        //INSERE A INICIAL
        $daoHonorariosParcelamento->ar43_sequencial = null;
        $daoHonorariosParcelamento->ar43_processoforo = null;
        $daoHonorariosParcelamento->ar43_inicial = $oPost->v71_inicial;
        $daoHonorariosParcelamento->ar43_numeroparcelas = 1;
        $daoHonorariosParcelamento->incluir(null);

        if($daoHonorariosParcelamento->erro_status == '0') 
        {
          $lSqlErro = true;
          $sMsgErro = $daoHonorariosParcelamento->erro_msg;
        }

        ###########################################

        db_fim_transacao($lSqlErro);
    }
} else if(isset($oPost->opcao)) {

    $sWhere                  = "v71_sequencial = {$v71_sequencial}";
    $sSqlProcessoForoInicial = $clprocessoforoinicial->sql_query(null, "*", null, $sWhere);
    $rsProcessoForoInicial   = $clprocessoforoinicial->sql_record($sSqlProcessoForoInicial);
    if ($clprocessoforoinicial->numrows > 0) {
        db_fieldsmemory($rsProcessoForoInicial,0);
    }
}

if (isset($oGet->v70_sequencial)) {
    $v71_processoforo = $oGet->v70_sequencial;
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js, strings.js, prototype.js");
    db_app::load("estilos.css, grid.style.css");
    ?>
</head>
<body bgcolor=#CCCCCC >
<?php
include(modification("forms/db_frmprocessoforoinicial.php"));
?>
</body>
<?php
if (isset($oPost->incluir) || isset($oPost->alterar) || isset($oPost->excluir)) {

    db_msgbox($sMsgErro);
    if ($clprocessoforoinicial->erro_campo != "") {

        echo "<script> document.form1.".$clprocessoforoinicial->erro_campo.".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1.".$clprocessoforoinicial->erro_campo.".focus();</script>";
    }
}
?>
</html>
