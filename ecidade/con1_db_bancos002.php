<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
include(modification("classes/db_db_bancos_classe.php"));
include(modification("dbforms/db_funcoes.php"));

$result = null;

parse_str($HTTP_SERVER_VARS["QUERY_STRING"], $result);
db_postmemory($HTTP_POST_VARS);

$cldb_bancos = new cl_db_bancos;
$db_opcao    = 22;
$db_botao    = false;

if (isset($alterar))
{
    $db_opcao = 2;
    $sql      = "select db90_logo as arquivoalt from db_bancos where db90_codban  = '" . $db90_codban . "'";
    $result   = db_query($sql);
    $linhas   = $result->numrows;

    if (is_numeric($result) && $linhas > 0)
    {
        db_fieldsmemory($result, 0);
    }

    if (isset($db90_logo) && $db90_logo != "")
    {
        if ($arquivoalt != "")
        {
            // se ja existe arquivo... ele altera
            $oidgrava = db_geraArquivoOid("db90_logo", "$arquivoalt", 2, $conn);
        }
        else
        {
            // se não existe arquivo ele inclui o arquivo
            $oidgrava = db_geraArquivoOid("db90_logo", "", 1, $conn);
        }

        $cldb_bancos->db90_logo = $oidgrava;
    }
    // else
    // {
  	//     // se não informou nenhum arquivo ele grava null... se tiver arquivo ja gravado tem q excluir.
    //     if ($arquivoalt != "")
    //     {
    //         $oidgrava = db_geraArquivoOid("db90_logo","$arquivoalt",3,$conn);
    //     }
	
  	//     $oidgrava= "null";
    //  }
  

    $cldb_bancos->db90_codban = $db90_codban;
    $cldb_bancos->db90_descr  = $db90_descr;
    $cldb_bancos->db90_digban = $db90_digban;
    $cldb_bancos->db90_abrev  = $db90_abrev;
    
    db_inicio_transacao();

    $cldb_bancos->alterar($db90_codban);
    $erro_msg = $cldb_bancos->erro_msg;

    if ($cldb_bancos->erro_status == 0)
    {
        $sqlerro = true;
    }

    db_fim_transacao();
} 
else if (isset($chavepesquisa))
{
    $db_opcao = 2;
    $result   = $cldb_bancos->sql_record(
        $cldb_bancos->sql_query($chavepesquisa)
    );

    db_fieldsmemory($result, 0);

    $db_botao = true;
}
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="grid.style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
    <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
        <tr>
            <td width="360" height="18">&nbsp;</td>
            <td width="263">&nbsp;</td>
            <td width="25">&nbsp;</td>
            <td width="140">&nbsp;</td>
        </tr>
    </table>
    <table style="width:100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
                <center>
                    <?php
                        include(modification("forms/db_frmdb_bancos.php"));
                    ?>
                </center>
            </td>
        </tr>
    </table>
    <?php
        db_menu(
            db_getsession("DB_id_usuario"), 
            db_getsession("DB_modulo"), 
            db_getsession("DB_anousu"), 
            db_getsession("DB_instit")
        );
    ?>
</body>

</html>
<?php
    if (isset($alterar))
    {        
        if ($cldb_bancos->erro_status == "0")
        {
            $cldb_bancos->erro(true, false);
            $db_botao = true;

            echo "<script>document.form1.db_opcao.disabled=false;</script>";

            if ($cldb_bancos->erro_campo != "")
            {
                echo "<script>document.form1." . $cldb_bancos->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
                echo "<script>document.form1." . $cldb_bancos->erro_campo . ".focus();</script>";
            }
        }
        else
        {
            $cldb_bancos->erro(true, true);
        }
    }

    if ($db_opcao == 22)
    {
        echo "<script>js_pesquisa();</script>";
    }
?>
<script>
    js_tabulacaoforms("form1", "db90_descr", true, 1, "db90_descr", true);
</script>
