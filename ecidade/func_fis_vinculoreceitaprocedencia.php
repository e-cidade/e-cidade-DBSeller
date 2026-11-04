<?php
require_once("libs/db_stdlib.php");
require_once("libs/db_sessoes.php");
require_once("dbforms/db_funcoes.php");

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$Ssequencial = 'Código Receita';
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
      <table border="0" align="center" cellspacing="0">
        <form name="form2" method="post" action="" >
          <tr>
            <td nowrap>
              <strong>Código Receita:</strong>
            </td>
            <td align="left" nowrap>
              <?php db_input("sequencial",10, 1,true,"text",4,"","chave_sequencial"); ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_VinculoReceitaProcedencia.hide();">
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

        $campos  = 'receita    as dl_Receita,             ';
        $campos .= 'k02_drecei as dl_Descricao_Receita,   ';
        $campos .= 'procdiver  as dl_Precedência_Diversos,';
        $campos .= 'dv09_descr as dl_Descricao_Diversos   ';        
        if (!empty($chave_sequencial)) {

          $sql = "select $campos 
                    from vinculoreceitaprocedencia 
                   inner join tabrec    on k02_codigo = receita  
                   inner join procdiver on procdiver  = dv09_procdiver
                   where receita = {$chave_sequencial}";
        } else {

          $sql = "select $campos 
                    from vinculoreceitaprocedencia
                   inner join tabrec    on k02_codigo = receita  
                   inner join procdiver on procdiver  = dv09_procdiver";
        }

        $repassa = array();
        if(isset($chave_sequencial)){
          $repassa = array("chave_sequencial"=>$chave_sequencial,"chave_sequencial"=>$chave_sequencial);
        }

        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa, true);

      }else{

        if($pesquisa_chave!=null && $pesquisa_chave!=""){

          $sql = "select * 
                    from vinculoreceitaprocedencia 
                   inner join tabrec    on k02_codigo = receita  
                   inner join procdiver on procdiver  = dv09_procdiver
                   where receita = {$pesquisa_chave}";
          $result = db_query($sql);
          if(pg_numrows($result) !=0){

            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$sequencial',false);</script>";
          }else{
            echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") nÃ£o Encontrado',true);</script>";
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
  js_tabulacaoforms("form2","chave_sequencial",true,1,"chave_sequencial",true);
</script>
