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

//MODULO: fiscal
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clprocfiscalfiscais->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("y100_coddepto");
$clrotulo->label("id_usuario");
$clrotulo->label("z01_nome");
$clrotulo->label("y106_principal");

if(isset($db_opcaoal)){
   $db_opcao=33;
    $db_botao=false;
}else if(isset($opcao) && $opcao=="alterar"){
    $db_botao=true;
    $db_opcao = 2;
}else if(isset($opcao) && $opcao=="excluir"){
    $db_opcao = 3;
    $db_botao=true;
}else{  
    $db_opcao = 1;
    $db_botao=true;
    if(isset($novo) || isset($alterar) ||   isset($excluir) || (isset($incluir) && $sqlerro==false ) ){
     $nome = "";
     $pf01_codigo = "";
     $p58_numero = "";
     $y60_proces = "";
		 $p58_requer = "";
   }
} 
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ty106_sequencial?>">
       <?=@$Ly106_sequencial?>
    </td>
    <td> 
<?php 
db_input('pf01_codigo',10,$Ipf01_codigo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty106_procfiscal?>">
       <?=@$Ly106_procfiscal?>
      
    </td>
    <td> 
<?php 
db_input('y106_procfiscal',10,$Iy106_procfiscal,true,'text',3)
?>
     
    </td>
  </tr>
  <tr> 
        <td nowrap title="<?=@$Ty60_proces?>">
          <?php 
          db_ancora("<b>Processo:</b>",' js_mostracodproc(true); ',4);
          ?>
          </td>
          <td> 
            <?php 
            db_input('p58_numero',14,$Ip58_numero,true,'text',4,'onchange="js_mostracodproc(false);"',"","","",14);
            db_input('y60_proces',10,$Iy60_proces,true,'hidden',4,'');
            
            db_input('p58_requer',40,$Ip58_requer,true,'text',3,'');
            ?>
            
        </td>
      </tr>
  </tr>
    <td colspan="2" align="center">
 <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  >
 <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
    </td>
  </tr>
  </table>
 <table>
  <tr>
    <td valign="top"  align="center">  
    <?php 
		
		$sql = "select pf01_codigo as y106_sequencial,pf01_procfiscal as y106_procfiscal, (select p58_numero || '/' || p58_ano as p58_numero from protprocesso where pf01_processo = p58_codproc) as p58_numero from fiscalizacao.fis_processosfiscais where pf01_procfiscal=$y106_procfiscal";
	 $chavepri= array("y106_sequencial"=>@$y106_sequencial);
	 $cliframe_alterar_excluir->chavepri=$chavepri;
	 $cliframe_alterar_excluir->sql     = $sql;
	 $cliframe_alterar_excluir->campos  ="y106_sequencial,y106_procfiscal,p58_numero";
	 $cliframe_alterar_excluir->legenda="ITENS LANÇADOS";
	 $cliframe_alterar_excluir->iframe_height ="160";
	 $cliframe_alterar_excluir->iframe_width ="700";
	 $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
    </td>
   </tr>
 </table>
  </center>
</form>
<script>
<?php  if(isset($opcao)){ ?>
  js_mostracodproc(false);
<?php } ?>
function js_cancelar(){
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}


function js_mostracodproc(mostra){
 if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo.php?funcao_js=parent.js_mostraproc1|p58_codproc|p58_numero|z01_nome','Pesquisa',true,'15');
  }else{
        js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo.php?pesquisa_chave='+document.form1.p58_numero.value+'&funcao_js=parent.js_mostraproc&chave_p58_numero=1','Pesquisa',false);
  }
}
function js_mostraproc(chave,obs,erro){
  if(erro==true){ 
    document.form1.p58_numero.focus(); 
    document.form1.y60_proces.value = ''; 
    document.form1.p58_numero.value = ''; 
    document.form1.p58_requer.value = 'Chave nao encontrada'; 
  }else{
    document.form1.p58_requer.value = obs; 
    document.form1.y60_proces.value = chave; 
    
  }   
}
function js_mostraproc1(chave1,n,z,chave2){  
  // document.form1.z01_numcgm.value = n;
  // alert(n);
  document.form1.p58_requer.value = z;
  document.form1.y60_proces.value = chave1;
  document.form1.p58_numero.value = n;
  db_iframe_proc.hide();
}
</script>
