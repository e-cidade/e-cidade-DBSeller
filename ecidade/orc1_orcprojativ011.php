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

db_postmemory($_POST);

$clorcprojativ            = new cl_orcprojativ();
$clorcprojativunidaderesp = new cl_orcprojativunidaderesp();

$db_opcao = 1;
$db_botao = true;
$lSqlErro = false;

if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"])=="Incluir") {
    db_inicio_transacao();

    $o55_projativ += $digito;

    $iAnoUsu       = db_getsession("DB_anousu");
    $sSqlUltimoAno = "select coalesce(max(o55_anousu),{$iAnoUsu}) as anomaximo from orcprojativ where o55_anousu > $iAnoUsu";
    $rsUltimoAno   = $clorcprojativ->sql_record($sSqlUltimoAno);
    $iUltimoAno    = db_utils::fieldsMemory($rsUltimoAno, 0)->anomaximo;

  //Verificamos se o projativ já existe para outro ano.
    $sSqlProjAtiv = "select * from orcprojativ where o55_projativ = $o55_projativ and o55_anousu between {$iAnoUsu} and {$iUltimoAno}";
    $iProjAtiv = db_query($sSqlProjAtiv);
    if (pg_num_rows($iProjAtiv) > 0) {
        db_msgbox("Inclusão não Efetuada.\\nProjeto/Atividade Já cadastrado com mesmo numero({$o55_projativ}) para outros anos.");
        db_redireciona("orc1_orcprojativ011.php");
        $lSqlErro = true;
    } else {
        for ($iAno = $o55_anousu; $iAno <= $iUltimoAno; $iAno++) {
            $clorcprojativ->incluir($iAno, $o55_projativ);
            if ($clorcprojativ->erro_status == 0) {
                $lSqlErro = true;
            }

            if (!$lSqlErro && isRioDeJaneiro() && !empty($unidadeMedida) && $unidadeMedida != 0) {
                $clOrcprojativunidademedida = new cl_orcprojativunidademedida;
                $clOrcprojativunidademedida->o221_anousu = $iAno;
                $clOrcprojativunidademedida->o221_orcprojativ   = $o55_projativ;
                $clOrcprojativunidademedida->o221_unidade   = $unidadeMedida;
                $clOrcprojativunidademedida->incluir(null);
            
                if ($clOrcprojativunidademedida->erro_status == 0) {
                    $lSqlErro = true;
                }
            }

            if (!$lSqlErro && isset($o13_unidaderesp) && trim($o13_unidaderesp) != "") {
                $clorcprojativunidaderesp->o13_anousu       = $iAno;
                $clorcprojativunidaderesp->o13_orcprojativ  = $o55_projativ;
                $clorcprojativunidaderesp->o13_unidaderesp  = $o13_unidaderesp;
                $clorcprojativunidaderesp->incluir(null);

                if ($clorcprojativunidaderesp->erro_status == 0) {
                    $lSqlErro = true;
                }
            }
        }
    }
    db_fim_transacao($lSqlErro);
    $projativ = $o55_projativ;
    unset($o55_projativ);
} elseif (isNiteroi()) {
    // Botao Proximo
    if (isset($db_procproximo) && $db_procproximo != "") {
        $clicou = 't';
        $sqlProjativ = $clorcprojativ->sql_query_file(null, null, "DISTINCT o55_projativ", " o55_projativ ASC ", " o55_anousu = {$o55_anousu}");
        $rsResultcod = db_query($sqlProjativ);

        if (pg_num_rows($rsResultcod) > 0) {
            $arrayCodigos =  array_column(pg_fetch_all($rsResultcod), 'o55_projativ');
            $arrayCodigos = array_filter($arrayCodigos, function ($item) use ($digito) {
                if ($digito == 0) {
                    return $item;
                } else {
                    if (substr($item, 0, 1) == substr($digito, 0, 1) && strlen($item) == 4) {
                        return $item;
                    }
                    return null;
                }
            });
            $arrayCodigos = array_values($arrayCodigos);
        
            for ($i = 0; $i < count($arrayCodigos); $i++) {
                $atual = strlen($arrayCodigos[$i+1]) === 4 ? substr($arrayCodigos[$i], 1, strlen($arrayCodigos[$i])) : $arrayCodigos[$i];
                // Verifica se na primeira interação o código atual é maior que 1
                // caso seja, o 1 está disponível, então encerra-se o loop
                if ($i == 0 && $atual > 1) {
                    $proximo = $digito == 0 ? 1 : 0; //VERIFICAR SE O PRIMEIRO CÓDIGO PODERÁ SER 0 - Ex.: 0000, 2000, 3000, 4000
                    $o55_projativ = str_pad($proximo, 3, '0', STR_PAD_LEFT);
                    break;
                }
          
                $proximo = strlen($arrayCodigos[$i+1]) === 4 ? substr($arrayCodigos[$i+1], 1, strlen($arrayCodigos[$i+1])) : $arrayCodigos[$i+1];
                $intervaloProximo = $proximo - $atual;
                // Verifica se o próximo item do array tem a diferença maior que 1
                // caso seja, é atribuído o primeiro número disponível nesse intervalo e encerra-se o loop
                if ($intervaloProximo > 1) {
                    $proximo = $atual + 1;
                    if ($proximo > 999) {
                        $o55_projativ = '';
                        db_msgbox("Não existe código disponível");
                        break;
                    }
                    $o55_projativ = str_pad($proximo, 3, '0', STR_PAD_LEFT);
                    break;
                }
            }

            if (!isset($o55_projativ) || $o55_projativ == "") {
                $proximo = $arrayCodigos[count($arrayCodigos) - 1] + 1;
                $proximo = str_pad($proximo, 3, '0', STR_PAD_LEFT);
                $o55_projativ = strlen($proximo) === 4 ? substr($proximo, 1, strlen($proximo)) : $proximo;
            }
        } else {
            $proximo = 1;
            $o55_projativ = str_pad($proximo, 3, '0', STR_PAD_LEFT);
        }

        $projativ = $o55_projativ;
        unset($o55_projativ);
    }
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table align="center" style="padding-top:25px;"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <center>
    <?php
    include(modification("forms/db_frmorcprojativ.php"));
    ?>
    </center>
    </td>
  </tr>
</table>
</body>
</html>
<?php
if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir") {
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
  
        if (!isNiteroi()) {
            $sQuery = "codprojativ={$clorcprojativ->o55_projativ}&anousu={$clorcprojativ->o55_anousu}";
            echo " <script>";
            echo "   parent.iframe_g2.location.href='orc1_orcprojativprogramfisica001.php?{$sQuery}';";
            echo "   parent.document.formaba.g2.disabled = false;";
            echo "   parent.mo_camada('g2');";
            echo "   parent.iframe_g1.location.href='orc1_orcprojativ012.php?chavepesquisa={$o55_anousu}&chavepesquisa1={$clorcprojativ->o55_projativ}'; ";
            echo " </script>";
        }
    }
}


if (isset($o55_projativ) && !isNiteroi()) {
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


?>
