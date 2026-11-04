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

//MODULO: cemiterio
$cltaxaserv->rotulo->label();
$clitenserv->rotulo->label();

$clrotulo->label("cm11_c_descr");
$clrotulo->label("cm01_i_codigo");
$clrotulo->label("nome");
$clrotulo->label("z01_nome");
$clrotulo->label("cm31_i_sepultamento");
$clrotulo->label("cm01_i_declarante");
$clrotulo->label("cm11_f_valor");

$dia = date('d',db_getsession("DB_datausu"));
$mes = date('m',db_getsession("DB_datausu"));
$ano = date('Y',db_getsession("DB_datausu"));

$dtAtual = date('Y-m-d',db_getsession("DB_datausu"));

if ( !isset($cm10_d_data_dia) && $db_opcao == 1 ) {

  $cm10_d_data_dia    = $dia;
  $cm10_d_data_mes    = $mes;
  $cm10_d_data_ano    = $ano;

  $cm10_d_privenc_dia = $dia;
  $cm10_d_privenc_mes = $mes;
  $cm10_d_privenc_ano = $ano;

  $cm10_d_dtlanc_dia  = $dia;
  $cm10_d_dtlanc_mes  = $mes;
  $cm10_d_dtlanc_ano  = $ano;
}

if( isset($cm31_i_sepultamento) ){
  $iCodigoSepultamento = $cm31_i_sepultamento;

  $sSqlSepultamentos = $clepultamentos->sql_query_dados_sepultamento($iCodigoSepultamento, 'cgm.z01_nome as sepultado, cgm3.z01_nome as declarante');
  $rsSepultamentos   = $clepultamentos->sql_record($sSqlSepultamentos);

  if( $rsSepultamentos ){

    $z01_nome          = db_utils::fieldsMemory($rsSepultamentos, 0)->sepultado;
    $cm01_c_declarante = db_utils::fieldsMemory($rsSepultamentos, 0)->declarante;
  }
}

$semParametros = $_POST == [] && $_GET == [];

if (isset($sepultamento)) {

  $db_opcao = 1;
  $db_botao = true;
  $campos = " 
  cm10_i_codigo,
  cm10_i_numpre,
  cm11_c_descr,
  nome,
  ( select rtstatus
  from fc_statusdebitos(cm10_i_numpre,1)
  ) as status,
  cm10_d_data,
  cm10_d_privenc,
  cm10_f_valor,
  cm10_t_obs";
  
  $sSqlItenServicos = $clitenserv->sql_query("",$campos,""," cm31_i_sepultamento = $sepultamento");
}
?>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<form name="form1" method="post" action="">
 <input type="hidden" name="cm10_i_numpre" value="<?=@$cm10_i_numpre?>">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tcm31_i_sepultamento?>">
       <?php
       db_ancora(@$Lcm31_i_sepultamento,"js_pesquisacm10_i_sepultamento(true);", ($semParametros ? 2 : 3));
       ?>
    </td>
    <td>
     <?php
     db_input('cm31_i_sepultamento',10,$Icm31_i_sepultamento,true,'text',3," onchange='js_pesquisacm10_i_sepultamento(false);'")
     ?>
     <?php
     db_input('z01_nome',50,$Iz01_nome,true,'text',3,'')
     ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm01_i_declarante?>">
       <?php
       db_ancora(@$Lcm01_i_declarante,"js_pesquisacm01_i_declarante(true);",3);
       ?>
    </td>
    <td>
     <?php
     db_input('cm01_i_declarante',10,$Icm01_i_declarante,true,'text',3," onchange='js_pesquisacm01_i_declarante(false);' ")
     ?>
     <?php
     db_input('cm01_c_declarante',50,@$Icm01_c_declarante,true,'text',3,'')
     ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_d_data?>">
       <?=@$Lcm10_d_data?>
    </td>
    <td>
			<?php
				db_inputdata('cm10_d_data',@$cm10_d_data_dia,@$cm10_d_data_mes,@$cm10_d_data_ano,true,'text',3,"")
			?>
    </td>
  </tr>
</table>
<input type="button" value="Buscar itens" onclick="js_buscaSepultamento()">
<fieldset style="width: 600">
 <legend>Valores</legend>
<table>
  <tr>
    <td nowrap title="<?=@$Tcm11_c_descr?>">
       <?=@$Lcm11_c_descr?>
    </td>
    <td>
        <?php
          db_input('cm11_c_descr',50,$Icm11_c_descr,true,'text',3,"")
          ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_i_codigo?>">
       <?=@$Lcm10_i_codigo?>
    </td>
    <td>
          <?php
          db_input('cm10_i_codigo',10,$Icm10_i_codigo,true,'text',3,"")
          ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_i_numpre?>">
       <?=@$Lcm10_i_numpre?>
    </td>
    <td>
     <?php
       db_input('cm10_i_numpre',10,$Icm10_i_numpre,true,'text',3," onkeypress=\"return mascaraValor(event, this);\" onchange='();'");
     ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_f_valor?>">
       <?=@$Lcm10_f_valor?>
    </td>
    <td>
     <?php
       db_input('cm10_f_valor',10,$Icm10_f_valor,true,'text',3," onkeypress=\"return mascaraValor(event, this);\" onchange='js_habilitabotaocalcular();'");
     ?>
    </td>
  </tr>
  <tr style="display: none;" >
    <td nowrap title="<?=@$Tcm10_d_dtlanc?>">
      <?=@$Lcm10_d_dtlanc?>
    </td>
    <td>
      <?php
        db_inputdata('cm10_d_dtlanc',@$cm10_d_dtlanc_dia,@$cm10_d_dtlanc_mes,@$cm10_d_dtlanc_ano,true,'text',3,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_d_privenc?>">
       <?=@$Lcm10_d_privenc?>
    </td>
    <td>
			<?php
			  db_inputdata('cm10_d_privenc',@$cm10_d_privenc_dia,@$cm10_d_privenc_mes,@$cm10_d_privenc_ano,true,'text',3,"")
			?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_t_obs?>">
       <?=@$Lcm10_t_obs?>
    </td>
    <td>
			<?php
			  db_textarea('cm10_t_obs',3,50,$Icm10_t_obs,true,'text',3,"")
			?>
    </td>
  </tr>
 </table>
 <input type="hidden" name="status" id="status">
  <input name="excluir"
         type="submit" id="db_opcao"
         value="Excluir" disabled onclick="return js_validar();">
 </fieldset>

  <div class="container">
    <?php
    if (isset($sepultamento)) {
      $repassa =   ['cm10_i_codigo'];
      db_lovrot($sSqlItenServicos,20,"()","16","js_preencheValores|0|1|2|4|6|7|8",null,"NoMe",$repassa);
    }
    ?>
  </div>
</form>
<script>
function js_validar() {
  if (window.confirm('Excluir o item selecionado?')) {
    return true;
  }

  return false;
}

function js_habilitabotaocalcular() {

  var iValorTaxa = $('cm10_f_valortaxa').value;

  if ( iValorTaxa == "" ) {
    $('calcular').disabled      = true;
    $('cm10_f_valor').disabled  = true;
  } else {
    $('calcular').disabled      = false;
    $('cm10_f_valor').disabled  = false;
    $('cm10_f_valor').value =  js_formatar($('cm10_f_valor').value,'f');
  }
}

function js_pesquisacm10_i_sepultamento(mostra) {

  var sUrl1 = 'func_sepultamentos.php?funcao_js=parent.js_mostrasepultamentos1|cm01_i_codigo|z01_nome|cm01_i_declarante|cm01_c_declarante|cm01_d_falecimento';
  var sUrl2 = 'func_sepultamentos.php?pesquisa_chave='+$('cm31_i_sepultamento').value+'&dtfalecimento=true&funcao_js=parent.js_mostrasepultamentos';

  if ( mostra == true ) {
    js_OpenJanelaIframe('','db_iframe_sepultamentos',sUrl1,'Pesquisa',true);
  } else {

     if($('cm31_i_sepultamento').value != '') {
        js_OpenJanelaIframe('','db_iframe_sepultamentos',sUrl2,'Pesquisa',false);
     } else {
       $('z01_nome').value = '';
     }
  }
}

function js_mostrasepultamentos(chave1,chave2,chave3,chave4,erro) {

  $('z01_nome').value          = chave1;
  $('cm01_i_declarante').value = chave2;
  $('cm01_c_declarante').value = chave3;
  $('cm10_d_data').value       = js_formatar(chave4,'d');

  if ( erro == true ) {
    $('cm10_i_sepultamento').focus();
    $('cm10_i_sepultamento').value = '';
  }

  if ( chave3 != "" ) {
    js_pesquisacm01_i_declarante(false);
  }

  if ( chave4 != "" ) {
    $('cm10_d_dtlanc').value = js_formatar(chave4,'d');
  }

}

function js_mostrasepultamentos1(chave1,chave2,chave3,chave4,chave5) {

  $('cm31_i_sepultamento').value = chave1;
  $('z01_nome').value            = chave2;
  $('cm01_i_declarante').value   = chave3;
  $('cm10_d_data').value         = js_formatar(chave5,'d');

  if ( chave3 != "" ) {
    js_pesquisacm01_i_declarante(false);
  }

  if ( chave5 != "" ) {
    $('cm10_d_dtlanc').value = js_formatar(chave5,'d');
  }

  db_iframe_sepultamentos.hide();
}

function js_pesquisacm01_i_declarante(mostra) {

  var sUrl1 = 'func_cgm.php?funcao_js=parent.js_mostradeclarante1|z01_numcgm|z01_nome';
  var sUrl2 = 'func_cgm.php?pesquisa_chave='+$('cm01_i_declarante').value+'&funcao_js=parent.js_mostradeclarante';

  if ( mostra == true ) {
    js_OpenJanelaIframe('','db_iframe_declarante',sUrl1,'Pesquisa',true);
  } else {

   if ( document.form1.cm01_i_declarante.value != '' ) {
     js_OpenJanelaIframe('','db_iframe_declarante',sUrl2,'Pesquisa',false);
   } else {
     $('cm01_c_declarante').value = '';
   }
  }
}

function js_mostradeclarante(erro,chave1) {

  $('cm01_c_declarante').value = chave1;

  if ( erro == true ) {

    $('cm01_i_declarante').focus();
    $('cm01_i_declarante').value = '';
  }
}

function js_mostradeclarante1(chave1,chave2) {

  $('cm01_i_declarante').value = chave1;
  $('cm01_c_declarante').value = chave2;
  db_iframe_declarante.hide();
}

function js_preenchepesquisa(chave) {

  db_iframe_itenserv.hide();
  <?php
    if($db_opcao!=1){
      echo " location.href = '".basename($_SERVER["PHP_SELF"])."?chavepesquisa='+chave";
    }
  ?>
}

function js_buscaSepultamento() {
  if (cm31_i_sepultamento.value.trim() != '') {
    <?php
      echo " location.href = '".basename($_SERVER["PHP_SELF"])."?sepultamento='+cm31_i_sepultamento.value";
      ?>
  }
}

function js_carregaSepultamento() {
  const buscaSepultamento = <?php echo isset($_GET['sepultamento']) ? $_GET['sepultamento'] : "null";?>

  if(buscaSepultamento){
    cm31_i_sepultamento.value = buscaSepultamento;
    js_pesquisacm10_i_sepultamento(false);
  }
}

function js_preencheValores(codigoDebito, numpre, descricaoDebito, statusDebito, dataVencimento, valorCorrigido, observacoes){
  const dataVenimentoDebito = dataVencimento.split('-')

  status.value = statusDebito;
  cm10_i_codigo.value = codigoDebito;
  cm10_i_numpre.value = numpre;
  cm11_c_descr.value = descricaoDebito;
  cm10_f_valor.value = valorCorrigido;
  cm10_t_obs.value = observacoes;
  cm10_d_privenc.value = `${dataVenimentoDebito[2]}/${dataVenimentoDebito[1]}/${dataVenimentoDebito[0]}`;

  desbloqueiaBotaoExclusao();
}

function desbloqueiaBotaoExclusao() {
  const botao = document.getElementById('db_opcao');
  botao.removeAttribute('disabled');
}

js_carregaSepultamento();
</script>
