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

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"], $queryString);

$cllab_exame = new cl_lab_exame;
$cllab_exame->rotulo->label("la08_i_codigo");
$cllab_exame->rotulo->label("la08_c_descr");

if(!empty($sexoPaciente)){
  $sexoPaciente = $sexoPaciente == 'M' ? 1 : 2;
}
//Operacao 1 - PluginAlteracoesSaude - func_lab_exame.php
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
            <td width="4%" align="right" nowrap title="<?php echo $Tla08_i_codigo ?>">
                <?php echo $Lla08_i_codigo ?>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
                db_input("la08_i_codigo", 10, $Ila08_i_codigo, true, "text", 4, "", "chave_la08_i_codigo");
                ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?= $Tla08_c_descr ?>">
                <?php echo $Lla08_c_descr ?>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
                db_input("la08_c_descr", 50, $Ila08_c_descr, true, "text", 4, "", "chave_la08_c_descr");
                ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar">
              <input name="Fechar" type="button" id="fechar" value="Fechar"
                     onClick="parent.db_iframe_lab_exame.hide();">
              <?php //Operacao 2 - PluginAlteracoesSaude - func_lab_exame.php ?>
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" valign="top">
        <?php
        $sWhere = array();
        $sAnd = "";

        if (isset($iVinculo)) {
            $sAtivo = "";

            if (isset($iAtivo)) {
                $sAtivo = " and la09_i_ativo = $iAtivo ";
            }

            if(isset($departamento)){
              $sWhere[] = "
                EXISTS (
                  select la09_i_codigo 
                    from lab_setorexame 
                    join lab_labsetor on la24_i_codigo = la09_i_labsetor
                    join lab_laboratorio on la02_i_codigo = la24_i_laboratorio
                    join lab_labdepart on la02_i_codigo = la03_i_laboratorio
                   where la03_i_departamento = $departamento 
                     and la09_i_exame=la08_i_codigo $sAtivo
                )
              ";
            } else {
              $sWhere[] = " EXISTS (select * from lab_setorexame where la09_i_exame=la08_i_codigo $sAtivo) ";
            }            
        }

        if (isset($iAtivo)) {
            $sWhere[] = "la08_i_ativo=$iAtivo ";
        }

        if(isset($sexoPaciente) && !empty($sexoPaciente)){
          $sWhere[] = "(la08_i_sexo = $sexoPaciente or la08_i_sexo = 3)";
        }

        if(!empty($exames)){
          $sWhere[] = "la08_i_codigo in ($exames)";
        }

        if (!isset($pesquisa_chave)) {
            if (isset($campos) == false) {
                if (file_exists("funcoes/db_func_lab_exame.php") == true) {
                    include(modification("funcoes/db_func_lab_exame.php"));
                } else {
                    $campos = "lab_exame.*";
                }
            }

            if (isset($chave_la08_i_codigo) && (trim($chave_la08_i_codigo) != "")) {
                $sWhere[] = "la08_i_codigo=" . $chave_la08_i_codigo;
            }

            if (isset($chave_la08_c_descr) && (trim($chave_la08_c_descr) != "")) {
                $sWhere[] = "la08_c_descr like '$chave_la08_c_descr%' ";
            }

            $sWhere = implode(" and ", $sWhere);
            $sql = $cllab_exame->sql_query("", $campos, "la08_i_codigo", $sWhere);
                 
            if(isset($la02_i_codigo)){
              $sWhere = [];

              if(!empty($la02_i_codigo)){
                $sWhere[] = " la02_i_codigo = " . $la02_i_codigo;
              }

              if(isset($la24_i_setor) && !empty($la24_i_setor)){
                $sWhere[] =  " la24_i_setor = " . $la24_i_setor;
              }

              if(isset($setores) && !empty($setores)){
                $sWhere[] = " la24_i_setor in ({$setores})";
              }

              $sWhere = implode(" and ", $sWhere);           
              $sql = $cllab_exame->sql_query_exame_laboratorio(null, $campos, "la08_i_codigo", $sWhere);
            }

            $repassa = array();

            if (isset($chave_la08_i_codigo)) {
                $repassa = array(
                  "chave_la08_i_codigo" => $chave_la08_i_codigo,
                  "chave_la08_c_descr"  => $chave_la08_c_descr
                );
            }
            db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
        } else {
            if ($pesquisa_chave != null && $pesquisa_chave != "") {
                //Abre Operacao 3 - PluginAlteracoesSaude - func_lab_exame.php
                $sWhere[] = " la08_i_codigo = {$pesquisa_chave} ";
                //Fecha Operacao 3
                $sWhere = implode(" and ", $sWhere);
                $sSqlExame = $cllab_exame->sql_query("", "*", "", $sWhere);

                if(isset($la02_i_codigo)){

                  $sWhere = [" la08_i_codigo = {$pesquisa_chave} "];

                  if(!empty($la02_i_codigo)){
                    $sWhere[] = " la02_i_codigo = " . $la02_i_codigo;
                  }
    
                  if(isset($la24_i_setor) && !empty($la24_i_setor)){
                    $sWhere[] =  " la24_i_setor = " . $la24_i_setor;
                  }
    
                  if(isset($setores) && !empty($setores)){
                    $sWhere[] = " la24_i_setor in ({$setores})";
                  }
    
                  $sWhere = implode(" and ", $sWhere);           
                  $sSqlExame = $cllab_exame->sql_query_exame_laboratorio(null, "*", "la08_i_codigo", $sWhere);
                }
                
                $result = db_query($sSqlExame);

                if (pg_num_rows($result) != 0) {
                    db_fieldsmemory($result, 0);
                    echo "<script>" . $funcao_js . "('$la08_c_descr',false,'$la08_i_codigo');</script>";
                } else {
                    echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                }
            } else {
                echo "<script>" . $funcao_js . "('',false,'$la08_i_codigo');</script>";
            }
        }
        ?>
    </td>
  </tr>
</table>
</body>
</html>
<script>
  js_tabulacaoforms("form2", "chave_la08_i_codigo", true, 1, "chave_la08_i_codigo", true);
</script>
<script type="text/javascript">
  (function() {
    var query = frameElement.getAttribute('name').replace('IF', ''),
      input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
  })();
</script>
