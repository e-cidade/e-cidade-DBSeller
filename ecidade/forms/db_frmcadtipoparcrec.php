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

//MODULO: caixa
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clcadtipoparcrec->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("k40_descr");
$clrotulo->label("c60_descr");
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
    if(isset($novo) || isset($_self) && $_self!=""){
     $k180_estorc = "";
     $c60_descr = "";
   }
}
?>
<center>
<form name="form1" method="post" action="">
<br>
<table border="0" >
  <tr>
    <td nowrap title="<?=@$Tk180_cadtipoparc?>">
       <?php
       db_ancora(@$Lk180_cadtipoparc,"js_pesquisak180_cadtipoparc(true);",3);
       ?>
    </td>
    <td>
<?php
db_input('k180_cadtipoparc',10,$Ik180_cadtipoparc,true,'text',3," onchange='js_pesquisak180_cadtipoparc(false);'")
?>
       <?php

       if (isset($k180_cadtipoparc)&&$k180_cadtipoparc!=""){
        $Result_Descr=$clcadtipoparc->sql_record($clcadtipoparc->sql_query_file($k180_cadtipoparc,"k40_descr","k40_descr"));
        if ($clcadtipoparc->numrows>0){
            db_fieldsmemory($Result_Descr,0);
        }
       }
db_input('k40_descr',50,$Ik40_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tk180_estorc?>">
       <?php
       db_ancora(@$Lk180_estorc,"js_pesquisak180_estorc(true);",$db_opcao);
       ?>
    </td>
    <td>
<?php
db_input('k180_estorc',13,$Ik180_estorc,true,'text',3," onchange='js_pesquisak180_estorc(false);'")
?>
       <?php
db_input('c60_descr',50,$Ic60_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
    <td colspan="2" align="center">
 <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="button" onclick="js_valida('<?=$db_opcao?>')" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  >
 <input name="_self" value="" type="hidden" id="db_opcao" >
 <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
    </td>
  </tr>
  </table>
 <table>
  <tr>
    <td valign="top"  align="center">
    <?php
     $chavepri= array("k180_cadtipoparc"=>@$k180_cadtipoparc,"k180_estorc"=>@$k180_estorc);
     $cliframe_alterar_excluir->chavepri=$chavepri;
     $cliframe_alterar_excluir->sql     = $clcadtipoparcrec->sql_query($k180_cadtipoparc);
     $cliframe_alterar_excluir->campos  ="k180_cadtipoparc,k40_descr,k180_estorc,c60_descr";
     $cliframe_alterar_excluir->legenda="ITENS LANÇADOS";
     $cliframe_alterar_excluir->iframe_height ="200";
     $cliframe_alterar_excluir->iframe_width ="750";
     $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
    </td>
   </tr>
 </table>
</form>
</center>
<script>
function js_cancelar(){
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}
function js_pesquisak180_cadtipoparc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_cadtipoparcrec','db_iframe_cadtipoparc','func_cadtipoparc.php?funcao_js=parent.js_mostracadtipoparc1|k40_codigo|k40_descr','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.k180_cadtipoparc.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_cadtipoparcrec','db_iframe_cadtipoparc','func_cadtipoparc.php?pesquisa_chave='+document.form1.k180_cadtipoparc.value+'&funcao_js=parent.js_mostracadtipoparc','Pesquisa',false);
     }else{
       document.form1.k40_descr.value = '';
     }
  }
}
function js_mostracadtipoparc(chave,erro){
  document.form1.k40_descr.value = chave;
  if(erro==true){
    document.form1.k180_cadtipoparc.focus();
    document.form1.k180_cadtipoparc.value = '';
  }
}
function js_mostracadtipoparc1(chave1,chave2){
  document.form1.k180_cadtipoparc.value = chave1;
  document.form1.k40_descr.value = chave2;
  db_iframe_cadtipoparc.hide();
}

function js_pesquisak180_estorc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_cadtipoparcrec','db_iframe_tabrecparcel','func_conplanoorcamentoparcel.php?funcao_js=parent.js_mostratabrecparcel1|c60_estrut|c60_descr','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.k180_estorc.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_cadtipoparcrec','db_iframe_tabrecparcel','func_conplanoorcamentoparcel.php?pesquisa_chave='+document.form1.k180_estorc.value+'&funcao_js=parent.js_mostratabrecparcel','Pesquisa',false);
     }else{
       document.form1.c60_descr.value = '';
     }
  }
}
function js_mostratabrecparcel(chave,erro){
  document.form1.c60_descr.value = chave;
  if(erro==true){
    document.form1.k180_estorc.focus();
    document.form1.k180_estorc.value = '';
  }
}
function js_mostratabrecparcel1(chave1,chave2){
  document.form1.k180_estorc.value = chave1;
  document.form1.c60_descr.value = chave2;
  db_iframe_tabrecparcel.hide();
}

function js_valida(db_opcao){
 if(db_opcao == 1 || db_opcao == 2){

 }
 document.form1._self.value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>";
 document.form1.submit();
}
</script>
