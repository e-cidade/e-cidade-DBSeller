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
require_once(modification("classes/db_pcsubgrupo_classe.php"));
db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);
$clpcsubgrupo = new cl_pcsubgrupo;
$clpcsubgrupo->rotulo->label("pc04_codsubgrupo");
$clpcsubgrupo->rotulo->label("pc04_descrsubgrupo");
$clpcsubgrupo->rotulo->label("pc04_ativo");

?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body background-color=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table height="100%" border="0" align="center" cellspacing="0" background-color="#CCCCCC">
        <tr>
            <td height="63" align="center" valign="top">
                <table width="35%" border="0" align="center" cellspacing="0">
                    <form name="form2" method="post" action="">
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Tpc04_codsubgrupo ?>">
                                <?= $Lpc04_codsubgrupo ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("pc04_codsubgrupo", 6, $Ipc04_codsubgrupo, true, "text", 4, "", "chave_pc04_codsubgrupo");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Tpc04_descrsubgrupo ?>">
                                <?= $Lpc04_descrsubgrupo ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("pc04_descrsubgrupo", 40, $Ipc04_descrsubgrupo, true, "text", 4, "", "chave_pc04_descrsubgrupo");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="4%" align="right">
                                <?= $Lpc04_ativo ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                $opcoesSelect = ['true' => 'Sim', 'false' => 'Não', '2' => 'Ambos'];
                                db_select("chave_pc04_ativo", $opcoesSelect, 10, 2, "onchange='js_reload()';");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                                <input name="limpar" type="reset" id="limpar" value="Limpar">
                                <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_pcsubgrupo.hide();">
                            </td>
                        </tr>
                    </form>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top">
                <?php
                include_once(modification("funcoes/db_func_pcsubgrupo.php"));

                $condicoes = [];
                $where = '';
                $chave_pc04_ativo = isset($chave_pc04_ativo) ? $chave_pc04_ativo : 'true';

                if (empty($pesquisa_chave)) {
                    if (!empty($chave_pc04_codsubgrupo)) {
                        $condicoes[] = "pc04_codsubgrupo = '{$chave_pc04_codsubgrupo}'";
                    }

                    if (!empty($chave_pc04_descrsubgrupo)) {
                        $condicoes[] = "pc04_descrsubgrupo LIKE '%{$chave_pc04_descrsubgrupo}%'";
                    }

                    if (!empty($chave_pc04_ativo)) {
                        if ($chave_pc04_ativo === 'false') {
                            $condicoes[] = "pc04_ativo = '{$chave_pc04_ativo}'";
                        }

                        if ($chave_pc04_ativo === 'true') {
                            $condicoes[] = "pc04_ativo = '{$chave_pc04_ativo}'";
                        }
                    }
                    $condicoes[] = "pc04_tipoutil in (2,3)";
                    $where = implode(' AND ', $condicoes);

                    $sql = $clpcsubgrupo->sql_query(null, $campos, null, $where);
                    db_lovrot($sql, 15, '()', '', $funcao_js);

                    echo "<script>" . $funcao_js . "('', false, false);</script>";
                }
                if (!empty($pesquisa_chave)) {
                    $filtroWhere = "pc04_codsubgrupo = $pesquisa_chave and pc04_tipoutil in (2,3) and pc04_ativo = true";
                    $query = $clpcsubgrupo->sql_query_file(null, "*", null, $filtroWhere);
                    $recordSet = $clpcsubgrupo->sql_record($query);

                    if (!empty($clpcsubgrupo->numrows)) {
                        $descricaoFornecedor = db_utils::fieldsMemory($recordSet,0)->pc04_descrsubgrupo;
                        echo "<script>" . $funcao_js . "('$descricaoFornecedor',false);</script>";
                    } else {
                        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                    }
                }
                ?>
            </td>
        </tr>
    </table>
</body>

</html>
<script type="text/javascript">
    function js_reload() {
        document.querySelector('input[name=pesquisar]').click();
    }

    (function() {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
