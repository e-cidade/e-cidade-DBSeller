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
include(modification("classes/db_issbase_classe.php"));
include(modification("classes/db_isscalc_classe.php"));
include(modification("classes/db_arrecad_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$erro = false;
$descricao_erro = false;
if(isset($geracarnes)){
  set_time_limit(0);
  $clabre_arquivo =  new cl_abre_arquivo();
  if($clabre_arquivo->arquivo!=false){
    $clissbase = new cl_issbase;
    $clisscalc = new cl_isscalc;
    $clarrecad = new cl_arrecad;
    $result = $clisscalc->sql_record($clisscalc->sql_query_file(null,null,null,null,null,'q01_inscr#q01_valor#q01_numpre','q01_inscr',' q01_anousu = '.db_getsession("DB_anousu")));
    if($result==false || $clisscalc->numrows == 0 ){
      $erro = true;
      $descricao_erro =  "Não existe cálculo efetuado.";
    }else{
      $quantos = 0;
      for($i=0;$i<pg_numrows($result);$i++){
        db_fieldsmemory($result,$i);
        $resultiss = $clissbase->empresa_record($clissbase->empresa_query($q01_inscr));
	db_fieldsmemory($resultiss,0);
	// verifica issqn fixo
        $resultarr = $clarrecad->sql_record($clarrecad->sql_query("","arrecad.*","arrecad.k00_numpre,k00_numpar"," arrecad.k00_numpre = $q01_numpre and k00_instit = ".db_getsession('DB_instit') ));
	if($clarrecad->numrows>0){
	  $quantos ++;
	  if(pg_result($resultarr,0,'k00_tipo')==2)
	    fputs($clabre_arquivo->arquivo,'ISSQN FIXO    ');
          else
//           fputs($clabre_arquivo->arquivo,'ISSQN VARIAVEL');
            continue;
	    
	  fputs($clabre_arquivo->arquivo,str_pad($quantos,10));
	  fputs($clabre_arquivo->arquivo,str_pad($q02_inscr,10));
	  fputs($clabre_arquivo->arquivo,db_formatar($q01_valor,'f'));
          // endereco de entrega
          fputs($clabre_arquivo->arquivo,str_pad($z01_ender,40));
          fputs($clabre_arquivo->arquivo,str_pad($z01_nome,40));
	  fputs($clabre_arquivo->arquivo,str_pad($z01_numero,10));
	  fputs($clabre_arquivo->arquivo,str_pad($z01_compl,20));
	  fputs($clabre_arquivo->arquivo,str_pad($z01_munic,20));
	  fputs($clabre_arquivo->arquivo,str_pad($z01_cep,8));
	  fputs($clabre_arquivo->arquivo,str_pad($z01_uf,2));
          // endereco da inscricao
//	  fputs($clabre_arquivo->arquivo,str_pad($j14_codigo,2));
//	  fputs($clabre_arquivo->arquivo,str_pad($j14_nome,4));
//	  fputs($clabre_arquivo->arquivo,str_pad($q02_numero,4));
//	  fputs($clabre_arquivo->arquivo,str_pad($q02_compl,4));
//	  fputs($clabre_arquivo->arquivo,str_pad($q02_cep,4));
	
  	  $resultfin = db_query(
	        "select *,
                  substr(fc_calcula,2,13)::float8 as uvlrhis,
                  substr(fc_calcula,15,13)::float8 as uvlrcor,
                  substr(fc_calcula,28,13)::float8 as uvlrjuros,
                  substr(fc_calcula,41,13)::float8 as uvlrmulta,
                  substr(fc_calcula,54,13)::float8 as uvlrdesconto,
                  (substr(fc_calcula,15,13)::float8+
                  substr(fc_calcula,28,13)::float8+
                  substr(fc_calcula,41,13)::float8-
                  substr(fc_calcula,54,13)::float8) as utotal
	          from (
		  select r.k00_numpre,r.k00_dtvenc as dtvencunic, r.k00_dtoper as dtoperunic,r.k00_percdes,
	  	          fc_calcula(r.k00_numpre,0,0,r.k00_dtvenc,r.k00_dtvenc,".db_getsession("DB_anousu").")
                  from recibounica r
		 where r.k00_numpre = $q01_numpre and r.k00_dtvenc >= ".db_getsession("DB_datausu")."
		 ) as unica");
	  
           if($resultfin!=false){

	      if(pg_numrows($resultfin) > 0) {
		    
		 for($unicont=0;$unicont<pg_numrows($resultfin);$unicont++){
		   db_fieldsmemory($resultfin,$unicont);
		   fputs($clabre_arquivo->arquivo,db_formatar($dtvencunic,'d'));
		   fputs($clabre_arquivo->arquivo,str_pad($k00_percdes,6));
		   fputs($clabre_arquivo->arquivo,str_pad(db_formatar($uvlrhis,'f'),15));
		   fputs($clabre_arquivo->arquivo,str_pad(db_formatar($uvlrdesconto,'f'),15));
		   fputs($clabre_arquivo->arquivo,str_pad(db_formatar($utotal,'f'),15));
		   fputs($clabre_arquivo->arquivo,db_numpre($k00_numpre).".000");
		   //$vlrbar = '0'.str_replace(db_formatar($utotal,'f','0',12),'.','');
//		   $vlrbar = "0".str_replace('.','',str_pad(number_format($utotal,2,"",""),11,"0",STR_PAD_LEFT));
		   $vlrbar = str_replace('.','',str_pad(number_format($utotal,2,"",""),11,"0",STR_PAD_LEFT));
//		   $numbanco = "4268" ;// deve ser tirado do db_config
		   $resultnumbco = db_query("select numbanco from db_config where codigo = " . db_getsession("DB_instit"));
		   $numbanco = pg_result($resultnumbco,0) ;// deve ser tirado do db_config
		   
		   $numpre = db_numpre($k00_numpre).'000'; //db_formatar(0,'s',3,'e');
		   $dtvenc = str_replace("-","",$dtvencunic);
		   $resultcod = db_query("select fc_febraban('816'||'$vlrbar'||'".$numbanco."'||$dtvenc||'000000'||'$numpre')");
		   db_fieldsmemory($resultcod,0);
		   fputs($clabre_arquivo->arquivo,$fc_febraban);
		 }
		 
              } else {
	         fputs($clabre_arquivo->arquivo,str_pad(' ',10).str_pad($q01_valor,2).str_pad(' ',157));
              }
	   }
	   
	   $resultfin = db_query("select a.k00_numpre,k00_numpar,k00_numtot,k00_numdig,k00_dtvenc,sum(k00_valor)::float8 as k00_valor 
	                         from arreinscr m 
	 		              inner join arrecad a on m.k00_numpre = a.k00_numpre 
				 where m.k00_numpre = $q01_numpre 
				 group by a.k00_numpre,k00_numpar,k00_numtot,k00_numdig,k00_dtvenc 
								 ");
	   if($resultfin!=false){

	     if(pg_numrows($resultfin) > 0) {
		    
		for($unicont=0;$unicont<pg_numrows($resultfin);$unicont++){
		  db_fieldsmemory($resultfin,$unicont);
		  fputs($clabre_arquivo->arquivo,db_formatar($k00_dtvenc,'d'));
		  fputs($clabre_arquivo->arquivo,db_formatar($k00_valor,'f'));
		  $numpre = db_numpre_sp($k00_numpre,$k00_numpar);
		  $numpref = db_numpre($k00_numpre,$k00_numpar);
		  fputs($clabre_arquivo->arquivo,$numpref);
//		  $vlrbar = "0".str_replace('.','',str_pad(number_format($k00_valor,2,"",""),11,"0",STR_PAD_LEFT));
		  $vlrbar = str_replace('.','',str_pad(number_format($k00_valor,2,"",""),11,"0",STR_PAD_LEFT));
//		  $numbanco = "4268" ;// deve ser tirado do db_config
		  $resultnumbco = db_query("select numbanco from db_config where codigo = " . db_getsession("DB_instit"));
		  $numbanco = pg_result($resultnumbco,0) ;// deve ser tirado do db_config

		  $dtvenc = str_replace("-","",$k00_dtvenc);
		  $resultcod = db_query("select fc_febraban('816'||'$vlrbar'||'".$numbanco."'||$dtvenc||'000000'||'$numpre')");
		  db_fieldsmemory($resultcod,0);
		  fputs($clabre_arquivo->arquivo,$fc_febraban);
		}
	      }
	   }

	   fputs($clabre_arquivo->arquivo,str_pad($q03_descr,40));
           fputs($clabre_arquivo->arquivo,"\n");
	   
	 }
//       if($i > 100)
//          break;
       }
       fclose($clabre_arquivo->arquivo);
       $erro = true;
       $descricao_erro =  "Carnes gerados com sucesso!";
     }
  }else{
    $erro = true;
    $descricao_erro =  "Erro ao Criar arquivo: $arquivo";
  }
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="center" valign="top" bgcolor="#CCCCCC">
   <form name="form1" action="" method="post" >
	    <table width="292" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="25">Quantidade:</td>
            <td height="25"><?=@$quantos?></td>
          </tr>
          <tr> 
            <td width="69" height="25">Arquivo:</td>
            <td width="223" height="25">
			<?php
			if(@$quantos!=0){
			  $clabre_arquivo->arquivo;
			}
			?>
			</td>
          </tr>
          <tr> 
            <td height="25">&nbsp;</td>
            <td height="25"> <input name="geracarnes"  type="submit" id="geracarnes" value="Gera Carnes" onclick="js_mostra_processando();"> 
            </td>
          </tr>
		  <script>
		  function js_mostra_processando(){
		     document.form1.processando.style.visibility='visible';
		  }
		  </script>
          <tr > 
            <td colspan="2" height="25" align="center" colspan="2" > <input name="processando" id="processando" style='color:red;border:none;visibility:hidden' type="button"  readonly value="Processando. Aguarde..."> 
            </td>
          </tr>
        </table>
      </form>
     </td>
  </tr>
</table>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if($erro==true){
  echo "<script>alert('$descricao_erro');</script>";
}
?>