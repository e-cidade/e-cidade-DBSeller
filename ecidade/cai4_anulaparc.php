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
//echo "parcel =$parcel  usu = $usu motivo = $motivo processo = $v22_processo"; exit;
$processo = $v22_processo;

// tem q receber o motivo e o processo....
//db_msgbox("parcel =$parcel e usuario = $usu motivo = $motivo processo =$processo");
if($parcel!=""){
	//echo "<script>alert('tem parcelamento');</script>";
	
	$sSqlVerificaAnulacao = "select * from termoanu where v09_parcel = ".$parcel;
	$rsVerificaAnulacao   = db_query($sSqlVerificaAnulacao);
	if ( pg_num_rows($rsVerificaAnulacao) > 0 ) {
		db_msgbox("Parcelamento ja anulado, verifique situação deste numpre");
    echo "<script>
            parent.db_iframe_anulaparc1.hide();
            parent.db_iframe_mostrainscr.hide();
          </script>";
	}
	
	db_query('begin');
	$processo = ($processo=="")?"null":$processo;
	$sql = "select fc_excluiparcelamento($parcel,$usu,'".$motivo."',$processo) as retorno";
  	$result = db_query($sql);
  	if (trim(substr(pg_result($result,0),0,1)) == "1") {
		db_query("commit");
		db_msgbox("Parcelamento anulado com sucesso.");
		echo "<script>parent.db_iframe_anulaparc1.hide();
					  parent.db_iframe_mostrainscr.hide();
		</script>";
   		echo "<script>parent.document.formatu.pesquisar.click();</script>";
		
		exit;
  	} else {
    	db_query('rollback');
    	//echo "<script>alert('Erro durante a exclusao do parcelamento!');</script>";
    	db_msgbox("Erro durante a exclusao do parcelamento! " . pg_result($result,0));
  	}
}else{
	echo "<script>alert('Não tem parcelamento');</script>";
}

?>