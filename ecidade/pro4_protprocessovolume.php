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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_protprocesso_classe.php"));
require_once(modification("classes/db_procdoctipo_classe.php"));
require_once(modification("classes/db_procvar_classe.php"));
require_once(modification("classes/db_andpadrao_classe.php"));
require_once(modification("classes/db_proctipovar_classe.php"));
require_once(modification("classes/db_db_syscampo_classe.php"));
require_once(modification("classes/db_procprocessodoc_classe.php"));
require_once(modification("classes/db_arrenumcgm_classe.php"));
require_once(modification("classes/db_protparam_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_app::import("protocolo.ProcessoProtocoloNumeracao");


try{

    if (ProcessoProtocoloNumeracao::getTipoConfiguracao() != ProcessoProtocoloNumeracao::TIPOORGAO) {
        throw new Exception('Controle de Numeração por Órgão não configurado para esta Instituição.');
    }
} catch( Exception $oErro ){

    db_msgbox($oErro->getMessage());

}

$p58_dtproc_dia = date("d", db_getsession("DB_datausu"));
$p58_dtproc_mes = date("m", db_getsession("DB_datausu"));
$p58_dtproc_ano = date("Y", db_getsession("DB_datausu"));

$oPost = db_utils::postMemory($_POST,0);
$oGet  = db_utils::postMemory($_GET,0);

$clprotprocesso = new cl_protprocesso;
$clprotparam = new cl_protparam;
$clprocprocessodoc = new cl_procprocessodoc;
$clproctipovar = new cl_proctipovar;
$clandpadrao = new cl_andpadrao;
$clarrenumcgm = new cl_arrenumcgm;
$clprotparamglobal = new cl_protparamglobal;

$db_opcao = 1;
$sqlerro= false;
$db_botao = false;

$tipoControle = $clprotparamglobal->get_parametro_numeracao()['p06_tipo'];

if (!empty($_GET['p58_codproc'])) {
    $result = $clprotprocesso->sql_record($clprotprocesso->sql_query($_GET['p58_codproc']));
    db_fieldsmemory($result, 0);

    if (!empty($p58_processopai)) {
        $rsNumeracaoProcessopai = $clprotprocesso->sql_record(
            $clprotprocesso->sql_query_file($p58_processopai, "cast(p58_numero||'/'||p58_ano as varchar) AS numeracaoprocessopai")
        );
        db_fieldsmemory($rsNumeracaoProcessopai, 0);
    }
}
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
<body style="background-color:#CCCCCC;" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
    <br/><br/>
    <div style="text-align:center; max-width:1910px;">
        <div style="width:790px; display:inline-block;">
            <form name="form1" method="post" action="">
                <?php require_once(modification("forms/db_frmprotprocessovolume.php")); ?>
            </form>
        </div>
    </div>
</body>
</html>
