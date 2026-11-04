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

require_once(modification(Modification("libs/db_stdlib.php")));
require_once(modification(Modification("libs/db_conecta.php")));
require_once(modification(Modification("libs/db_sessoes.php")));
require_once(modification(Modification("libs/db_usuariosonline.php")));
require_once(modification(Modification("dbforms/db_funcoes.php")));
require_once(modification(Modification("classes/db_fis_lancamento_classe.php")));
require_once(modification(Modification("classes/db_fis_lanclocal_classe.php")));
require_once(modification(Modification("classes/db_fis_lancexec_classe.php")));
require_once(modification(Modification("classes/db_fis_procfiscallanc_classe.php")));
require_once(modification(Modification("classes/db_fis_enderecopecas_classe.php")));
require_once(modification(Modification("classes/db_fis_paragrafolanc_classe.php")));


parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

if(!isset($abas)){
  echo "<script>location.href='fis1_fis_lancamento005.php?db_opcao=2'</script>";
  exit;
}

db_postmemory($HTTP_POST_VARS);

$cllancamento     = new cl_fis_lancamento;
$cllanclocal      = new cl_fis_lanclocal;
$cllancexec       = new cl_fis_lancexec;
$clprocfiscallanc = new cl_fis_procfiscallanc;
$clenderecopecas  = new cl_fis_enderecopecas;
$clparagrafolanc  = new cl_fis_paragrafolanc;
$db_opcao         = 22;
$db_botao         = false;

echo "
<script>
  parent.document.formaba.lanclevanta.disabled = true;
  parent.document.formaba.lanctipo.disabled    = true;
  parent.document.formaba.fiscais.disabled     = true;
  parent.document.formaba.responsavel.disabled = true;
  parent.document.formaba.precalculo.disabled  = true;
</script>
";

$sqlerro = false;
$passa   = false;
if( (isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar" ){


  db_inicio_transacao();
  $db_opcao = 2;
  $cllancamento->alterar($nl01_codlanc);
  if ($cllancamento->erro_status==0){
    $sqlerro  = true;
    $erro_msg = $cllancamento->erro_msg;
  }

  // exclui e inclui no procfiscal
   $sqlprocfiscalv    = "select nl09_sequencial from fiscalizacao.fis_procfiscallanc where nl09_lanc = $nl01_codlanc";
   $resultprocfiscalv = db_query($sqlprocfiscalv);
   $linhasprocfiscalv = pg_num_rows($resultprocfiscalv);
   if( $linhasprocfiscalv > 0 ){

     db_fieldsmemory($resultprocfiscalv,0);
     $clprocfiscallanc->nl09_sequencial = $nl09_sequencial;
     $clprocfiscallanc->excluir($nl09_sequencial);
     if( $clprocfiscallanc->erro_status == 0 ){
        $erro    = $clprocfiscallanc->erro_msg;
        $sqlerro = true;
     }
   }

   if( $procfiscal != "" ){

      $clprocfiscallanc->nl09_procfiscal = $procfiscal;
      $clprocfiscallanc->nl09_lanc        = $nl01_codlanc;
      $clprocfiscallanc->incluir(null);
      if ($clprocfiscallanc->erro_status==0){
        $sqlerro = true;
        $erro    = $clprocfiscallanc->erro_msg;
      }
    }

  if( $sqlerro == false && $nl03_codigo != "" && $nl03_codi != "" ){
    $result = $cllanclocal->sql_record($cllanclocal->sql_query($nl01_codlanc));

      $cllanclocal->nl02_codlanc = $nl01_codlanc;
      $cllanclocal->nl02_codigo  = $nl03_codigo;
      $cllanclocal->nl02_codi    = $nl03_codi;
      $cllanclocal->nl02_numero  = $nl03_numero;
      $cllanclocal->nl02_compl   = $nl03_compl;

    if($cllanclocal->numrows > 0){      
      $cllanclocal->alterar($nl01_codlanc);
      if ($cllanclocal->erro_status==0){

        $sqlerro  = true;
        $erro_msg = $cllanclocal->erro_msg;
      }
    }else{



      $cllanclocal->incluir($nl01_codlanc);
      if ($cllanclocal->erro_status==0){

        $sqlerro  = true;
        $erro_msg = $cllanclocal->erro_msg;
      }
     }
  }else{

      $clenderecopecas->end01_codpeca  = $nl01_codlanc;
      $clenderecopecas->end01_tipopeca = "L";
      $clenderecopecas->end01_rua      = $j14_nome;
      $clenderecopecas->end01_numero   = $nl02_numero;
      $clenderecopecas->end01_compl    = $nl02_compl;
      $clenderecopecas->end01_bairro   = $j13_descr;

      $rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$nl01_codlanc} and end01_tipopeca = 'L'");
      
      if( pg_num_rows($rsEndPecas) > 0 ){
        $clenderecopecas->alterar();
      }else{
        $clenderecopecas->incluir();
      }

      if( $clenderecopecas->erro_status == "0" ){
        $sqlerro = true;
        $erro    = $clenderecopecas->erro_msg;
      }
  }


    $cllancexec->nl03_codlanc = $nl01_codlanc;
    $cllancexec->nl03_codigo  = $nl03_codigo;
    $cllancexec->nl03_codi    = $nl03_codi;
    $cllancexec->nl03_numero  = $nl03_numero;
    $cllancexec->nl03_compl   = $nl03_compl;
    $cllancexec->alterar($nl01_codlanc);
    if ($cllancexec->erro_status==0){

      $sqlerro  = true;
      $erro_msg = $cllancexec->erro_msg;
    }

  if ( $sqlerro == false ){
    $passa=true;
  }

    if($sqlerro==false){
      $rowParagrafo = count($paragrafoTipo);

      $pSql     = 'select * from fiscalizacao.fis_paragrafolanc where pl30_codlanc = '.$nl01_codlanc;
      $pRs      = db_query($pSql);
      $pRowsDel = pg_num_rows($pRs);
      $pDel     = array();
      
      if($pRowsDel > 0){
          for($i=0; $i < pg_num_rows($pRs); $i++){ 
              db_fieldsmemory($pRs, $i);
              $pDel[] = $pl30_codigo;
          }
      }
      $pArr = array();
      for($i=0; $i < $rowParagrafo; $i++){
          $clparagrafolanc->pl30_codigo    = '';
          $clparagrafolanc->pl30_codlanc   = $nl01_codlanc;
          $clparagrafolanc->pl30_paragrafo = $paragrafoTipo[$i];
          $clparagrafolanc->pl30_texto     = $paragrafoTexto[$i];
          $clparagrafolanc->pl30_usu       = db_getsession('DB_id_usuario');

          if($paragrafoCod[$i] == ''){
              $clparagrafolanc->incluir();
              if($clparagrafolanc->erro_status==0){
                  $sqlerro=true;
                  $erro_msg=$clparagrafolanc->erro_msg;
                  break;
              }
          }else{
              $clparagrafolanc->pl30_codigo = $paragrafoCod[$i];
              $clparagrafolanc->alterar();
              if($clparagrafolanc->erro_status==0){
                  $sqlerro=true;
                  $erro_msg=$clparagrafolanc->erro_msg;
                  break;
              }
              $pArr[] = $paragrafoCod[$i];
          }
      }

      if($sqlerro==false){
          $delArr = array_diff($pDel, $pArr);
          
          foreach($delArr as $key => $value) {
              $clparagrafolanc->pl30_codigo = $value;
              $clparagrafolanc->excluir();

              if($clparagrafolanc->erro_status==0){
                  $sqlerro=true;
                  $erro_msg=$clparagrafolanc->erro_msg;
                  break;
              }
          }
      } 
  }

  db_fim_transacao($sqlerro);
}else if(isset($chavepesquisa)){

   $db_opcao = 2;

   $result   = $cllancamento->sql_record($cllancamento->sql_query($chavepesquisa,"*",null," nl01_codlanc = $chavepesquisa and nl01_instit = ".db_getsession('DB_instit') ));

   if ($cllancamento->numrows>0){
     db_fieldsmemory($result,0);
   }

   $result = $cllanclocal->sql_record($cllanclocal->sql_query($chavepesquisa,"*"));
   if($cllanclocal->numrows > 0){
     db_fieldsmemory($result,0);
   }

   $result = $cllancexec->sql_record($cllancexec->sql_query($chavepesquisa,"fis_lancexec.* , j14_nome as j14_nome_exec,j13_descr as j13_descr_exec"));
   if($cllancexec->numrows > 0){
     db_fieldsmemory($result,0);
   }

   $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as nl02_numero,end01_compl as  nl02_compl,end01_bairro as j13_descr from fiscalizacao.fis_enderecopecas where end01_codpeca = {$chavepesquisa} and end01_tipopeca = 'L' " );

  
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
    
      db_fieldsmemory( $rs2EndPecas,0 );
   }

   $db_botao = true;

    echo "
    <script>
     
      parent.iframe_lanclevanta.location.href      = 'fis1_fis_lanclevanta001.php?nl01_codlanc=".$chavepesquisa."&abas=1';\n
      parent.iframe_lanctipo.location.href         = 'fis1_fis_lanctipo001.php?nl18_codlanc=".$chavepesquisa."&abas=1';\n
      parent.iframe_fiscais.location.href          = 'fis1_fis_lancusu001.php?nl14_codlanc=".$chavepesquisa."&abas=1';\n
      parent.iframe_testem.location.href           = 'fis1_fis_lanctestem001.php?nl21_codlanc=".$chavepesquisa."&abas=1';\n
      parent.iframe_responsavel.location.href      = 'fis1_fis_lancrespons001.php?nl12_codlanc=".$chavepesquisa."&abas=1';\n
      parent.iframe_precalculo.location.href       = 'fis1_fis_lancprecalc001.php?nl01_codlanc=".$chavepesquisa."&abas=1';\n

  
      parent.document.formaba.lanclevanta.disabled  = false;
      parent.document.formaba.lanctipo.disabled     = false;
      parent.document.formaba.fiscais.disabled      = false;
      parent.document.formaba.testem.disabled       = false;
      parent.document.formaba.responsavel.disabled  = false;
      parent.document.formaba.precalculo.disabled   = false;
    </script>
      ";

 $sqlprocfiscal = "select nl09_procfiscal as procfiscal,z01_nome as nome
                     from fiscalizacao.fis_procfiscallanc
                          inner join fiscalizacao.fis_procfiscalcgm on nl09_procfiscal = y101_procfiscal
                          inner join cgm           on y101_numcgm     = z01_numcgm
                    where nl09_lanc = $chavepesquisa ";
                   
   $resultprocfiscal = db_query($sqlprocfiscal);
   $linhasprocfiscal = pg_num_rows($resultprocfiscal);
   if($linhasprocfiscal>0){
    db_fieldsmemory($resultprocfiscal,0);
   }else{
    $nome="";
   }
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
  <div class="container">
    <?php
    // if ( $passa == false ){
      include(modification(Modification("forms/db_frm_fis_lancamento.php")));
    // }
    ?>
  </div>
</body>
</html>
<?php 
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){

  if( $cllancamento->erro_status == "0" || $sqlerro == true ){

    $cllancamento->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllancamento->erro_campo!=""){

      echo "<script> document.form1.".$cllancamento->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllancamento->erro_campo.".focus();</script>";
    }elseif($cllancexec->erro_campo!=""){

      echo "<script> document.form1.".$cllancexec->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllancexec->erro_campo.".focus();</script>";
    }elseif($cllanclocal->erro_campo!=""){

      echo "<script> document.form1.".$cllanclocal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllanclocal->erro_campo.".focus();</script>";
    }

  }else{

    $cllancamento->erro(true,false);
    echo "
         <script>
         function js_src(){

           parent.iframe_lancamento.location.href     = 'fis1_fis_lancamento002.php?chavepesquisa=".$cllancamento->nl01_codlanc."&abas=1';\n
           parent.iframe_lanclevanta.location.href    = 'fis1_fis_lanclevanta001.php?nl01_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
           parent.iframe_lanctipo.location.href       = 'fis1_fis_lancamentotipo001.php?nl18_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
           parent.iframe_fiscais.location.href        = 'fis1_fis_lancusu001.php?nl14_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n
           parent.iframe_testem.location.href         = 'fis1_fis_lanctestem001.php?nl21_codlanc=". $cllancamento->nl01_codlanc."&abas=1';\n
           parent.iframe_responsavel.location.href    = 'fis1_fis_lancrespons001.php?nl12_codlanc=". $cllancamento->nl01_codlanc."&abas=1';\n
           parent.iframe_precalculo.location.href     = 'fis1_fis_lancprecalc001.php?nl01_codlanc=".$cllancamento->nl01_codlanc."&abas=1';\n


            parent.document.formaba.lanclevanta.disabled = false;
            parent.document.formaba.lanctipo.disabled    = false;
            parent.document.formaba.fiscais.disabled     = true;
            parent.document.formaba.testem.disabled      = false;
            parent.document.formaba.responsavel.disabled = false;
            parent.document.formaba.precalculo.disabled  = false;
            parent.mo_camada('lanclevanta');
         }
         js_src();
         </script>
       ";
  }
}
if($db_opcao==22){
  echo "<script>
    document.form1.pesquisar.click();
    </script>";
}
?>
