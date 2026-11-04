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

$daoAlvaraEvento = new cl_alvaraevento();
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
            <fieldset>
                <legend>Alvará de eventos</legend>
                <table>
                    <tr>
                        <td>
                            <label for="codigo"><b>Código do Alvará:</b></label>
                        </td>
                        <td>
                            <input type="text" name="codigo" id="codigo" class="field-size2" onchange="js_ValidaCampos(this, 1, 'Código do Alvará', true, true)">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="descricao"><b>Nome do Evento:</b></label>
                        </td>
                        <td>
                            <input type="text" name="descricao" id="descricao" class="field-size9">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="localizacao"><b>Localização do Evento:</b></label>
                        </td>
                        <td>
                            <input type="text" name="localizacao" id="localizacao" class="field-size9">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="nome_inscricao"><b>Nome Inscrição:</b></label>
                        </td>
                        <td>
                            <input type="text" name="nome_inscricao" id="nome_inscricao" class="field-size9">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="nome_razao"><b>Nome/Razão Social:</b></label>
                        </td>
                        <td>
                            <input type="text" name="nome_razao" id="nome_razao" class="field-size9">
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
            <input name="limpar" type="reset" id="limpar" value="Limpar" >
            <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_alvaraevento.hide();">
        </form>
    </div>
    <?php
        $campos = "q170_codigo, 
               q98_descricao as dl_descricao_tipo_alvara, 
               q168_codigo dl_codigo_evento, 
               q168_descricao, 
               q168_localizacao,
               q02_inscr,
               cgm_inscricao.z01_numcgm as dl_cgm_inscricao,
               cgm_inscricao.z01_nome dl_nome_inscricao,
               cgm_ordemservico.z01_numcgm,
               cgm_ordemservico.z01_nome";

        if(!isset($pesquisa_chave)){

            $where = array();
            
            if(!empty($codigo)){
                $where[] = "q170_codigo = {$codigo}";
            }

            if (!empty($descricao)) {
                $where[] = "q168_descricao ilike '{$descricao}%'";
            }

            if (!empty($localizacao)) {
                $where[] = "q168_localizacao ilike '{$localizacao}%'";
            }

            if (!empty($nome_inscricao)) {
                $where[] = "cgm_inscricao.z01_nome ilike '{$nome_inscricao}%'";
            }

            if (!empty($nome_razao)) {
                $where[] = "cgm_inscricao.z01_nome ilike '{$nome_razao}%'";
            }
            $sql = $daoAlvaraEvento->sqlDadosEvento($campos, $where);

            echo '<div class="container">';
            echo '  <fieldset>';
            echo '    <legend>Resultado da Pesquisa</legend>';
                db_lovrot($sql, 15, "()", "", $funcao_js);
            echo '  </fieldset>';
            echo '</div>';
        } else {
            if(!empty($pesquisa_chave)){
                $result = db_query($daoAlvaraEvento->sql_query($pesquisa_chave));

                if (pg_num_rows($result) > 0) {
                    $dados = db_utils::fieldsMemory($result, 0);
                    echo "<script>".$funcao_js."('". $dados->q168_descricao ."',false);</script>";
                }else{
                    echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
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
