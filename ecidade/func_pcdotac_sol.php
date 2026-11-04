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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_pcdotac_classe.php"));
db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);
$clpcdotac = new cl_pcdotac;
$clpcdotac->rotulo->label("pc13_codigo");
$clpcdotac->rotulo->label("pc13_anousu");
$clpcdotac->rotulo->label("pc13_coddot");
$clpcdotac->rotulo->label("pc13_quant");
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
        <tr>
            <td height="63" align="center" valign="top">
                <table width="35%" border="0" align="center" cellspacing="0">
                    <form name="form2" method="post" action="">
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Tpc13_codigo ?>">
                                <?= $Lpc13_codigo ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("pc13_codigo", 10, $Ipc13_codigo, true, "text", 4, "", "chave_pc13_codigo");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Tpc13_coddot ?>">
                                <?= $Lpc13_coddot ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("pc13_coddot", 6, $Ipc13_coddot, true, "text", 4, "", "chave_pc13_coddot");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Tpc13_quant ?>">
                                <?= $Lpc13_quant ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("pc13_quant", 10, $Ipc13_quant, true, "numeric", 4, "", "chave_pc13_quant");
                                ?>
                            </td>
                        </tr>
                        <tr>
                        <tr>
                            <td colspan="2" align="center">
                                <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                                <input name="limpar" type="reset" id="limpar" value="Limpar">
                                <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_pcdotac.hide();">
                            </td>
                        </tr>
                    </form>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top">
                <?php

                include(modification("funcoes/db_func_pcdotac.php"));

                $condicoes = [];
                $where = '';

                if (empty($pesquisa_chave)) {
                    if (!empty($chave_pc13_codigo)) {
                        $condicoes[] = "pc13_codigo = '{$chave_pc13_codigo}'";
                    }

                    if (!empty($chave_pc13_coddot)) {
                        $condicoes[] = "pc13_coddot = '{$chave_pc13_coddot}'";
                    }

                    if (!empty($chave_pc13_quant)) {
                        $condicoes[] = "pc13_quant = '{$chave_pc13_quant}'";
                    }

                    $where = implode(' AND ', $condicoes);

                    $sql = $clpcdotac->sql_query_file(null, null, null, $campos, null, $where);
                    db_lovrot($sql, 15, '()', '', $funcao_js);
                }

                if (!empty($pesquisa_chave)) {

                    $result = $clpcdotac->sql_record($clpcdotac->sql_query_file(null, null, $pesquisa_chave));

                    if (!empty($clpcdotac->numrows)) {

                        db_fieldsmemory($result, 0);
                        echo "<script>" . $funcao_js . "('$pc13_anousu',false);</script>";
                    } else {
                        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                    }
                }

                if (empty($pesquisa_chave)) {
                    echo "<script>" . $funcao_js . "('', false, false);</script>";
                }
                ?>
            </td>
        </tr>
    </table>
</body>

</html>
<script type="text/javascript">
    (function() {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
