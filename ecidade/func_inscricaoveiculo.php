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
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"],  $queryString);    

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$daoIssVeiculo = new cl_issveiculo();
$calculo = false;

if (!empty($_GET['calculo']) && $_GET['calculo'] == '1') {
    $calculo = true;
}

?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <link href='estilos.css' rel='stylesheet' type='text/css'>
    <script type='text/javascript' src='scripts/scripts.js'></script>
</head>
<body class="body-default">
    <div class="container">
        <form id="form2" name="form2">
            <input type="hidden" name="funcao_js" id="funcao_js" value="<?=$funcao_js?>">
            <input type="hidden" name="calculo" id="calculo" value="<?=$calculo?>">
            <fieldset>
                <legend>Alvar de eventos</legend>
                <table>
                    <tr>
                        <td>
                            <label for="inscricao"><b>Inscrição:</b></label>
                        </td>
                        <td>
                            <input type="text" name="inscricao" id="inscricao" class="field-size2" onchange="js_ValidaCampos(this, 1, 'Inscrição', true, true)">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="codigoCGM"><b>Código do CGM:</b></label>
                        </td>
                        <td>
                            <input type="text" name="codigoCGM" id="codigoCGM" class="field-size2" onchange="js_ValidaCampos(this, 1, 'Código do CGM', true, true)">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="nomeCGM"><b>Nome do CGM:</b></label>
                        </td>
                        <td>
                            <input type="text" name="nomeCGM" id="nomeCGM" class="field-size9">
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
            <input name="limpar" type="reset" id="limpar" value="Limpar" >
            <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_inscricaoveiculo.hide();">
        </form>
    </div>
    <?php
        $campos = "q172_sequencial, q172_datacadastro, q172_issbase, z01_numcgm, z01_nome, q123_sequencial as db_issalvara";
        $where = array();
        
        if ($calculo) {
            $where[] = "q02_dtbaix is null";
        }

        if(!isset($pesquisa_chave)){
            
            if(!empty($inscricao)){
                $where[] = "q172_issbase = {$inscricao}";
            }

            if (!empty($codigoCGM)) {
                $where[] = "q02_numcgm = {$codigoCGM}";
            }

            if (!empty($nomeCGM)) {
                $where[] = "z01_nome ilike '{$nomeCGM}%'";
            }

            $sql = $daoIssVeiculo->sqlDados($campos, $where);
            echo '<div class="container">';
            echo '  <fieldset>';
            echo '    <legend>Resultado da Pesquisa</legend>';
                db_lovrot($sql, 15, "()", "", $funcao_js);
            echo '  </fieldset>';
            echo '</div>';
        } else {
            if(!empty($pesquisa_chave)){
                $where[] = "q172_issbase = {$pesquisa_chave}";
                $result = db_query($daoIssVeiculo->sqlDados($campos, $where));

                if (pg_num_rows($result) > 0) {
                    $dados = db_utils::fieldsMemory($result, 0);
                    echo "<script>".$funcao_js."('". $dados->z01_nome ."',false, '". $dados->q172_sequencial ."', '". $dados->db_issalvara ."');</script>";
                }else{
                    echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") no Encontrado',true);</script>";
                }
            } else {
                echo "<script>".$funcao_js."('',false);</script>";
            }
        }
    ?>
</body>
</html>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
