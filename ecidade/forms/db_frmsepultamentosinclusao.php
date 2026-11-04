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
use App\Domain\Tributario\Cemiterio\Repositories\Cemiterio\Parametros\ParametrosCemiterioRepository;

$clsepultamentos->rotulo->label();
$cltaxaserv->rotulo->label();
$clitenserv->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("cm17_i_funeraria");
$clrotulo->label("cm18_i_hospital");
$clrotulo->label("cm14_i_codigo");
$clrotulo->label("nome");
$clrotulo->label("cm04_c_descr");
$clrotulo->label("cm07_d_vencimento");
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

$oDaoTaxaSepultamentos = new cl_txsepultamentos();
$oDaoSepultamentos     = new cl_sepultamentos();

$taxaObrigatoria = false;

$anoSessao = (db_getsession('DB_anousu'));
$taxaObrigatoria = false;

$parametrosCemiterioRepository =new ParametrosCemiterioRepository();
$parametrosCadastrados = $parametrosCemiterioRepository->buscaParametros($anoSessao);

if (count($parametrosCadastrados) > 0) {
  $taxaObrigatoria = $parametrosCadastrados[0]['cem36_obrigatoriedadetaxasepultamento'];
}

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

  $sSqlSepultamentos = $oDaoSepultamentos->sql_query_dados_sepultamento($iCodigoSepultamento, 'cgm.z01_nome as sepultado, cgm3.z01_nome as declarante');
  $rsSepultamentos   = $oDaoSepultamentos->sql_record($sSqlSepultamentos);

  if( $rsSepultamentos ){

    $z01_nome          = db_utils::fieldsMemory($rsSepultamentos, 0)->sepultado;
    $cm01_c_declarante = db_utils::fieldsMemory($rsSepultamentos, 0)->declarante;
  }
}

 ?>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
 <form name="form1" method="post" action="">
 <fieldset>
   <legend>Certidão de Óbito</legend>
 <table border="0">
   <tr>
     <td nowrap title="<?php echo $Tcm01_i_medico; ?>">
       <?php
         db_ancora($Lcm01_i_medico, "js_pesquisacm01_i_medico(true);", $db_opcao);
       ?>
     </td>
     <td>
       <?php

         if (!isset($cm32_nome)) {
           $cm32_nome = '';
         }
         $GLOBALS['Gcm32_nome'] = true;
         db_input('cm01_i_medico', 10, $Icm01_i_medico, true, 'text', $db_opcao, " onchange='js_pesquisacm01_i_medico(false);'");
         db_input('cm32_nome', 40, $cm32_nome, true, 'text', $db_opcao, '');
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Tcm01_i_causa; ?>">
       <?php
         db_ancora($Lcm01_i_causa, "js_pesquisacm01_i_causa(true);", $db_opcao);
       ?>
     </td>
     <td>
       <?php
         db_input('cm01_i_causa', 10, $Icm01_i_causa, true, 'text', $db_opcao, " onchange='js_pesquisacm01_i_causa(false);'");
         db_input('cm04_c_descr', 40, $Icm04_c_descr, true, 'text', 3, '');
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Tcm01_c_local; ?>">
        <?php echo $Lcm01_c_local; ?>
     </td>
     <td>
       <?php
         db_input('cm01_c_local', 54, $Icm01_c_local, true, 'text', $db_opcao, "");
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Tcm01_c_cartorio; ?>">
        <?php echo $Lcm01_c_cartorio; ?>
     </td>
     <td>
       <?php
         db_input('cm01_c_cartorio', 54, $Icm01_c_cartorio, true, 'text', $db_opcao, "");
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Tcm01_i_hospital; ?>">
       <?php
         db_ancora($Lcm01_i_hospital, "js_pesquisacm01_i_hospital(true);", $db_opcao);
       ?>
     </td>
     <td>
       <?php

         $GLOBALS['Gnome_hospital'] = true;
         db_input('cm01_i_hospital', 10, $Icm01_i_hospital, true, 'text', $db_opcao, " onchange='js_pesquisacm01_i_hospital(false);'");
         db_input('nome_hospital', 40, 0, true, 'text', $db_opcao, '');
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Tcm01_i_funeraria; ?>">
       <?php
         db_ancora($Lcm01_i_funeraria, "js_pesquisacm01_i_funeraria(true);", $db_opcao);
       ?>
     </td>
     <td>
       <?php

         $GLOBALS['Gnome_funeraria'] = true;
         db_input('cm01_i_funeraria', 10, $Icm01_i_funeraria, true, 'text', $db_opcao, " onchange='js_pesquisacm01_i_funeraria(false);'");
         db_input('nome_funeraria', 40, 0, true, 'text', $db_opcao, '');
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Lcm01_c_livro; ?>">
        <?php echo $Lcm01_c_livro; ?>
     </td>
     <td>
       <?php
         db_input('cm01_c_livro', 10, $Icm01_c_livro, true, 'text', $db_opcao, "");
       ?>
     </td>
   </tr>
   <tr>
     <td nowrap title="<?php echo $Lcm01_i_folha; ?>">
        <?php echo $Lcm01_i_folha; ?>
     </td>
     <td>
       <?php
         db_input('cm01_i_folha', 10, $Icm01_i_folha, true, 'text', $db_opcao, "");
       ?>
     </td>
   </tr>
     <td nowrap title="<?php echo $Lcm01_i_registro; ?>">
        <?php echo $Lcm01_i_registro; ?>
     </td>
     <td>
       <?php
         db_input('cm01_i_registro', 10, $Icm01_i_registro, true, 'text', $db_opcao, "");
       ?>
     </td>
   </tr>
  </table>
  </fieldset>
  <fieldset>
  <legend>Declarante</legend>
  <table>
   <tr>
     <td nowrap title="Renovante">
       <?php
         db_ancora("<strong>Declarante:</strong>", "js_pesquisacm01_i_declarante(true);", $db_opcao);
       ?>
     </td>
     <td>
       <?php
         if (!isset($nome_declarante)) {
           $nome_declarante = '';
         }
         db_input('cm01_i_declarante', 10, $Icm01_i_declarante, true, 'text', $db_opcao, " onchange='js_pesquisacm01_i_declarante(false);'");
         db_input('nome_declarante', 38, $nome_declarante, true, 'text', 3, '');
       ?>
     </td>
   </tr>
   <?php
     if($db_opcao == 1) {
   ?>
     <tr>
       <td nowrap title="<?php echo $Tcm07_d_vencimento; ?>">
          <strong>Vencimento:</strong>
       </td>
       <td>
         <?php
           if (!isset($cm07_d_vencimento_dia)) {
             $cm07_d_vencimento_dia = '';
             $cm07_d_vencimento_mes = '';
             $cm07_d_vencimento_ano = '';
           }

           if($cm07_d_vencimento_dia == "") {
             $cm07_d_vencimento_dia = substr($cm01_d_falecimento, 8, 2);
           }

           if($cm07_d_vencimento_mes == "") {
             $cm07_d_vencimento_mes = substr($cm01_d_falecimento, 5, 2);
           }

           if($cm07_d_vencimento_ano == "") {
             $cm07_d_vencimento_ano = substr($cm01_d_falecimento, 0, 4) + 5;
           }

           db_inputdata('cm07_d_vencimento', $cm07_d_vencimento_dia, $cm07_d_vencimento_mes, $cm07_d_vencimento_ano, true, 'text', $db_opcao, "");
         ?>
       </td>
     </tr>
   <?php
     }
   ?>
   </table>
  </fieldset>

 <fieldset>
  <legend>Valores</legend>
<table>
  <tr>
    <td nowrap title="<?=@$Tcm10_i_taxaserv?>">
       <?=@$Lcm10_i_taxaserv?>
    </td>
    <td>
       <?php
         $sWhere     = " (cm11_d_datalimite >= cast('{$dtAtual}' as date) or cm11_d_datalimite is null) ";
         $rsTaxaServ = $cltaxaserv->sql_record($cltaxaserv->sql_query(null,"*",null,$sWhere));

         $tx      = array();
         $tx[0]   = "Selecione";

         for ( $q = 0; $q < $cltaxaserv->numrows; $q++ ) {

           db_fieldsmemory($rsTaxaServ,$q);
           $tx[$cm11_i_codigo] = $cm11_c_descr;
         }

         db_select("cm10_i_taxaserv",$tx,true,$db_opcao,"onchange='js_pesquisaTaxaServicos();'");
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_f_valortaxa?>">
       <?=@$Lcm10_f_valortaxa?>
    </td>
    <td>
     <?php
       db_input('cm10_f_valortaxa',10,$Icm10_f_valortaxa,true,'text',3,"")
     ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_f_valor?>">
       <?=@$Lcm10_f_valor?>
    </td>
    <td>
     <?php
       db_input('cm10_f_valor',10,$Icm10_f_valor,true,'text',$db_opcao," onkeypress=\"return mascaraValor(event, this);\" onchange='js_habilitabotaocalcular();'");
     ?>
      <input name="calcular" type="button" id="calcular" value="Calcular" onclick="js_calcularValores();">
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_d_dtlanc?>">
      <?=@$Lcm10_d_dtlanc?>
    </td>
    <td>
      <?php
        db_inputdata('cm10_d_dtlanc',@$cm10_d_dtlanc_dia,@$cm10_d_dtlanc_mes,@$cm10_d_dtlanc_ano,true,'text',$db_opcao,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_d_privenc?>">
       <?=@$Lcm10_d_privenc?>
    </td>
    <td>
			<?php
			  db_inputdata('cm10_d_privenc',@$cm10_d_privenc_dia,@$cm10_d_privenc_mes,@$cm10_d_privenc_ano,true,'text',$db_opcao,"")
			?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm10_t_obs?>">
       <?=@$Lcm10_t_obs?>
    </td>
    <td>
			<?php
			  db_textarea('cm10_t_obs',3,50,$Icm10_t_obs,true,'text',$db_opcao,"")
			?>
    </td>
  </tr>
 </table>
 </fieldset>

 <center>
<?php if(!isset($sepultamento)) { ?>
<input name="z01_cgccpf" type="hidden" id="z01_cgccpf" value="" />
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick='return validar()' >
<?php } ?>
</center>

 </form>
 <script type="text/javascript">
 js_removeObj('actionLoading');

 var oCodigoMedico = $('cm01_i_medico');
 var oNomeMedico   = $('cm32_nome');

 var oCodigoHospital = $('cm01_i_hospital');
 var oNomeHospital   = $('nome_hospital');

 var oCodigoFuneraria = $('cm01_i_funeraria');
 var oNomeFuneraria   = $('nome_funeraria');

 var oCodigoDeclarante = $('cm01_i_declarante');
 var oNomeDeclarante   = $('nome_declarante');

 var oCodigoCausa    = $('cm01_i_causa');
 var oCodigoLocal    = $('cm01_c_local');
 var oCodigoCartorio = $('cm01_c_cartorio');

 js_controla_campo(oNomeMedico, oCodigoMedico);
 js_controla_campo(oNomeHospital, oCodigoHospital);
 js_controla_campo(oNomeFuneraria, oCodigoFuneraria);

 function validar() {

   try{
    const nValorTaxa      = new Number(js_strToFloat($F('cm10_f_valortaxa')));
    const nValorCorrigido = new Number(js_strToFloat($F('cm10_f_valor')));
    const taxaJaCalculada = js_validaTaxaSepultamentoJaCalculada();
    const taxaNaoIncluida = ( $F('cm10_f_valortaxa') == "" || nValorTaxa.valueOf() == 0 )
                          || ($F('cm10_f_valor') == "" || nValorCorrigido.valueOf() == 0);

    if(oCodigoCausa.value == '') {
      throw new Error("Campo Causa é de preenchimento obrigatório.");
    }

    if(oCodigoLocal.value == '') {
      throw new Error("Campo Local é de preenchimento obrigatório.");
    }

    if(oCodigoCartorio.value == '') {
      throw new Error("Campo Cartório é de preenchimento obrigatório.");
    }

    if(oNomeDeclarante.value == '') {
      throw new Error("Campo Declarante é de preenchimento obrigatório.");
    }

    if(taxaJaCalculada){
      throw new Error('Taxa de Sepultamento já calculada!');
    }

    if(!validaCpfPessoaFisica(document.getElementById('z01_cgccpf').value)){
      throw new Error('Declarante deve ser pessoa física.');
    }

    <?php
      if ($taxaObrigatoria) {
    ?>

    if ( $F('cm10_f_valortaxa') == "" || nValorTaxa.valueOf() == 0 ) {
      throw new Error('Campo valor taxa não informado!');
    }

    if ($F('cm10_f_valor') == "" || nValorCorrigido.valueOf() == 0) {
      throw new Error('Campo valor corrigido não informado!');
    }

    <?php
      } else {
    ?>

    if (taxaNaoIncluida && !window.confirm('Não foi incluída nenhuma taxa no sepultamento, gostaria de prosseguir?')){
      return false;
    }

    <?php
      }
    ?>

   } catch(erro) {

     alert(erro.message);
     return false;
   }

   js_divCarregando("Aguarde, processando...", "actionLoading");

   return true;
 }

 function validaCpfPessoaFisica(cpf){
  if(cpf && cpf.toString().trim().length !== 11){
     return false;
   }

   return true;
 }

 function js_controla_campo(oCampo, oCampoRefencia) {

   if (oCampoRefencia.value != '') {

     oCampo.readOnly = true;
     oCampo.classList.add("readOnly");
     return true;
   }

   if(oCampoRefencia.readOnly == false) {

     oCampo.readOnly = false;
     oCampo.classList.remove("readOnly");
   }

 }

 function js_pesquisacm01_i_funeraria(mostra) {

   oNomeFuneraria.value = "";

   if(mostra == true) {
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_funerarias', 'func_funerarias.php?funcao_js=parent.js_mostrafunerarias1|cm17_i_funeraria|z01_nome', 'Pesquisa', true);
   } else {
     if(document.form1.cm01_i_funeraria.value != '') {
       js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_funerarias', 'func_funerarias.php?pesquisa_chave='+document.form1.cm01_i_funeraria.value+'&funcao_js=parent.js_mostrafunerarias', 'Pesquisa', false);
     } else {
       js_controla_campo(oNomeFuneraria, oCodigoFuneraria);
     }
   }
 }

 function js_mostrafunerarias(chave, erro) {

   document.form1.nome_funeraria.value = chave;

   if(erro == true) {

     document.form1.cm01_i_funeraria.focus();
     document.form1.cm01_i_funeraria.value = '';
   }

   js_controla_campo(oNomeFuneraria, oCodigoFuneraria);
 }

 function js_mostrafunerarias1(chave1, chave2) {

   document.form1.cm01_i_funeraria.value = chave1;
   document.form1.nome_funeraria.value   = chave2;

   js_controla_campo(oNomeFuneraria, oCodigoFuneraria);
   db_iframe_funerarias.hide();
 }

 function js_pesquisacm01_i_hospital(mostra) {

   oNomeHospital.value = "";

   if(mostra == true) {
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_hospitais', 'func_hospitais.php?funcao_js=parent.js_mostrahospitais1|cm18_i_hospital|z01_nome', 'Pesquisa', true);
   } else {
     if(document.form1.cm01_i_hospital.value != '') {
       js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_hospitais', 'func_hospitais.php?pesquisa_chave='+document.form1.cm01_i_hospital.value+'&funcao_js=parent.js_mostrahospitais', 'Pesquisa', false);
     } else {
       js_controla_campo(oNomeHospital, oCodigoHospital);
     }
   }
 }

 function js_mostrahospitais(chave, erro) {

   document.form1.nome_hospital.value = chave;

   if(erro == true) {

     document.form1.cm01_i_hospital.focus();
     document.form1.cm01_i_hospital.value = '';
   }

   js_controla_campo(oNomeHospital, oCodigoHospital);
 }

 function js_mostrahospitais1(chave1, chave2) {

   document.form1.cm01_i_hospital.value = chave1;
   document.form1.nome_hospital.value   = chave2;

   js_controla_campo(oNomeHospital, oCodigoHospital);
   db_iframe_hospitais.hide();
 }

 function js_pesquisacm01_i_medico(mostra) {

   oNomeMedico.value = "";

   if(mostra == true) {
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_legista', 'func_legista.php?funcao_js=parent.js_mostramedicos1|cm32_i_codigo|z01_nome', 'Pesquisa', true);
   } else {
     if(document.form1.cm01_i_medico.value != '') {
       js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_legista', 'func_legista.php?pesquisa_chave='+document.form1.cm01_i_medico.value+'&funcao_js=parent.js_mostramedicos', 'Pesquisa', false);
     } else {
       js_controla_campo(oNomeMedico, oCodigoMedico);
     }
   }
 }

 function js_mostramedicos(chave, erro) {

   document.form1.cm32_nome.value = chave;

   if(erro == true) {

     document.form1.cm01_i_medico.focus();
     document.form1.cm01_i_medico.value = '';
   }

   js_controla_campo(oNomeMedico, oCodigoMedico);
 }

 function js_mostramedicos1(chave1, chave2) {

   document.form1.cm01_i_medico.value = chave1;
   document.form1.cm32_nome.value     = chave2;

   js_controla_campo(oNomeMedico, oCodigoMedico);
   db_iframe_legista.hide();
 }

 function js_pesquisacm01_i_causa(mostra) {

   if(mostra == true) {
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_causa', 'func_causa.php?funcao_js=parent.js_mostracausa1|cm04_i_codigo|cm04_c_descr', 'Pesquisa', true);
   } else {
     if(document.form1.cm01_i_causa.value != '') {
       js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_causa', 'func_causa.php?pesquisa_chave='+document.form1.cm01_i_causa.value+'&funcao_js=parent.js_mostracausa', 'Pesquisa', false);
     } else {
       document.form1.cm04_c_descr.value = '';
     }
   }
 }

 function js_mostracausa(chave, erro) {

   document.form1.cm04_c_descr.value = chave;

   if(erro == true) {

     document.form1.cm01_i_causa.focus();
     document.form1.cm01_i_causa.value = '';
   }
 }

 function js_mostracausa1(chave1, chave2) {

   document.form1.cm01_i_causa.value = chave1;
   document.form1.cm04_c_descr.value = chave2;
   db_iframe_causa.hide();
 }

 function js_pesquisacm01_i_declarante(mostra) {

   if(mostra == true) {
     js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'func_nome', 'func_cgmtipo.php?funcao_js=parent.js_mostradeclarante1|z01_numcgm|z01_nome|z01_cgccpf&tipo=fisico', 'Pesquisa', true);
   } else {
     if(document.form1.cm01_i_declarante.value != '') {
       js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'func_nome', 'func_cgm.php?cgccpf=true&familia=true&pesquisa_chave='+document.form1.cm01_i_declarante.value+'&funcao_js=parent.js_mostradeclarante&tipo=fisico', 'Pesquisa', false);
     } else {
       document.form1.nome_declarante.value = '';
     }
   }
 }

 function js_mostradeclarante(erro, chave1, chave2, chave3, chave4) {
   if(document.form1.cm01_i_declarante.value == <?php echo isset($cm01_i_codigo) ? $cm01_i_codigo : 'null'; ?>) {
     alert('Aviso!\n\nCgm informado para o declarante é o mesmo para o Sepultamento!');
     erro = true;
   }

   document.form1.nome_declarante.value = chave1;
   document.getElementById('z01_cgccpf').value = chave4;

   if(erro == true) {

     document.form1.cm01_i_declarante.focus();
     document.form1.cm01_i_declarante.value = '';
     document.form1.nome_declarante.value   = '';
   }
 }

 function js_mostradeclarante1(chave1, chave2, chave3) {

   if(chave1 == <?php echo isset($cm01_i_codigo) ? $cm01_i_codigo : 'null'; ?> ) {
     alert('Aviso!\n\nCgm informado para o declarante é o mesmo para o Sepultamento!');
     return false;
   }

   document.form1.cm01_i_declarante.value = chave1;
   document.form1.nome_declarante.value   = chave2;
   document.getElementById('z01_cgccpf').value = chave3;

   func_nome.hide();
 }

 function js_pesquisa() {
   js_OpenJanelaIframe('CurrentWindow.corpo.iframe_a2', 'db_iframe_sepultamentos', 'func_sepultamentos.php?funcao_js=parent.js_preenchepesquisa|cm01_i_codigo', 'Pesquisa', true);
 }

 function js_preenchepesquisa(chave) {

   db_iframe_sepultamentos.hide();

   <?php
     if($db_opcao != 1) {
       echo " location.href = '".basename($_SERVER["PHP_SELF"])."?chavepesquisa='+chave";
     }
   ?>
 }

function js_validaTaxaSepultamentoJaCalculada() {
<?php
  $cm31_i_sepultamento = isset($cm31_i_sepultamento) ? $cm31_i_sepultamento : $_GET['cm01_i_codigo'];
  $sSqlTaxaSepultamentos = $oDaoTaxaSepultamentos->sql_query_file(null, "*", null, "cm31_i_sepultamento = $cm31_i_sepultamento");
  $rsTaxaSepultamentos = db_query($sSqlTaxaSepultamentos);
  $taxasSepultamentosComMesmoSepultamento = db_utils::getCollectionByRecord($rsTaxaSepultamentos);

  $taxaSepultamentoJaCalculada = (count($taxasSepultamentosComMesmoSepultamento) > 0) ? 'true' : 'false';

  echo "return $taxaSepultamentoJaCalculada;";
?>
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

function js_pesquisaTaxaServicos() {

js_divCarregando('Aguarde, Pesquisando Valores...','msgBoxTaxaServicos');

var iCodTaxaServ       = $F('cm10_i_taxaserv');
var dtLancamento       = $F('cm10_d_dtlanc');

if ( iCodTaxaServ != 0 ) {
  $('cm10_i_taxaserv').options[0].disabled = true;
}

var oParam              = new Object();
    oParam.exec         = "listarTaxaServicos";
    oParam.codtaxaserv  = iCodTaxaServ;
    oParam.dtlancamento = dtLancamento;
var oAjax               = new Ajax.Request(
                       "cem4_itensserv.RPC.php",
                      {
                        method    : 'post',
                        parameters: 'json='+Object.toJSON(oParam),
                        onComplete: js_retornoPesquisaTaxaServicos
                      }
                    );
}

function js_retornoPesquisaTaxaServicos(oAjax) {

js_removeObj('msgBoxTaxaServicos');

var aRetorno = JSON.parse(oAjax.responseText);

$('cm10_f_valor').value = "";

if ( aRetorno.numrows == 0 ) {

  alert('Nenhum valor cadastrado para taxa!');
  $('cm10_f_valortaxa').value = "";
  js_habilitabotaocalcular();
} else {

  if ( Number(aRetorno.oValorTaxa) == 0 ) {

    alert('Nenhum valor cadastrado para taxa!');
    $('cm10_f_valortaxa').value = "";
    js_habilitabotaocalcular();
  } else {
    $('cm10_f_valortaxa').value = js_formatar(aRetorno.oValorTaxa,'f');
    js_habilitabotaocalcular();
  }
}
}

function js_calcularValores() {

var iCodTaxaServ        = $F('cm10_i_taxaserv');
var dtLancamento        = $F('cm10_d_dtlanc');
var dtVencimento        = $F('cm10_d_privenc');
var iValorCorrigido     = $F('cm10_f_valortaxa');

if ( iValorCorrigido == "" ) {
  alert("Nenhum valor taxa para calcular!");
  return false;
}

js_divCarregando('Aguarde, Calculando Valores...','msgBoxCalcular');

if ( iCodTaxaServ != 0 ) {

  var oParam              = new Object();
      oParam.exec         = "calcularValores";
      oParam.codtaxaserv  = iCodTaxaServ;
      oParam.dtlancamento = dtLancamento;
      oParam.dtvencimento = dtVencimento;
      oParam.vlcorrigido  = iValorCorrigido;

  var oAjax               = new Ajax.Request(
                         "cem4_itensserv.RPC.php",
                        {
                          method    : 'post',
                          parameters: 'json='+Object.toJSON(oParam),
                          onComplete: js_retornoCalcularValores
                        }
                      );

} else {

  js_removeObj('msgBoxCalcular');
  alert("Selecione uma taxa de serviço!");
}
}

function js_retornoCalcularValores(oAjax) {

js_removeObj('msgBoxCalcular');

var aRetorno = JSON.parse(oAjax.responseText);

if ( aRetorno.numrows == 0 ) {
  alert('Nenhum valor cadastrado para taxa!');
  $('cm10_f_valortaxa').value = "";
  $('cm10_f_valor').value     = "";
} else {

  $('cm10_f_valor').value = js_formatar(aRetorno.oValorCorrigido,'f');
  js_habilitabotaocalcular();
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
 </script>
