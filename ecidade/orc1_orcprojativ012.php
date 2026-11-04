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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_orcprojativ_classe.php"));
require_once(modification("classes/db_orcprojativunidaderesp_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clorcprojativ            = new cl_orcprojativ();
$clorcprojativunidaderesp = new cl_orcprojativunidaderesp();
$clOrcprojativunidademedida = new cl_orcprojativunidademedida;

$db_opcao = 22;
$db_botao = false;
$lSqlErro = false;

if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"])=="Alterar") {
    db_inicio_transacao();
    $db_opcao = 2;

    $sSqlUltimoAno = "select max(o55_anousu) as anomaximo from orcprojativ";
    $rsUltimoAno   = $clorcprojativ->sql_record($sSqlUltimoAno);
    $iUltimoAno    = db_utils::fieldsMemory($rsUltimoAno, 0)->anomaximo;

    for ($iAno = $o55_anousu; $iAno <= $iUltimoAno; $iAno++) {
        $clorcprojativ->o55_anousu   = $iAno;
        $clorcprojativ->o55_projativ = $o55_projativ;
        $clorcprojativ->alterar($iAno, $o55_projativ);

        if ($clorcprojativ->erro_status == 0) {
            $lSqlErro = true;
        }
    }

    if (!$lSqlErro && isRioDeJaneiro() && !empty($unidadeMedida) && $unidadeMedida != "0") {
        $clOrcprojativunidademedida->o221_anousu = $o55_anousu;
        $clOrcprojativunidademedida->o221_orcprojativ   = $o55_projativ;
        $clOrcprojativunidademedida->o221_unidade   = $unidadeMedida;

        if (trim($o221_sequencial) != "") {
            $clOrcprojativunidademedida->alterar($o221_sequencial);
        } else {
            $clOrcprojativunidademedida->incluir(null);
        }

        if ($clOrcprojativunidademedida->erro_status == 0) {
            $lSqlErro = true;
        }
    } elseif (!$lSqlErro && isRioDeJaneiro() && $unidadeMedida == "0") {
        if (trim($o221_sequencial) != "") {
            $clOrcprojativunidademedida->excluir($o221_sequencial);
            if ($clOrcprojativunidademedida->erro_status == 0) {
                $lSqlErro = true;
            }
        }
    }

    if (!$lSqlErro && isset($o13_unidaderesp) && trim($o13_unidaderesp) != "") {
        $clorcprojativunidaderesp->o13_anousu       = $o55_anousu;
        $clorcprojativunidaderesp->o13_orcprojativ  = $o55_projativ;
        $clorcprojativunidaderesp->o13_unidaderesp  = $o13_unidaderesp;

        if (trim($o13_sequencial) != "") {
            $clorcprojativunidaderesp->alterar($o13_sequencial);
        } else {
            $clorcprojativunidaderesp->incluir(null);
        }

        if ($clorcprojativunidaderesp->erro_status == 0) {
            $lSqlErro = true;
        }
    } else {
        if (trim($o13_sequencial) != "") {
            $clorcprojativunidaderesp->excluir($o13_sequencial);

            if ($clorcprojativunidaderesp->erro_status == 0) {
                $lSqlErro = true;
            }
        }
    }

    db_fim_transacao($lSqlErro);
} elseif (isset($chavepesquisa)) {
    $clOrcprojativunidademedida = new cl_orcprojativunidademedida;
    $where = "o221_orcprojativ = ".$chavepesquisa1;
    $where .= " and o221_anousu = ".$chavepesquisa;
    $sqlUnidadeMedida = $clOrcprojativunidademedida->sql_query(null, "o221_sequencial,o221_unidade", null, $where);
  
    $rsUnidadeMedida = db_query($sqlUnidadeMedida);
 
    $unidadeMedida = 0;
    $o221_sequencial = null;

    if (pg_num_rows($rsUnidadeMedida) > 0) {
        $unidadeMedidaObjeto = db_utils::fieldsMemory($rsUnidadeMedida, 0);
        $unidadeMedida = $unidadeMedidaObjeto->o221_unidade;
        $o221_sequencial = $unidadeMedidaObjeto->o221_sequencial;
    }
    
    $db_opcao = 2;
    $result = $clorcprojativ->sql_record($clorcprojativ->sql_query($chavepesquisa, $chavepesquisa1));
    db_fieldsmemory($result, 0);
    $db_botao = true;
    $digito = ($o55_projativ{0}*1000);
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <?php
        include(modification("forms/db_frmorcprojativ.php"));
    ?>
</div>
</body>
</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"])=="Alterar") {
    if ($clorcprojativ->erro_status=="0") {
        $clorcprojativ->erro(true, false);
        $db_botao=true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clorcprojativ->erro_campo!="") {
            echo "<script> document.form1.".$clorcprojativ->erro_campo.".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1.".$clorcprojativ->erro_campo.".focus();</script>";
        };
    } else {
        $clorcprojativ->erro(true, false);
    }
}

if (isset($o55_projativ)  && !isNiteroi()) {
    $sQuery = "codprojativ={$o55_projativ}&anousu={$o55_anousu}";
    echo " <script> 															 	    ";
    echo "   parent.iframe_g2.location.href='orc1_orcprojativprogramfisica001.php?{$sQuery}';  ";
    echo "   parent.iframe_g3.location.href='orc1_orciniciativavinculoprojativ001.php?{$sQuery}';  ";
    echo "   parent.document.formaba.g2.disabled = false; 								";
    echo "   parent.document.formaba.g3.disabled = false; 								";
    echo " </script>																	";
} elseif (!isNiteroi()) {
    echo " <script>parent.document.formaba.g2.disabled = true;</script> 				";
    echo " <script>parent.document.formaba.g3.disabled = true;</script> 				";
}


if ($db_opcao==22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>