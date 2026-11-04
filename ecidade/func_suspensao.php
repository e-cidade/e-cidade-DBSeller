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
include(modification("classes/db_suspensao_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clsuspensao = new cl_suspensao;
$clsuspensao->rotulo->label("ar18_sequencial");
$clsuspensao->rotulo->label("ar18_procjur");
$clrotulo = new rotulocampo();
$clrotulo->label('j01_matric');
$clrotulo->label('q02_inscr');
$clrotulo->label('z01_numcgm');
$clrotulo->label('z01_nome');

$k00_numcgm = null;
$k00_inscr = null;

if(!isset($mostra)){
  $mostra = false;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript">

  function js_mostranomes(mostra) {
    document.form2.q02_inscr.value = "";
    document.form2.j01_matric.value = "";
    if (mostra == true) {
      js_OpenJanelaIframe('', 'db_iframe_nomes', 'func_nome.php?funcao_js=parent.js_preenche|0|1', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe_nomes', 'func_nome.php?pesquisa_chave=' + document.form2.z01_numcgm.value + '&funcao_js=parent.js_preenche1', 'Pesquisa', false);
    }
  }

  function js_preenche(chave, chave1) {
    document.form2.z01_numcgm.value = chave;
    document.form2.z01_nome.value = chave1;
    db_iframe_nomes.hide();
  }

  function js_preenche1(chave, chave1) {
    document.form2.z01_nome.value = chave1;
    if (chave == true) {
      document.form2.z01_numcgm.value = "";
      document.form2.z01_numcgm.focus();
    }
  }

  function js_mostramatricula(mostra) {
    document.form2.z01_numcgm.value = "";
    document.form2.q02_inscr.value = "";
    if (mostra == true) {
      document.form2.j01_matric.value = "";
      js_OpenJanelaIframe('', 'db_iframe_matric', 'func_iptubase.php?funcao_js=parent.js_preenchematricula|0|1|2|3|4', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe_matric', 'func_iptubase.php?pesquisa_chave=' + document.form2.j01_matric.value + '&funcao_js=parent.js_preenchematricula2', 'Pesquisa', false);
    }
  }

  function js_preenchematricula(chave, chave1, chave2, chave3, chave4) {
    document.form2.j01_matric.value = chave;
    document.form2.z01_nome.value = chave4;
    db_iframe_matric.hide();
  }

  function js_preenchematricula2(chave, chave1) {
    if (chave1 == false) {
      document.form2.z01_nome.value = chave;
      db_iframe_matric.hide();
    } else {
      document.form2.z01_nome.value = chave;
      document.form2.j01_matric.value = "";
      db_iframe_matric.hide();
    }
    if (document.form2.j01_matric.value == '' && document.form2.z01_nome.value == '') {
      document.form2.z01_nome.value = '';
    }
  }

  function js_mostrainscricao(mostra) {
    document.form2.j01_matric.value = "";
    document.form2.z01_numcgm.value = "";
    if (mostra == true) {
      js_OpenJanelaIframe('', 'db_iframe', 'func_issbase.php?funcao_js=parent.js_mostra|q02_inscr|z01_nome|q02_dtbaix&todas_inscricoes=true', 'Pesquisa', true);
    } else {
      js_OpenJanelaIframe('', 'db_iframe', 'func_issbase.php?pesquisa_chave=' + document.form2.q02_inscr.value + '&funcao_js=parent.js_mostra&todas_inscricoes=true', 'Pesquisa', false);
    }
  }

  function js_mostra(chave1, chave2, baixa) {
    if (chave2 != false) {
      document.form2.q02_inscr.value = chave1;
      document.form2.z01_nome.value = chave2;
      db_iframe.hide();
    } else {
      document.form2.z01_nome.value = chave1;
    }

    if (document.form2.q02_inscr.value == '') {
      document.form2.z01_nome.value = '';
    }

    if (typeof (baixa) == "undefined" && chave2 == true) {
      document.form2.z01_nome.value = chave1;
      document.form2.q02_inscr.value = '';
    }

    db_iframe.hide();
  }

</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <input type="hidden" name="mostra" value="true">
          <tr>
            <td  width="4%" align="right" nowrap title="<?php echo $Tz01_nome; ?>">
              <?php db_ancora($Lz01_nome, 'js_mostranomes(true);', 4); ?>
            </td>
            <td align="left" nowrap>

              <?php
                db_input("z01_numcgm",10,$Iz01_numcgm,true,"text",4, "onchange=js_mostranomes(false)");
                db_input("z01_nome", 40, $Iz01_nome, true, 'text', 5);
              ?>
            </td>
          </tr>
          <tr>
            <td align="right" title="<?php echo $Tj01_matric; ?>">
              <?php db_ancora($Lj01_matric, "js_mostramatricula(true);", 2); ?>
            </td>
            <td>
              <?php
                db_input("j01_matric",10,$Ij01_matric,true,"text",4, "onchange=js_mostramatricula(false)");
              ?>
            </td>
          </tr>
          <tr>
              <td width="4%" align="right" nowrap title="<?php echo $Tq02_inscr; ?>">
                  <?php db_ancora($Lq02_inscr, 'js_mostrainscricao(true);', 4); ?>
              </td>
              <td>
                  <?php
                    db_input("q02_inscr",10,$Iq02_inscr,true,"text",4,"onchange=js_mostrainscricao(false)");
                  ?>
              </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tar18_sequencial?>">
              <?=$Lar18_sequencial?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
                db_input("ar18_sequencial",10,$Iar18_sequencial,true,"text",4,"","chave_ar18_sequencial");
              ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tar18_procjur?>">
              <?=$Lar18_procjur?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
                db_input("ar18_procjur",10,$Iar18_procjur,true,"text",4,"","chave_ar18_procjur");
              ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_suspensao.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
          if (file_exists("funcoes/db_func_suspensao.php")==true) {
            include(modification("funcoes/db_func_suspensao.php"));
          } else {
            $campos = "suspensao.*";
          }
        }

        $query = array();
        $order = "ar18_sequencial";

        if(isset($chave_ar18_sequencial) && (trim($chave_ar18_sequencial)!="") ){
          $query[] = " ar18_sequencial like '$chave_ar18_sequencial'";
        }

        if(isset($chave_ar18_procjur) && (trim($chave_ar18_procjur)!="") ){
          $query[] = " ar18_procjur like '$chave_ar18_procjur%' ";
          $order = "ar18_procjur";
        }

        if(isset($j01_matric) && trim($j01_matric) != ""){
          $query[] = " arrematric.k00_matric = $j01_matric ";
        }

        if(isset($z01_numcgm) && trim($z01_numcgm) != ""){
          $query[] = " arresusp.k00_numcgm = $z01_numcgm ";
        }

        if(isset($q02_inscr) && trim($q02_inscr) != ""){
          $query[] = " arreinscr.k00_inscr = $q02_inscr ";
        }

        if(isset($situacao) && trim($situacao) != ""){
          $query[] = " ar18_situacao = {$situacao} ";
        } else {
          $query[] = " ar18_situacao = 1 ";
        }

        $query = implode(' AND ', $query);
        $sql = $clsuspensao->sql_query_susp("",$campos, $order,$query);


        $repassa = array();
        if(isset($chave_ar18_procjur)){
          $repassa = array("chave_ar18_sequencial"=>$chave_ar18_sequencial,"chave_ar18_procjur"=>$chave_ar18_procjur);
        }
        
        if(!!$mostra || $nova_quantidade_linhas){
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        }
        
      } else {
        if($pesquisa_chave!=null && $pesquisa_chave!=""){

          if(isset($situacao) && trim($situacao) != ""){
            $sSql = $clsuspensao->sql_query(null,"*",null," ar18_sequencial = {$pesquisa_chave} and ar18_situacao = {$situacao}");
          } else {
          	$sSql = $clsuspensao->sql_query(null, "*", null, " ar18_sequencial = {$pesquisa_chave} and ar18_situacao = 1");
          }

          $result = $clsuspensao->sql_record($sSql);

          if($clsuspensao->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$ar18_sequencial',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </td>
   </tr>
</table>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
  ?>
  <script>
  </script>
  <?php
}
?>
<script>
js_tabulacaoforms("form2","chave_ar18_procjur",true,1,"chave_ar18_procjur",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
