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
$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');
$clrotulo->label('r08_descr');
db_postmemory($HTTP_POST_VARS);
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
function js_verifica(){
  var anoi = new Number(document.form1.datai_ano.value);
  var anof = new Number(document.form1.dataf_ano.value);
  if(anoi.valueOf() > anof.valueOf()){
    alert('Intervalo de data invalido. Velirique !.');
    return false;
  }
  return true;
}


function js_emite(){
  qry = 'base1='+document.form1.base01.value+
	'&base2='+document.form1.base02.value+
	'&base3='+document.form1.base03.value+
	'&base4='+document.form1.base04.value+
	'&perc1='+document.form1.perc1.value+
	'&perc2='+document.form1.perc2.value+
	'&perc3='+document.form1.perc3.value+
	'&perc4='+document.form1.perc4.value+
	'&filtro='+document.form1.filtro.value+
	'&ano='+document.form1.DBtxt23.value+
	'&mes='+document.form1.DBtxt25.value;
  jan = window.open('pes2_osoconsignacao002.php?'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>

  <table  align="center">
    <form name="form1" method="post" action="" >
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
      <tr >
        <td align="right" nowrap title="Digite o Ano / Mes de competência" >
        <strong>Ano / Mês :&nbsp;&nbsp;</strong>
        </td>
        <td>
          <?php
           $DBtxt23 = db_anofolha();
           db_input('DBtxt23',4,$IDBtxt23,true,'text',2,'')
          ?>
          &nbsp;/&nbsp;
          <?php
           $DBtxt25 = db_mesfolha();
           db_input('DBtxt25',2,$IDBtxt25,true,'text',2,'')
          ?>
        </td>
      </tr>
          <tr>
            <td nowrap align="right" title="" width="40%"><b>
              <?php
              db_ancora("Líquido (B100)","js_pesquisabase01(true)",4);
              ?>
	      </b>
            </td>
            <td nowrap> 
              <?php
              db_input('base01',4,@$base01,true,'text',@$db_opcao,"onchange='js_pesquisabase01(false)'");
              db_input("r08_descr",30,$Ir08_descr,true,"text",3,"","descr_base01");
              ?>
            </td>
            <td align="right" nowrap title="Percentual" >
            <strong>Perc. :&nbsp;&nbsp;</strong>
            </td>
            <td>
            <?php
	     @$perc1=50;
             db_input('perc1',3,$perc1,true,'text',2,'')
            ?>
	    </td>
          </tr>
          <tr>
            <td nowrap align="right" title="" width="40%"><b>
              <?php
              db_ancora("SSPMO (B102)","js_pesquisabase02(true)",@$db_opcao);
              ?>
	      </b>
            </td>
            <td nowrap> 
              <?php
              db_input('base02',4,@$base02,true,'text',@$db_opcao,"onchange='js_pesquisabase02(false)'");
              db_input("r08_descr",30,$Ir08_descr,true,"text",3,"","descr_base02");
              ?>
            </td>
            <td align="right" nowrap title="Percentual" >
            <strong>Perc. :&nbsp;&nbsp;</strong>
            </td>
            <td>
            <?php
	     @$perc2=15;
             db_input('perc2',3,$perc2,true,'text',2,'')
            ?>
	    </td>
          </tr>
          <tr>
            <td nowrap align="right" title="" width="40%"><b>
              <?php
              db_ancora("AFMO (B103)","js_pesquisabase03(true)",@$db_opcao);
              ?>
	      </b>
            </td>
            <td nowrap> 
              <?php
              db_input('base03',4,@$base03,true,'text',@$db_opcao,"onchange='js_pesquisabase03(false)'");
              db_input("r08_descr",30,$Ir08_descr,true,"text",3,"","descr_base03");
              ?>
            </td>
            <td align="right" nowrap title="Percentual" >
            <strong>Perc. :&nbsp;&nbsp;</strong>
            </td>
            <td>
            <?php
	     @$perc3=15;
             db_input('perc3',3,$perc3,true,'text',2,'')
            ?>
	    </td>
          </tr>
          <tr>
            <td nowrap align="right" title="" width="40%"><b>
              <?php
              db_ancora("CONSIGNAÇÕES (B104)","js_pesquisabase04(true)",@$db_opcao);
              ?>
	      </b>
            </td>
            <td nowrap> 
              <?php
              db_input('base04',4,@$base04,true,'text',@$db_opcao,"onchange='js_pesquisabase04(false)'");
              db_input("r08_descr",30,$Ir08_descr,true,"text",3,"","descr_base04");
              ?>
            </td>
            <td align="right" nowrap title="Percentual" >
            <strong>Perc. :&nbsp;&nbsp;</strong>
            </td>
            <td>
            <?php
	     @$perc4=20;
             db_input('perc4',3,$perc4,true,'text',2,'')
            ?>
	    </td>
          </tr>
      <tr>
         <td align="right">
	 <strong>Filtro :</strong>
	 </td>
	 <td  align="left">
               <?php
                 $x = array("c"=>"Com Diferença","t"=>"Todos");
                 db_select('filtro',$x,true,4,"");
               ?>
	 
	 </td>
      </tr>
      <tr>
        <td colspan="2"align = "center"> 
          <input  name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();" >
        </td>
      </tr>

  </form>
    </table>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>

function js_pesquisabase01(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bases','func_bases.php?funcao_js=parent.js_mostrabase011|r08_codigo|r08_descr','Pesquisa',true);
  }else{
    if(document.form1.base01.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_base01','func_bases.php?pesquisa_chave='+document.form1.base01.value+'&funcao_js=parent.js_mostrabase01','Pesquisa',false);
    }else{
      document.form1.descr_base01.value = ''; 
    }
  }
}
function js_mostrabase01(chave,erro){
  document.form1.descr_base01.value = chave; 
  if(erro==true){ 
    document.form1.base01.focus(); 
    document.form1.base01.value = ''; 
  }
}
function js_mostrabase011(chave1,chave2){
  document.form1.base01.value = chave1;
  document.form1.descr_base01.value = chave2;
  db_iframe_bases.hide();
}



function js_pesquisabase02(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bases','func_bases.php?funcao_js=parent.js_mostrabase021|r08_codigo|r08_descr','Pesquisa',true);
  }else{
    if(document.form1.base02.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_base02','func_bases.php?pesquisa_chave='+document.form1.base02.value+'&funcao_js=parent.js_mostrabase02','Pesquisa',false);
    }else{
      document.form1.descr_base02.value = ''; 
    }
  }
}
function js_mostrabase02(chave,erro){
  document.form1.descr_base02.value = chave; 
  if(erro==true){ 
    document.form1.base02.focus(); 
    document.form1.base02.value = ''; 
  }
}
function js_mostrabase021(chave1,chave2){
  document.form1.base02.value = chave1;
  document.form1.descr_base02.value = chave2;
  db_iframe_bases.hide();
}


function js_pesquisabase03(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bases','func_bases.php?funcao_js=parent.js_mostrabase031|r08_codigo|r08_descr','Pesquisa',true);
  }else{
    if(document.form1.base03.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_base03','func_bases.php?pesquisa_chave='+document.form1.base03.value+'&funcao_js=parent.js_mostrabase03','Pesquisa',false);
    }else{
      document.form1.descr_base03.value = ''; 
    }
  }
}
function js_mostrabase03(chave,erro){
  document.form1.descr_base03.value = chave; 
  if(erro==true){ 
    document.form1.base03.focus(); 
    document.form1.base03.value = ''; 
  }
}
function js_mostrabase031(chave1,chave2){
  document.form1.base03.value = chave1;
  document.form1.descr_base03.value = chave2;
  db_iframe_bases.hide();
}


function js_pesquisabase04(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bases','func_bases.php?funcao_js=parent.js_mostrabase041|r08_codigo|r08_descr','Pesquisa',true);
  }else{
    if(document.form1.base04.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_base04','func_bases.php?pesquisa_chave='+document.form1.base04.value+'&funcao_js=parent.js_mostrabase04','Pesquisa',false);
    }else{
      document.form1.descr_base04.value = ''; 
    }
  }
}

function js_mostrabase04(chave,erro){
  document.form1.descr_base04.value = chave; 
  if(erro==true){ 
    document.form1.base04.focus(); 
    document.form1.base04.value = ''; 
  }
}

function js_mostrabase041(chave1,chave2){
  document.form1.base04.value = chave1;
  document.form1.descr_base04.value = chave2;
  db_iframe_bases.hide();
}












</script>