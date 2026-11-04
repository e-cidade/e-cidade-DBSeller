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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_proctransfer_classe.php"));
include(modification("classes/db_proctransferproc_classe.php"));
include(modification("dbforms/db_funcoes.php"));
use ECidade\Patrimonial\Protocolo\Repositorio\ProcessoRepositorio;
use ECidade\Patrimonial\Protocolo\Procedimentos\Parametros\Modelo\MensageriaProcesso;

db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);
$clproctransfer     = new cl_proctransfer;
$clproctransferproc = new cl_proctransferproc;
$clProcessosApensados = new cl_processosapensados();

if (isset($cancel)) {
    $codigoProcessos =explode(',', $listaproc);

    foreach ($codigoProcessos as $codigoProcesso) {
        $processo = ProcessoRepositorio::encontrar($codigoProcesso);
        $codigoUsuario = $processo->getCodigoUsuario();
        // Verifica se existe usuario atribuido ao processo
        // Caso nao exista, envia notificacao para o departamento todo
        if (empty($codigoUsuario) || (int)$codigoUsuario === 0) {
            //$codtransfer
            $daoTransferencia  = new cl_proctransfer();
            $sqlBuscaDepartamento = $daoTransferencia->sql_query_file($codtransfer, 'p62_coddeptorec');
            $resBuscaDepartamento = db_query($sqlBuscaDepartamento);
            if (!$resBuscaDepartamento) {
                $erro = $daoTransferencia->erro_msg;
                $sqlerro = true;
            } else {

                $oDepartamento = new DBDepartamento(db_utils::fieldsMemory($resBuscaDepartamento, 0)->p62_coddeptorec);
                MensageriaProcesso::enviar($processo->getCodigo(), true, $oDepartamento);
            }
        } else {
            // Vai por usuario
            MensageriaProcesso::enviar($processo->getCodigo(), true);
        }
    }
    db_inicio_transacao();
    $sqlerro     = false;
    $sqlproctransferproc = $clproctransferproc->sql_query_file(null,
        null,
        '*',
        null,
        "    p63_codtran = $codtransfer
      and not exists (select 1
        from processosapensados
        where p30_procapensado = proctransferproc.p63_codproc) ");

    $result_cont = $clproctransferproc->sql_record($sqlproctransferproc);

    if ($clproctransferproc->numrows == $contador) {
        /* select processoapensado pela lista de processos excluir processos apensandos da lista */
        $sSqlProcessosApensados = $clProcessosApensados->sql_query_file(null, "*", null, "p30_procprincipal in ($listaproc) ");
        $rsProcessosApensados   = $clProcessosApensados->sql_record($sSqlProcessosApensados);
        for($iProcessoApensado = 0; $iProcessoApensado < pg_num_rows($rsProcessosApensados); $iProcessoApensado++) {

            $oProcessoApensado = db_utils::fieldsmemory($rsProcessosApensados, $iProcessoApensado);
            $clproctransferproc->excluir(null, null, "p63_codproc = $oProcessoApensado->p30_procapensado and p63_codtran = $codtransfer");
            $erro = $clproctransferproc->erro_msg;
            if ($clproctransferproc->erro_status==0){
                $sqlerro=true;
            }

        }

        $clproctransferproc->excluir(null, null, "p63_codtran=$codtransfer");
        $erro = $clproctransferproc->erro_msg;
        if ($clproctransferproc->erro_status == 0) {
            $sqlerro = true;
        }

        if ($sqlerro == false) {
            $clproctransfer->excluir($codtransfer);
            $erro = $clproctransfer->erro_msg;

            if ($clproctransfer->erro_status == 0) {
                $sqlerro = true;
            }
        }
    } else {
        /* select processoapensado pela lista de processos excluir processos apensandos da lista */
        $sSqlProcessosApensados = $clProcessosApensados->sql_query_file(null, "*", null, "p30_procprincipal in ($listaproc) ");
        $rsProcessosApensados   = $clProcessosApensados->sql_record($sSqlProcessosApensados);
        for($iProcessoApensado = 0; $iProcessoApensado < pg_num_rows($rsProcessosApensados); $iProcessoApensado++) {

            $oProcessoApensado = db_utils::fieldsmemory($rsProcessosApensados, $iProcessoApensado);
            $clproctransferproc->excluir(null, null, "p63_codproc = $oProcessoApensado->p30_procapensado and p63_codtran = $codtransfer");
            $erro = $clproctransferproc->erro_msg;
            if ($clproctransferproc->erro_status==0){
                $sqlerro=true;
            }

        }

        $clproctransferproc->excluir(null, null, "p63_codtran=$codtransfer and p63_codproc in ($listaproc)");
        $erro = $clproctransferproc->erro_msg;

        if ($clproctransferproc->erro_status == 0) {
            $sqlerro=true;
        }
    }
    db_fim_transacao($sqlerro);
}
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script>
        </script>
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
    <body bgcolor=#CCCCCC leftmargin="0" style="margin-top: 25px" marginwidth="0" marginheight="0" onLoad="a=1" >
        <center>
            <?php include(modification("forms/db_frmcanceltranspar.php"));?>
        </center>
        <?php
        if (isset($cancel)) {
            db_msgbox($erro);
            if ($sqlerro == true) {
                echo "<script> document.form1." . $clproctransferproc->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
                echo "<script> document.form1." . $clproctransferproc->erro_campo . ".focus();</script>";
            } else {
                echo"<script>(window.CurrentWindow || parent.CurrentWindow).corpo.location.href='pro4_canceltranspar001.php';</script>";
            }
        }
        ?>
    </body>
    <?php
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
    ?>
</html>
