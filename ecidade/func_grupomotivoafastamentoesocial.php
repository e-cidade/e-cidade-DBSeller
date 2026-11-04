<?php
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_grupomotivoafastamentoesocial_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clgrupomotivoafastamentoesocial = new cl_grupomotivoafastamentoesocial;
$clgrupomotivoafastamentoesocial->rotulo->label("eso10_sequencial");
$clgrupomotivoafastamentoesocial->rotulo->label("eso10_descricao");
?>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
  <link href='estilos.css' rel='stylesheet' type='text/css'>
  <script language='JavaScript' type='text/javascript' src='scripts/scripts.js'></script>
</head>
<body>
  <form name="form2" method="post" action="" class="container">
    <fieldset>
      <legend>Dados para Pesquisa</legend>
      <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
        <tr style="display: none">
          <td><label><?=$Leso10_sequencial?></label></td>
          <td><?php db_input("eso10_sequencial",10,$Ieso10_sequencial,true,"hidden",4,"","chave_eso10_sequencial"); ?></td>
        </tr>
        <tr>
          <td><label><?=$Leso10_descricao?></label></td>
          <td><?php db_input("eso10_descricao",10,$Ieso10_descricao,true,"text",4,"","chave_eso10_descricao");?></td>
        </tr>
      </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_grupomotivoafastamentoesocial.hide();">
  </form>
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_grupomotivoafastamentoesocial.php")==true){
             include(modification("funcoes/db_func_grupomotivoafastamentoesocial.php"));
           }else{
           $campos = "grupomotivoafastamentoesocial.*";
           }
        }

        $sWhere = "";
        if (isset($chave_eso10_sequencial) && (trim($chave_eso10_sequencial)!="")) {
            $sWhere = " and eso10_sequencial = {$chave_eso10_sequencial} ";
        } else if(isset($chave_eso10_descricao) && (trim($chave_eso10_descricao)!="")) {
            $sWhere = " and eso10_descricao ilike '%{$chave_eso10_descricao}%' ";
        }
        $sql = "select eso10_sequencial as db_eso10_sequencial,concat(eso10_descricao, ' - código(s) eSocial:', string_agg(db18_opcao,', 'order by db18_opcao::integer))::varchar as eso10_descricao from grupomotivoafastamentoesocial inner join db_cadattdinamico on eso10_db_cadattdinamico = db118_sequencial inner join db_cadattdinamicoatributos on db109_db_cadattdinamico = db118_sequencial inner join db_cadattdinamicoatributosopcoes on
                db18_cadattdinamicoatributos = db109_sequencial where db109_nome = 'motivo_esocial' {$sWhere} group by eso10_sequencial, eso10_descricao, eso10_db_cadattdinamico";

        $repassa = array();

        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clgrupomotivoafastamentoesocial->sql_record($clgrupomotivoafastamentoesocial->sql_query($pesquisa_chave));
          if($clgrupomotivoafastamentoesocial->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$eso10_descricao',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
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
js_tabulacaoforms("form2","chave_eso10_descricao",true,1,"chave_eso10_descricao",true);
</script>
