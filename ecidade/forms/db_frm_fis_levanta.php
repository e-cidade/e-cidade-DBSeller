<?php
/**
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

$cllevanta->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("q02_inscr");
$clrotulo->label("y60_proces");
$clrotulo->label("p58_requer");
$clrotulo->label("y100_sequencial");
$clrotulo->label("z01_nome");
$clrotulo->label("p58_numero");
$get  = "";
$data = date("d-m-Y",db_getsession("DB_datausu"));
$data = explode('-',$data);
$dia  = $data[0];
$mes  = $data[1];
$ano  = $data[2];

if($db_opcao==1){
  $action = 'fis1_fis_levanta014.php';
}else if($db_opcao==2 || $db_opcao==22){
  $action = 'fis1_fis_levanta015.php';
}else if($db_opcao==3 || $db_opcao==33){
  $action = 'fis1_fis_levanta016.php';
}
?>
<form name="form1" method="post" action="">
<?php
db_input('tipo',10,0,true,'hidden',3);
db_input('valor',10,0,true,'hidden',3);
if($db_opcao == 1){

  if($tipo == "z01_numcgm"){

  	$get   = "&tipo=y101_numcgm&valor=$valor";
    $dados = "<a onClick=\"js_abre('prot3_conscgm002.php?fechar=true&numcgm=$valor');return false;\" href=''>CGM: ".$valor."</a>";

  }elseif($tipo == "q02_inscr"){

  	$get       = "&tipo=y103_inscr&valor=$valor";
    $dados     = "<a onClick=\"js_abre('iss3_consinscr003.php?numeroDaInscricao=$valor');return false;\" href=''>inscrição: ".$valor."</a>";
    $q02_inscr = $valor;

    $oDaoIssBase = db_utils::getDao("issbase");
    $sSqlIssBase = $oDaoIssBase->sql_query( $q02_inscr, 'z01_nome' );
    $rsIssBase   = $oDaoIssBase->sql_record( $sSqlIssBase );
    if( $rsIssBase ){
      $z01_nome = db_utils::fieldsMemory( $rsIssBase, 0, true )->z01_nome;
    }
  }
}
$bloqueia = 2;

/* Verifica se é alteração para bloquear os campos*/
if(isset($procfiscal) && $procfiscal != "" && ($db_opcao == 2 or $db_opcao == 1)){
	$bloqueia = 3 ;
}
?>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>

<fieldset>
  <legend>Levantamento</legend>

    <table border="0">
      <tr>
        <td nowrap title="<?=@$Tq02_inscr?>">
           <?=@$Lq02_inscr?>
        </td>
        <td nowrap >
        <?php  db_input('q02_inscr',6,0,true,'text',3);?>
        <?php  db_input('z01_nome',40,0,true,'text',3);?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty60_codlev?>">
           <?=@$Ly60_codlev?>
        </td>
        <td>
          <?php
          db_input('y60_codlev',10,$Iy60_codlev,true,'text',3);
          if($db_opcao == 1){
            echo "<span style=\"float:right; font-weight:bold;\">$dados</strong>";
          }
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty60_data?>">
           <?=@$Ly60_data?>
        </td>
        <td>
    <?php
    if(empty($y60_data_dia)){

      $y60_data_dia=$dia;
      $y60_data_mes=$mes;
      $y60_data_ano=$ano;
    }
    db_inputdata('y60_data',@$y60_data_dia,@$y60_data_mes,@$y60_data_ano,true,'text',$db_opcao,"")
    ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty60_contato?>">
           <?=@$Ly60_contato?>
        </td>
        <td>
          <?php
          db_input('y60_contato',40,$Iy60_contato,true,'text',$db_opcao,"")
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty60_dtini?>">
           <?=@$Ly60_dtini?>
        </td>
        <td>
    <?php
    db_inputdata('y60_dtini',@$y60_dtini_dia,@$y60_dtini_mes,@$y60_dtini_ano,true,'text',$db_opcao,"")
    ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty60_dtfim?>">
           <?=@$Ly60_dtfim?>
        </td>
        <td>
          <?php
          db_inputdata('y60_dtfim',@$y60_dtfim_dia,@$y60_dtfim_mes,@$y60_dtfim_ano,true,'text',$db_opcao,"")
          ?>
        </td>
      </tr>
<!--         <tr>
          <td nowrap title="<?=@$Ty60_proces?>">
        	 <?php
           db_ancora("<stroc/strong>","js_pesquisay60_proces(true);",$db_opcao);
           ?>
          </td>
          <td>
          <?php
           db_input('y60_proces',15,@$Iy60_proces,true,'text',$bloqueia," onchange='js_pesquisay60_proces(false);'",'','','','14');
           db_input('p58_requer',40,$Ip58_requer,true,'text',$bloqueia,'');
           ?>
          <td>
        </tr> -->
      <tr>
        <td nowrap title="<?=@$Ty60_proces?>">
          <?php
          	 if($bloqueia == 3 )
	             echo "<b>Processo:</b>";
             else
               db_ancora("<b>Processo:</b>",' js_mostracodproc(true); ',4);
          ?>
          </td>
          <td>
            <?php
            db_input('p58_numero',14,$Ip58_numero,true,'text',$bloqueia,"","","",14);
            db_input('y60_proces',10,$Iy60_proces,true,'hidden',4,'');

            db_input('p58_requer',40,$Ip58_requer,true,'text',3,'');
            ?>

        </td>
      </tr>

      <tr>
        <td nowrap title="<?=@$Ty60_obs?>">
           <?=@$Ly60_obs?>
        </td>
        <td>
        <?php
        db_textarea('y60_obs',0,30,$Iy60_obs,true,'text',$db_opcao,"")
        ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty60_espontaneo?>">
       <strong>Levantamento Espontâneo:</strong>
        </td>
        <td>
        <?php
    	  $tipo_ordem = array("f"=>"Não","t"=>"Sim");
    	  db_select("y60_espontaneo",$tipo_ordem,true,2); ?>
    	  </td>
      </tr>

    <tr>
        <td nowrap title="<?=@$Ty100_sequencial?>">
           <?php
           db_ancora(@$Ly100_sequencial,"js_pesquisaprocfiscal(true);",$bloqueia);
           ?>
        </td>
        <td>
          <?php
            db_input('procfiscal',10,$Iy100_sequencial,true,'text',$bloqueia," onchange='js_pesquisaprocfiscal(false);'");
            db_input('nome',40,$Iz01_nome,true,'text',3,'');
          ?>
        </td>
      </tr>
   </table>
  </fieldset>

  <br/>
  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick="return js_validaFormulario();" >
<?php  if($db_opcao != 1){?>
 <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
<?php }?>
</form>
<script type="text/javascript">

var sCaminhoMensagens = 'tributario.fiscal.db_frmlevanta.';


function js_validaFormulario (){

  if( empty($F('y60_data')) ){

    alert( _M ( sCaminhoMensagens + 'data_levantamento_obrigatorio') );
    return false;
  }

  if( empty($F('y60_dtini')) ){

    alert( _M ( sCaminhoMensagens + 'periodo_inicial_levantamento_obrigatorio') );
    return false;
  }

   if( empty($F('y60_dtfim')) ){

    alert( _M ( sCaminhoMensagens + 'periodo_final_levantamento_obrigatorio') );
    return false;
  }

  var dDataInicioLevantamento = new Date($F('y60_dtini_ano'), $F('y60_dtini_mes')-1, $F('y60_dtini_dia'));
  var dDataFimLevantamento    = new Date($F('y60_dtfim_ano'), $F('y60_dtfim_mes')-1, $F('y60_dtfim_dia'));
  if(dDataFimLevantamento < dDataInicioLevantamento){

    alert( _M ( sCaminhoMensagens + 'final_levantamento_invalido') );
    return false;
  }

  var dHoje = new Date();
  if(dDataInicioLevantamento  > dHoje){
	alert("A data de Início do Levantamento não pode ser superior ao dia de hoje!");
	return false;
  }

 if(dDataFimLevantamento > dHoje){
	alert("A data de Fim do Levantamento não pode ser superior ao dia de hoje!");
	return false;
 }

  var oRegex = /Chave\(*[0-9]*\)/;
  if( empty($F('nome')) || oRegex.test($F('nome')) ){
    $('procfiscal').value = null;
  }

  return true;
}

function js_pesquisay60_proces(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levanta','db_iframe_processo','func_fis_processoadministrativofiscal.php?funcao_js=parent.js_mostraprocesso1|p58_numero|p58_requer','Pesquisa',true,0);
  }else{
     if(document.form1.y60_proces.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levanta','db_iframe_processo','func_fis_processoadministrativofiscal.php?pesquisa_chave='+document.form1.y60_proces.value+'&funcao_js=parent.js_mostraprocesso&chave_p58_numero=aa','Pesquisa',false);
     }else{
       document.form1.y60_proces.value = '';
     }
  }
}
function js_mostraprocesso(chave, sDescricao, lErro){

  document.form1.p58_requer.value = sDescricao;

  if( lErro == true ){

    document.form1.y60_proces.focus();
    document.form1.y60_proces.value = '';
  }
}

function js_mostraprocesso1(chave1,chave2){

  document.form1.y60_proces.value = chave1;
  document.form1.p58_requer.value = chave2;
  db_iframe_processo.hide();
}

function js_pesquisaprocfiscal(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?funcao_js=parent.js_mostraprocfiscal1|y100_sequencial|z01_nome|db_depart_protocolo|db_descr_depart|db_depart_atual|p58_codproc|p58_numero<?=$get?>&'+document.form1.tipo.value+'='+document.form1.valor.value,'Pesquisa',true);
  }else{
     if(document.form1.procfiscal.value != ''){
        js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?pesquisa_chave='+document.form1.procfiscal.value+'&funcao_js=parent.js_mostraprocfiscal','Pesquisa',false,'0','1','775','390');
     }else{
		 	 document.form1.nome.value = '';
		 }
  }
}
<?php
if(isset($procfiscal) && $procfiscal != ''){
  echo 'js_pesquisaprocfiscal(false);';
}
?>
function js_mostraprocfiscal(chave,erro,dep_prot,depart,dep_atual){

 if (dep_prot == dep_atual) {
  	document.form1.nome.value = chave;
    if(erro==true){
      document.form1.procfiscal.focus();
      document.form1.procfiscal.value = '';
    }
  }
  else {
    alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
		document.form1.procfiscal.focus();
    document.form1.procfiscal.value = '';
		document.form1.nome.value = '';
		return false;
  }
}
function js_mostraprocfiscal1(chave1,chave2,dep_prot,depart,dep_atual,processo,numeroproc){
	//alert("Processo"+processo+"-"+numeroproc);
  if (dep_prot == dep_atual) {
  	document.form1.procfiscal.value = chave1;
  	document.form1.nome.value = chave2;
  	db_iframe_procfiscal.hide();
  }
  else {
    alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
		return false;
  }
 // if(document.form1.p58_numero.value == ""){
    if (processo != "") {
    document.form1.y60_proces.value = processo;
    document.form1.p58_numero.value = numeroproc;
    js_mostracodproc(false);
  }
}

function js_pesquisa(){
  sUrl = 'func_fis_levanta_altera.php';
  js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levanta','db_iframe_levanta',sUrl+'?funcao_js=parent.js_preenchepesquisa|y60_codlev','Pesquisa',true,0);
}
function js_abre(pagina){
  js_OpenJanelaIframe('','db_iframe_consulta',pagina,'Pesquisa',true,0);
}
function js_preenchepesquisa(chave){
  db_iframe_levanta.hide();
  <?php
  $op='';
  if(isset($alterando)){
    $op="alterando=true&";
  }else if(isset($excluindo)){
    $op="excluindo=true&";
  }
  echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?".$op."chavepesquisa='+chave";
  ?>
}
<?php
  if(empty($chavepesquisa) && (isset($alterando) || isset($excluindo)) ){
    echo "js_pesquisa();";
  }
?>

function js_mostracodproc(mostra){

  if (document.form1.procfiscal.value != "") {
    url= '&ProcFiscal='+document.form1.procfiscal.value
  }else{
    url = '';
  }

 if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativofiscal.php?funcao_js=parent.js_mostraproc1|p58_codproc|p58_numero|z01_nome'+url,'Pesquisa',true,'15');
  }else{
        js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativofiscal.php?pesquisa_chave='+document.form1.p58_numero.value+'&funcao_js=parent.js_mostraproc&chave_p58_numero=1'+url,'Pesquisa',false);
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
  document.form1.p58_requer.value = z;
  document.form1.y60_proces.value = chave1;
  document.form1.p58_numero.value = n;
  db_iframe_proc.hide();
}

</script>
