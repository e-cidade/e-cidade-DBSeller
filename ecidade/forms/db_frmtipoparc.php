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

//MODULO: caixa
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$cltipoparc->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("i01_descr");
$clrotulo->label("k40_descr");
if(isset($db_opcaoal)){
   $db_opcao=33;
   $db_botao=false;
}else if(isset($opcao) && $opcao=="alterar" || isset($acao) && $acao=="alterar"){
    $db_botao=true;
    $db_opcao = 2;
}else if(isset($opcao) && $opcao=="excluir" || isset($acao) && $acao=="excluir"){
    $db_opcao = 3;
    $db_botao=true;
}else{

    $db_opcao = 1;
    $db_botao=true;
    if(isset($novo) || isset($acao) && $acao!=""){
     $descr          = "";
     $dtini_ano      = "";
     $dtini_dia      = "";
     $dtini_mes      = "";
     $dtfim_ano      = "";
     $dtfim_dia      = "";
     $dtfim_mes      = "";
     $maxparc        = "";
     $minparc        = "";
     $vlrmin         = "";
     $dtvlr_ano      = "";
     $dtvlr_dia      = "";
     $dtvlr_mes      = "";
     $inflat         = "";
     $descvlr        = "";
     $descmul        = "";
     $descjur        = "";
     $i01_descr      = "";
     $tipoparc       = "";
     $k42_minentrada = "";
   }
}
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
<tr>
    <td nowrap title="<?=@$Tcadtipoparc?>">
       <?php
       db_ancora(@$Lcadtipoparc,"js_pesquisacadtipoparc(true);",3);
       ?>
    </td>
    <td>
<?php
db_input('cadtipoparc',10,$Icadtipoparc,true,'text',3," onchange='js_pesquisacadtipoparc(false);'")
?>
       <?php
       if (isset($cadtipoparc)&&$cadtipoparc!=""){
        $Result_Descr=$clcadtipoparc->sql_record($clcadtipoparc->sql_query_file($cadtipoparc,"k40_descr"));
       	if ($clcadtipoparc->numrows>0){
       		db_fieldsmemory($Result_Descr,0);
       	}
       }
db_input('k40_descr',40,$Ik40_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ttipoparc?>">
       <?=@$Ltipoparc?>
    </td>
    <td>
<?php
db_input('tipoparc',10,$Itipoparc,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdescr?>">
       <?=@$Ldescr?>
    </td>
    <td>
<?php
db_input('descr',40,$Idescr,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdtini?>">
       <?=@$Ldtini?>
    </td>
    <td>
<?php
db_inputdata('dtini',@$dtini_dia,@$dtini_mes,@$dtini_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdtfim?>">
       <?=@$Ldtfim?>
    </td>
    <td>
<?php
db_inputdata('dtfim',@$dtfim_dia,@$dtfim_mes,@$dtfim_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tmaxparc?>">
       <?=@$Lmaxparc?>
    </td>
    <td>
<?php
db_input('maxparc',10,$Imaxparc,true,'text',$db_opcao,"onchange='js_validaparcela()'")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tminparc?>">
       <?=@$Lminparc?>
    </td>
    <td>
<?php
db_input('minparc',10,$Iminparc,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tvlrmin?>">
       <?=@$Lvlrmin?>
    </td>
    <td>
<?php
db_input('vlrmin',10,$Ivlrmin,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tvlrmax?>">
       <?=@$Lvlrmax?>
    </td>
    <td>
<?php
db_input('vlrmax',10,$Ivlrmax,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdtvlr?>">
       <?=@$Ldtvlr?>
    </td>
    <td>
<?php
db_inputdata('dtvlr',@$dtvlr_dia,@$dtvlr_mes,@$dtvlr_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
    <tr>
    <td nowrap title="<?=@$Tvlrmindeb?>">
       <?=@$Lvlrmindeb?>
    </td>
    <td>
<?php
db_input('vlrmindeb',10,$Ivlrmindeb,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tvlrmaxdeb?>">
       <?=@$Lvlrmaxdeb?>
    </td>
    <td>
<?php
db_input('vlrmaxdeb',10,$Ivlrmaxdeb,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tinflat?>">
       <?php
       db_ancora(@$Linflat,"js_pesquisainflat(true);",$db_opcao);
       ?>
    </td>
    <td>
<?php
db_input('inflat',10,$Iinflat,true,'text',$db_opcao," onchange='js_pesquisainflat(false);'")
?>
       <?php
db_input('i01_descr',40,$Ii01_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdescvlr?>">
       <?=@$Ldescvlr?>
    </td>
    <td>
<?php
db_input('descvlr',10,$Idescvlr,true,'text',$db_opcao,"onChange='js_validaPerc(this);'")
?>
    </td>
  </tr>

  <tr>
    <td nowrap title="<?=@$Ttipovlr?>">
       <?=@$Ltipovlr?>
    </td>
    <td>
		<?php
		$x = getValoresPadroesCampo('tipovlr');
		$x = array_reverse($x,true);
		db_select('tipovlr',$x,true,$db_opcao,"");
		?>
    </td>
  </tr>

  <tr>
    <td nowrap title="<?=@$Tdescmul?>">
       <?=@$Ldescmul?>
    </td>
    <td>
<?php
db_input('descmul',10,$Idescmul,true,'text',$db_opcao,"onChange='js_validaPerc(this);'")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tdescjur?>">
       <?=@$Ldescjur?>
    </td>
    <td>
<?php
db_input('descjur',10,$Idescjur,true,'text',$db_opcao,"onChange='js_validaPerc(this);'")
?>
    </td>
  </tr>
  <tr>
  <tr>
    <td nowrap title="<?=@$Tk42_minentrada?>">
       <?=@$Lk42_minentrada?>
    </td>
    <td>
<?php
db_input('k42_minentrada',10,$Ik42_minentrada,true,'text',$db_opcao,"onChange='js_validaPerc(this);'")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ttipopessoa?>">
       <?=@$Ltipopessoa?>
    </td>
    <td>
    <?php
    $x = array(0 => 'Todos', 1 => 'Física', 2 => 'Jurídica');
    db_select('tipopessoa',$x,true,$db_opcao,"");
    ?>
    </td>
  </tr>
    <td colspan="2" align="center">
 <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="button" onclick="js_valida('<?=$db_opcao?>')" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  >
 <input name="acao" value="" type="hidden" id="db_opcao" >
 <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
    </td>
  </tr>
  </table>
 <table>
  <tr>
    <td valign="top"  align="center">
    <?php
   $campos = 'tipoparc,descr,dtini,dtfim,maxparc,minparc,vlrmin,vlrmax,dtvlr,vlrmindeb,vlrmaxdeb,inflat,descvlr,descmul,descjur,k42_minentrada,cadtipoparc,k40_descr';
   $sqlCaseTipoPessoa = ",case when tipopessoa = 0 then 'Todos' when tipopessoa = 1 then 'Física' when tipopessoa = 2 then 'Jurídica' ELSE '' END as tipopessoa";
	 $chavepri= array("cadtipoparc"=>@$cadtipoparc,"tipoparc"=>@$tipoparc);
	 $cliframe_alterar_excluir->chavepri=$chavepri;
	 $cliframe_alterar_excluir->sql     = $cltipoparc->sql_query(null,$campos.$sqlCaseTipoPessoa,"tipoparc","cadtipoparc=$cadtipoparc");
	 $cliframe_alterar_excluir->campos  = $campos.',tipopessoa';
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

function js_validaPerc(oObj){

  nPerc = new Number(oObj.value);
  if (nPerc > 100 || nPerc < 0){
    alert("Percentual deve ser um valor entre 0 e 100");
    oObj.value = '';
  }

}





function js_cancelar(){
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}
function js_pesquisainflat(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tipoparc','db_iframe_inflan','func_inflan.php?funcao_js=parent.js_mostrainflan1|i01_codigo|i01_descr','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.inflat.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tipoparc','db_iframe_inflan','func_inflan.php?pesquisa_chave='+document.form1.inflat.value+'&funcao_js=parent.js_mostrainflan','Pesquisa',false);
     }else{
       document.form1.i01_descr.value = '';
     }
  }
}
function js_mostrainflan(chave,erro){
  document.form1.i01_descr.value = chave;
  if(erro==true){
    document.form1.inflat.focus();
    document.form1.inflat.value = '';
  }
}
function js_mostrainflan1(chave1,chave2){
  document.form1.inflat.value = chave1;
  document.form1.i01_descr.value = chave2;
  db_iframe_inflan.hide();
}
function js_pesquisacadtipoparc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tipoparc','db_iframe_cadtipoparc','func_cadtipoparc.php?funcao_js=parent.js_mostracadtipoparc1|k40_codigo|k40_descr','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.cadtipoparc.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tipoparc','db_iframe_cadtipoparc','func_cadtipoparc.php?pesquisa_chave='+document.form1.cadtipoparc.value+'&funcao_js=parent.js_mostracadtipoparc','Pesquisa',false);
     }else{
       document.form1.k40_descr.value = '';
     }
  }
}
function js_mostracadtipoparc(chave,erro){
  document.form1.k40_descr.value = chave;
  if(erro==true){
    document.form1.cadtipoparc.focus();
    document.form1.cadtipoparc.value = '';
  }
}
function js_mostracadtipoparc1(chave1,chave2){
  document.form1.cadtipoparc.value = chave1;
  document.form1.k40_descr.value = chave2;
  db_iframe_cadtipoparc.hide();
}

function js_valida(db_opcao){
 if(db_opcao == 1 || db_opcao == 2){
  obj = document.form1;
  data1 = obj.dtini_ano.value+obj.dtini_mes.value+obj.dtini_dia.value;
  data2 = obj.dtfim_ano.value+obj.dtfim_mes.value+obj.dtfim_dia.value;
  if(data1 > data2){
   alert('Data Inicial maior que a Data Final');
   return false;
  }
 }
 if(db_opcao == 1){
  acao = "incluir";
 }else if(db_opcao == 2){
  acao = "alterar";
 }else if(db_opcao == 3){
  acao = "excluir";
 }
 document.form1.acao.value = acao;
 document.form1.submit();

}

function js_validaparcela(){
  var maxparc = +document.form1.maxparc.value;
  var vencimento = CurrentWindow.corpo.iframe_cadtipoparc.document.form1.k40_controlavencimento.value;

  if(vencimento == 't'){
    if(maxparc > 12){
      alert('M\u00e1ximo de parcelas n\u00e3o pode ser maior que 12!');
      document.getElementById("db_opcao").disabled = true;
    }else{
      document.getElementById("db_opcao").disabled = false;
    }
  }
}

</script>
