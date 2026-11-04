<?php 
/*
*     E-cidade Software Publico para Gestao Municipal                
*  Copyright (C) 2009 DBSeller Servicos de Informatica             
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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("classes/db_fis_fiscal_classe.php");
include modification("dbforms/db_funcoes.php");
include modification("dbforms/db_classesgenericas.php");
db_postmemory($HTTP_POST_VARS);
// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
// -------------
//$aux = new cl_arquivo_auxiliar;
$cldbfiscal = new cl_fis_fiscal;
$cldbfiscal->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("y30_codnoti");
$clrotulo->label("");
$clrotulo->label("");
$clrotulo->label("");
$clrotulo->label("");

// Ticket 108335
if(isset($intimacao) && $intimacao == 1){
    $Ty30_codnoti = 'Código da Intimação

    Campo:y30_codnoti                             ';

    $Ty30_nome = 'Nome da Pessoa Intimada

    Campo:y30_nome                             ';

    $Ly30_codnoti = '<strong>Código da Intimação:</strong>';
    $Ly30_nome    = '<strong>Nome da Pessoa Intimada:</strong>';
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
<body onLoad="a=1" bgcolor="#cccccc">
    <div class="container">
        <style type="text/css">
            fieldset {border-radius:7px;padding:20px;}
        </style>
        <br />
        <br />
        <?php $legend = (isset($intimacao) and $intimacao == 1) ? 'Intimação' : 'Notificação'; ?>
        <form name="form1" method="post" action="" class="container">
            <fieldset>
                <legend><?php echo $legend; ?></legend>
                <table>
                    <tr> 
                        <td nowrap align="right" title="<?=@$y30_codnoti?>"><?php db_ancora('Código',"js_codfiscal(true);",1);?></td>
                        <td>
                            <?php 
                                db_input('y30_codnoti',6,$Iy30_codnoti,true,'text',1," onchange='js_codfiscal(false);'");
                                db_input('y30_nome',35,$Iy30_nome,true,'text',3,'');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$y100_sequencial?>"><?php db_ancora('Processo fiscal: ',"js_codprocfiscal(true);",1);?></td>
                        <td>
                            <?php
                                db_input('y100_sequencial',6,$Iy50_codauto,true,'text',1,"onblur='js_codprocfiscal(false);'");
                                db_input('z01_nome',35,$Iy50_nome,true,'text',3,'');
                            ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
            <br />
            <input name="consultar" type="submit" value="Processar" onclick="js_mandadados();" >
        </form>
        <?php 
            db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
        ?>
    </div>
</body>
</html>
<script>
function js_mandadados(){
    if (document.form1.y30_codnoti.value == "" && document.form1.y100_sequencial.value == "") {
        alert("Preencha um dos campos do formulário!");
        if (document.form1.y30_codnoti.value == '') {
            document.form1.y30_codnoti.focus();
        }
        if (document.form1.y100_sequencial.value == "") {
            document.form1.y100_sequencial.focus();    
        }
    } else {
        jan = window.open('fis2_fis_fiscalinf002.php?codfiscal='+document.form1.y30_codnoti.value+'<?=$getIntimacao?>','','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
        jan.moveTo(0,0);
    } 
}
function js_mostracodfiscal1(chave1,chave2){
    document.form1.y30_codnoti.value = chave1;
    document.form1.y30_nome.value = chave2;
    db_iframe_fiscal.hide();
}
function js_mostracodfiscal(chave,erro){
    document.form1.y30_nome.value = chave; 
    if(erro==true){ 
        document.form1.y30_codnoti.focus(); 
        document.form1.y30_codnoti.value = ''; 
    }
}
function js_codfiscal(mostra){
    if(mostra==true){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_fiscal','func_fis_fiscalalt.php?funcao_js=parent.js_mostracodfiscal1|dl_Codigo|z01_nome<?=$getIntimacao?>','Pesquisa',true);
    }else{
        y30_codnoti = document.form1.y30_codnoti.value;
        if(y30_codnoti!=""){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_fiscal','func_fis_fiscalalt.php?pesquisa_chave='+y30_codnoti+'&funcao_js=parent.js_mostracodfiscal<?=$getIntimacao?>','Pesquisa',false);
        }else{ 	
            document.form1.y30_nome.value='';
        } 	
    }
} 

function js_codprocfiscal(mostra){
    if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_auto', 'func_fis_intimacao.php?funcao_js=parent.js_mostracodprocfiscal1|dl_Codigo|dl_Processo_Fiscal|z01_nome<?=$getIntimacao?>','Pesquisa',true)
    } else {
        y100_sequencial = document.form1.y100_sequencial.value;
        if (y100_sequencial != '') {
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_auto','func_fis_intimacao.php?pesquisa_chave='+y100_sequencial+'&funcao_js=parent.js_mostracodprocfiscal1|dl_Codigo|dl_Processo_Fiscal|z01_nome<?=$getIntimacao?>','Pesquisa',true);
        } else {
	   document.getElementById('y30_codnoti').value = '';
	   document.getElementById('z01_nome').value = '';
	   document.getElementById('y30_nome').value = '';
	}
    }
}

function js_mostracodprocfiscal1(chave1,chave2,chave3){
    document.form1.y30_codnoti.value = chave1
    document.form1.y100_sequencial.value = chave2;
    document.form1.z01_nome.value    = chave3;
    document.form1.y30_nome.value = chave3;
    db_iframe_auto.hide();
}

function js_mostracodprocfiscal(chave,erro){
    document.form1.z01_nome.value = chave;
    if(erro==true){
        document.form1.y100_sequencial.focus();
        document.form1.y100_sequencial.value = '';
	document.form1.y30_codnoti.value = '';
	document.form1.z01_nome.value = '';
	document.form1.y30_nome.value = '';
    }
}
</script>
