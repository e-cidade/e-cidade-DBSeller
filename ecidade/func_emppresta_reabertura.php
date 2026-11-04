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
require_once(modification("classes/db_emppresta_classe.php"));

db_postmemory($_POST);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clemppresta = new cl_emppresta;
$clemppresta->rotulo->label("e45_numemp");
$clemppresta->rotulo->label("e45_tipo");
$rotulo = new rotulocampo;
$rotulo->label("z01_nome");
$rotulo->label("e60_codemp");
$rotulo->label("e60_numemp");

$sWhereComplementar     = '';
$aStringNull['null']    = " is null";
$aStringNull['notnull'] = " is not null";

if (isset($fechamento) && !empty($fechamento)) {

  $sWhereComplementar .= " and e45_conferido {$aStringNull[$fechamento]} ";
}
if (isset($acertoPrestacaoContas) && !empty($acertoPrestacaoContas)) {
  $sWhereComplementar .= " and e45_acerta {$aStringNull[$acertoPrestacaoContas]} ";
}
if (isset($lEncerrados) && $lEncerrados == 0) {

  $sWhereComplementar .= " and e45_acerta is null ";
}
?>
  <html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script>
      function js_mascara(evt){

        var evt = (evt) ? evt : (window.event) ? window.event : "";

        if( (evt.charCode >46 && evt.charCode <58) || evt.charCode ==0 ){//8:backspace|46:delete|190:.
          return true;
        }else{
          return false;
        }
      }
    </script>
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
      <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
          <form name="form2" method="post" action="" >

            <tr>
              <td width="4%" align="right" nowrap title="<?php echo $Te60_numemp?>"><?php echo $Le60_codemp?> </td>
              <td width="96%" align="left" nowrap>

                <input name="chave_e60_codemp" size="12" type='text'  onKeyPress="return js_mascara(event);" >
                <?php echo $Le60_numemp?>
                <?php db_input("e60_numemp",10,$Ie60_numemp,true,"text",4,"","chave_e60_numemp"); ?>
              </td>
            </tr>
            <tr>
              <td width="4%" align="right" nowrap title="<?php echo $Tz01_nome?>"><?php echo $Lz01_nome?></td>
              <td width="96%" align="left" nowrap>
                <?php db_input("z01_nome",45,"",true,"text",4,"","chave_z01_nome"); ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
                <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                <input name="limpar" type="reset" id="limpar" value="Limpar" >
                <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_emppresta.hide();">
              </td>
            </tr>
          </form>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
        <?php
        /* [Extensão] - Filtro da Despesa */

        if(!isset($pesquisa_chave)){
          if(isset($campos)==false){
            if(file_exists("funcoes/db_func_emppresta.php")==true){
              include(modification("funcoes/db_func_emppresta.php"));
            }else{
              $campos = "emppresta.*,codigo,nomeinst";
            }
          }

          if (isset($exibeMovimento)) {
            $campos = " e45_codmov, {$campos}, e45_sequencial ";
          }

          $dbwhere  = " e60_instit = ".db_getsession("DB_instit");

          if (!isset($fechamento)) {
            $dbwhere .=  " and  e45_conferido is null ";
          }
          if(isset($chave_e60_numemp) && (trim($chave_e60_numemp)!="")){
            $sql = $clemppresta->sql_query_depto(null,$campos,"e45_numemp","$dbwhere and e45_numemp = {$chave_e60_numemp} {$sWhereComplementar}");
          }else if(isset($chave_e60_codemp) && (trim($chave_e60_codemp)!="") ){

            $arr = split("/",$chave_e60_codemp);
            if (count($arr) == 2  && isset($arr[1]) && $arr[1] != '' ){
              $dbwhere_ano = " and e60_anousu = ".$arr[1];
            }else{
              $dbwhere_ano = " and e60_anousu = ".db_getsession("DB_anousu");
            }
            $sql = $clemppresta->sql_query_depto(null,$campos,"e45_numemp","$dbwhere and  e60_codemp='".$arr[0]."' {$dbwhere_ano} {$sWhereComplementar}");

          }else if(isset($chave_z01_nome) && (trim($chave_z01_nome)!="")) {
            $sql = $clemppresta->sql_query_depto(null,$campos,"e45_numemp","$dbwhere and z01_nome like '$chave_z01_nome%' {$sWhereComplementar}");
          }else{
            $sql = $clemppresta->sql_query_depto("",$campos,"e45_numemp","{$dbwhere} {$sWhereComplementar}");
          }
          $coddepto =  db_getsession("DB_coddepto");

          $sql = "
          
          
select min(e45_codmov ) as e45_codmov    ,
       e60_codemp     ,
       e60_anousu     ,
       e60_numemp     ,
       e60_emiss      ,
       e60_vlremp     ,
       e60_vlrpag     ,
       z01_nome       ,
       e44_descr      ,
       codigo         ,
       nomeinst       ,
       e45_acerta     ,
       e82_codord

from (
$sql
) as x 
join empord on e45_codmov = e82_codmov
group by 
         e60_codemp     ,
         e60_anousu     ,
         e60_numemp     ,
         e60_emiss      ,
         e60_vlremp     ,
         e60_vlrpag     ,
         z01_nome       ,
         e44_descr      ,
         codigo         ,
         nomeinst       ,
         e45_acerta     ,
         e82_codord

          
          ";
          db_lovrot($sql,15,"()","",$funcao_js);
        } else {

          if($pesquisa_chave!=null && $pesquisa_chave!=""){

            $sql = $clemppresta->sql_query_depto("",$campos,"e45_numemp","$dbwhere and e45_numemp = $pesquisa_chave {$sWhereComplementar}");

            if($clemppresta->numrows!=0){

              db_fieldsmemory($result,0);
              echo "<script>".$funcao_js."('$e45_tipo',false);</script>";
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
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
