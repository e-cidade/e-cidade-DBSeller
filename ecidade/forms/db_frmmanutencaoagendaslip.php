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

$clrotulo = new rotulocampo;
$clrotulo->label("e80_data");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e80_codage");
$clrotulo->label("e50_codord");
$clrotulo->label("e50_numemp");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e60_emiss");
$clrotulo->label("e82_codord");
$clrotulo->label("e87_descgera");
$clrotulo->label("k17_codigo");
$clrotulo->label("o15_descr");
$clrotulo->label("o15_codigo");
$clrotulo->label("o15_recurso");
$clrotulo->label("e21_sequencial");
$db_opcao = 1;
$db_instit = db_getsession("DB_instit");


$dao = new cl_sliptipooperacao;
$sql = $dao->sql_query_file(null, '*', '2', 'k152_sequencial in (1,2,5,13,9,500,16,17)');
$rs = db_query($sql);

$tiposPagamento = db_utils::getCollectionByRecord($rs);
?>
<script>
  function js_mascara(evt) {
    var evt = (evt) ? evt : (window.event) ? window.event : "";

    if ((evt.charCode >46 && evt.charCode <58) || evt.charCode ==0) {//8:backspace|46:delete|190:.
      return true;
    }else{
      return false;
    }
  }

</script>
<br><br>
<div >
    <form name="form1" method="post" action="">
        <table style='width:90%; '>
            <tr>
                <td width="40%">
                    <fieldset>
                        <legend><b>Filtros</b></legend>
                        <table border="0" align="left">
                            <tr>
                                <td nowrap title="<?= @$Tk17_slip ?>">
                                    <?php db_ancora("<b>Slip</b>", "js_pesquisak17_slip(true);", $db_opcao); ?>
                                </td>
                                <td nowrap>
                                    <?php db_input('k17_slip', 10, $Ie82_codord, true, 'text', $db_opcao, "onchange='js_pesquisak17_slip(false);'") ?>
                                </td>
                                <td>
                                    <?php db_ancora("<b>até:</b>", "js_pesquisak17_slip02(true);", $db_opcao); ?>
                                </td>
                                <td nowrap align="left">
                                    <?php db_input('k17_slip02', 10, $Ie82_codord, true, 'text', $db_opcao,
                                        "onchange='js_pesquisak17_slip02(false);'") ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Data Inicial:</b>
                                </td>
                                <td nowrap>
                                    <?php
                                    db_inputdata("dataordeminicial", null, null, null, true, "text", 1);
                                    ?>
                                </td>
                                <td>
                                    <b>Data Final:</b>
                                </td>
                                <td nowrap align="">
                                    <?php
                                    db_inputdata("dataordemfinal", null, null, null, true, "text", 1);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tz01_numcgm ?>">
                                    <?php
                                    db_ancora("<b  >Credor:</b>", "js_pesquisaz01_numcgm(true);", $db_opcao);
                                    ?>
                                </td>
                                <td colspan='4' nowrap>
                                    <?php
                                    db_input('z01_numcgm', 10, $Iz01_numcgm, true, 'text', $db_opcao, " onchange='js_pesquisaz01_numcgm(false);'");
                                    db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '')
                                    ?>
                                </td>
                            </tr>

                            <tr nowrap>
                                <td>
                                    <?php
                                    db_ancora("<b>Fonte de Recurso:</b>", "js_pesquisaRecurso(true)", 1);
                                    ?>
                                </td>
                                <td colspan="3">
                                    <?php
                                    db_input("o15_codigo", 8, null, false, "hidden", 3);
                                    db_input("gestao", 10, null, true, "text", 1, "onchange='js_pesquisaRecurso(false);'");
                                    db_input("o15_recurso", 8, null, true, "text", 3);
                                    db_input("descricao", 40, null, true, "text", 3);
                                    ?>
                                    <input type="text" name="slipsParciais" id="slipsParciais"
                                           style = "display:none" readonly value = "[]">
                                </td>
                            </tr>

                            <tr>
                                <td><strong>Complemento:</strong></td>
                                <td colspan='4'>
                                    <input type="text" name="complementoRecurso" id="complementoRecurso"
                                           readonly class="readonly field-size-max">
                                </td>
                            </tr>
                            <?php if (FONTE_RECURSO_UNIAO) : ?>
                                <tr>
                                    <td><b>Recurso Reduzido:</b></td>
                                    <td>
                                        <?php
                                        db_input('recurso_reduzido', 10, 1, true, 'text', 1, null, null, null, null, 3);
                                        ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (isParaiba()) : ?>
                            <tr>
                                <td>
                                    <strong><?php db_ancora('Retenção', "js_pesquisaRetencoes();", 2); ?></strong>
                                </td>
                                <td colspan='4' nowrap>
                                    <?php
                                    $rsInicio = db_query("SELECT null AS codigo, '' AS descr
                                                            UNION
                                                            SELECT e21_sequencial AS codigo,
                                                                   e21_descricao  AS descr
                                                            FROM   retencaotiporec
                                                            WHERE  e21_instit = {$db_instit}
                                                            ORDER BY codigo NULLS FIRST ");
                                    db_selectrecord('e21_sequencial', $rsInicio, true, $db_opcao, '', null);
                                    ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr style="display: none">
                                <td nowrap><b>Conta pagadora padrão:</b></td>
                                <td colspan=3 nowrap>

                                    <?php
                                    $clsaltes = new cl_saltes();
                                    $sWhere = " ( k13_limite is null or k13_limite >= '" . date("Y-m-d", db_getsession("DB_datausu")) . "')";
                                    $sWhere .= " and c61_instit = " . db_getsession("DB_instit");
                                    $result05 = $clsaltes->sql_record($clsaltes->sql_query_anousu(null, "k13_conta as e83_conta, k13_descr as e83_descr ", null, $sWhere));
                                    $numrows05 = $clsaltes->numrows;
                                    $arr['0'] = "Nenhum";

                                    for ($r = 0; $r < $numrows05; $r++) {
                                        db_fieldsmemory($result05, $r);
                                        $arr[$e83_conta] = "{$e83_conta} - {$e83_descr}";
                                    }

                                    $e83_codtipo = '0';
                                    db_select("e83_codtipo", $arr, true, 1, "onchange='js_setContaPadrao(this.value);' style='width:26em'");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap>
                                    <b>Tipo de Transmissão:</b>
                                </td>
                                <td nowrap>
                                    <?php

                                    $oDaoTipoTransmissao = new cl_empagetipotransmissao();
                                    $sSqlBuscaTipos = $oDaoTipoTransmissao->sql_query_file(null, "*", 'e57_sequencial');
                                    $rsBuscaTipos = $oDaoTipoTransmissao->sql_record($sSqlBuscaTipos);

                                    $aTipoTransmissao = array(0 => "Nenhum");
                                    for ($iTrans = 0; $iTrans < $oDaoTipoTransmissao->numrows; $iTrans++) {
                                        $oTipoTransmissao = db_utils::fieldsMemory($rsBuscaTipos, $iTrans);
                                        $aTipoTransmissao[$oTipoTransmissao->e57_sequencial] = $oTipoTransmissao->e57_descricao;
                                    }

                                    db_select('tipo_transmissao', $aTipoTransmissao, true, 1, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap><b>Forma de Pagamento Padrão:</b></td>
                                <td nowrap>

                                    <?php
                                    $rsFormaPagamento = $clempageforma->sql_record($clempageforma->sql_query(null));
                                    $iNumRowsPagamento = $clempageforma->numrows;
                                    $aFormaPagamento['0'] = "NDA";
                                    for ($r = 0; $r < $iNumRowsPagamento; $r++) {
                                        $oFormaPagamento = db_utils::fieldsMemory($rsFormaPagamento, $r);
                                        $aFormaPagamento[$oFormaPagamento->e96_codigo] = $oFormaPagamento->e96_descr;
                                    }

                                    $e96_codigo = '0';
                                    db_select("e96_codigo", $aFormaPagamento, true, 1, "onchange='js_setFormaPadrao(this.value);' style='width:10em'");
                                    ?>
                                </td>

                                <td nowrap="nowrap">
                                    <strong>Processo Administrativo:</strong>
                                </td>

                                <td>
                                    <?php db_input('k145_numeroprocesso', 10, null, true, 'text', 1, null, null, null, null, 15) ?>
                                </td>
                            </tr>
                            <tr>

                                <td nowrap="nowrap">
                                    <b>Data de Pagamento: </b>
                                </td>
                                <td>
                                    <?php
                                    $data = explode("-", date("d-m-Y", DB_getsession("DB_datausu")));
                                    db_inputdata("e42_dtpagamento", $data[0], $data[1], $data[2], true, "text", 1);
                                    ?>
                                </td>

                                <td nowrap="nowrap" colspan="2">
                                    <input type='checkbox' id='efetuarpagamento' onclick="js_showAutenticar(this)"/>

                                    <label for='efetuarpagamento'><b>Efetuar Pagamento</b></label>

                                    <span id='showautenticar' style='visibility:hidden'>
                                       <input type="checkbox" id='autenticar'/>
                                       <label for="autenticar"><b>Autenticar</b></label>
                                    </span>
                                </td>
                            </tr>

                            <tr style="display: none">
                                <td nowrap>
                                    <b>Contas Bancárias:</b></td>
                                <td>
                                    <?php
                                    $ar = [
                                        "todas" => "Todas",
                                        "contas" => "Contas da Unidade",
                                        "limiteSaque" => "Contas do Limite de Saque"
                                    ];
                                    $tipo_conta = 'todas';
                                    db_select("tipo_conta", $ar, true, 1);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td rowspan="1" valign="top" height="100%">
                    <fieldset>
                        <legend><b>Saldos da Conta</b></legend>
                        <table>
                            <tr>
                                <td style='color:blue' id='descrConta' colspan='4'>
                                </td>
                            </tr>
                            <tr>
                                <td valign='top'>
                                    <b>Tesouraria:</b>
                                </td>
                                <td style='text-align:right'>
                                    <pre>(+)</pre>
                                </td>
                                <td valign='top'>
                                    <?php
                                    db_input("saldotesouraria", 15, null, true, "text", 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td valign='top'>
                                    <b>Movimentos:</b>
                                </td>
                                <td style='text-align:right'>
                                    <pre>(-)</pre>
                                </td>
                                <td valign='top'>
                                    <?php
                                    db_input("totalcheques", 15, null, true, "text", 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td valign='top'>
                                    <b>Disponível:</b>
                                </td>
                                <td style='text-align:right' valign="">
                                    <pre>(=)</pre>
                                </td>
                                <td valign='top'>
                                    <?php
                                    db_input("saldoatual", 15, null, true, "text", 3);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Tipos de Operação</legend>
                        <?php foreach ($tiposPagamento as $tipo): ?>
                            <input type="checkbox" id="tipoPagamento_<?=$tipo->k152_sequencial?>" name="tipoPagamento"
                                   value="<?=$tipo->k152_sequencial?>" checked class="tiposOperacao">
                            <label for="tipoPagamento_<?=$tipo->k152_sequencial?>"><?=$tipo->k152_descricao?></label>
                            <br>
                        <?php endforeach ?>
                    </fieldset>
                </td>
                <td width='20%'>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan='4' style='text-align: center'>
                    <input name="pesquisar" id='pesquisar' type="button" value="Pesquisar"
                           onclick='return js_pesquisarOrdens();'>
                    <input name="atualizar" id='atualizar' type="button" value="Atualizar" onclick='js_configurar()'>
                    <input name="emitecheque" id='emitecheque' type="button" value='Emitir Cheque'
                           onclick='location.href="emp4_empageformache001.php"'>
                    <input name="emitetxt" id='emitetxt' type="button" value='Emitir Arquivo Texto'
                           onclick='location.href="emp4_empageconfgera001.php"'>
                </td>
            <tr>
                <td colspan='3'>
                    <fieldset>
                        <legend><b>Ordens</legend>
                        <div id='gridNotas' style="width: 100%">
                        </div>
                        <p style="background-color: yellow; border: 1px solid black; padding: 5px; width: 30%;">
                            <b>Clique duplo sob a linha para alterar o histórico de pagamento.</b>
                        </p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan='5' align='left'>
                    <span>**</span><b>Conta conferida</b>
                    <br>
                    <span>
                        <fieldset><legend><b>Mostrar</b></legend>
                            <input type="checkbox" id='configuradas' onclick='js_showFiltro("configurada",this.checked)'>
                            <label for="configuradas" style='padding:1px;border: 1px solid black; background-color:#d1f07c'>
                                <b>Atualizados</b>
                            </label>
                            <input type="checkbox" id='normais' checked onclick='js_showFiltro("normal",this.checked)'>
                            <label for="normais" style='padding:1px;border: 1px solid black;background-color:white'><b>Não Atualizados</b></label>
                            <input type="checkbox" id='comMovs' onclick='js_showFiltro("comMov",this.checked)'>
                            <label for="comMovs" style='padding:1px;border: 1px solid black;background-color:rgb(222, 184, 135)'>
                                <b>Com cheque/em Arquivo</b>
                            </label>
                        </fieldset>
                    </span>
                </td>
            </tr>
        </table>
    </form>
</div>
<div style='position:absolute;top: 200px; left:15px;
            border:1px solid black;
            width:300px;
            text-align: left;
            padding:3px;
            background-color: #FFFFCC;
            display:none;' id='ajudaItem'>

</div>
<script>
    const UF = '<?=getEstadoInstituicao()?>';
    const isPB = UF === 'PB';

    function js_marcaEfetuarPagamento( sValor, codmov ){

      if (isPB) {

         if (sValor == 4) {

          //  js_showAutenticar($("efetuarpagamento"))
           $("efetuarpagamento").checked = true;
           $('showautenticar').style.visibility = 'visible';
           //$('autenticar').checked   = true;



           $("ctnBancoAgencia_"+ codmov).style.display = "none";  // oculta
           $("ctnHistorico_" + codmov ).style.display = "";  // mostra

         } else {

           $("efetuarpagamento").checked = false;
           $('showautenticar').style.visibility = 'hidden';
           //$('autenticar').checked = false;


           $("ctnBancoAgencia_"+ codmov).style.display = "";  // mostra
           $("ctnHistorico_" + codmov ).style.display = "none";  // oculta
         }

      }
    }




  var contasPagadoras = [];
  sDataDia = "<?=date("d/m/Y",db_getsession("DB_datausu"))?>";
  function js_reload(){
    document.form1.submit();
  }




  function js_showAutenticar(obj) {
    if (obj.checked) {

      $('showautenticar').style.visibility = 'visible';
      $('autenticar').checked               = true;

    } else {

      $('showautenticar').style.visibility = 'hidden';
      $('autenticar').checked              = false;
    }
  }


  //-----------------------------------------------------------
  //---ordem 01
  function js_pesquisak17_slip(mostra){
    if(mostra==true){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_slip.php?funcao_js=parent.js_mostraslip1|k17_codigo','Pesquisa',true);
    }else{
      ord01 = new Number(document.form1.k17_slip.value);
      ord02 = new Number(document.form1.k17_slip02.value);
      if(ord01 > ord02 && ord01 != "" && ord02 != ""){
        alert("Selecione um slip menor que o segundo!");
        document.form1.k17_slip.focus();
        document.form1.k17_slip.value = '';
      }
    }
  }
  function js_mostraslip1(chave1){
    document.form1.k17_slip.value = chave1;
    db_iframe_pagordem.hide();
  }
  //-----------------------------------------------------------
  //---ordem 02
  function js_pesquisak17_slip02(mostra){
    if(mostra==true){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_slip.php?funcao_js=parent.js_mostraslip102|k17_codigo','Pesquisa',true);
    }else{
      ord01 = new Number(document.form1.k17_slip.value);
      ord02 = new Number(document.form1.k17_slip02.value);
      if(ord01 > ord02 && ord02 != ""  && ord01 != ""){
        alert("Selecione uma ordem maior que a primeira");
        document.form1.k17_slip02.focus();
        document.form1.k17_slip02.value = '';
      }
    }
  }
  function js_mostraslip102(chave1,chave2){
    document.form1.k17_slip02.value = chave1;
    db_iframe_pagordem.hide();
  }


  function js_pesquisaz01_numcgm(mostra){
    if(mostra==true){
      js_OpenJanelaIframe('','func_nome','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
    }else{
      if(document.form1.z01_numcgm.value != ''){
        js_OpenJanelaIframe('','func_nome','func_nome.php?pesquisa_chave='+document.form1.z01_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
      }else{
        document.form1.z01_nome.value = '';
      }
    }
  }
  function js_mostracgm(erro,chave){
    document.form1.z01_nome.value = chave;
    if(erro==true){
      document.form1.z01_numcgm.focus();
      document.form1.z01_numcgm.value = '';
    }
  }

  function js_mostracgm1(chave1,chave2){
    document.form1.z01_numcgm.value = chave1;
    document.form1.z01_nome.value = chave2;
    func_nome.hide();
  }

  function js_pesquisarOrdens() {

      let checkBoxes = document.querySelectorAll('input.tiposOperacao[type="checkbox"]');
      let tiposOperacao = [];

      for (const input of checkBoxes) {
          if (input.checked) {
              tiposOperacao.push(input.value);
          }
      }

    js_liberaBotoes(false);
    js_reset();
    $('TotalForCol8').innerHTML = "0,00";
    // $('normais').checked = true;
    // Criamos um objeto que tera a requisicao
    var oParam                 = new Object();
    oParam.iOrdemIni           = $F('k17_slip');
    oParam.iOrdemFim           = $F('k17_slip02');
    oParam.dtDataIni           = $F('dataordeminicial');
    oParam.dtDataFim           = $F('dataordemfinal');
    oParam.iNumCgm             = $F('z01_numcgm');
    oParam.sDtAut              = $F('e42_dtpagamento');
    oParam.iRecurso            = $F('o15_codigo');
      oParam.tiposOperacao = tiposOperacao.join(',')
    if(isPB) {
        oParam.iRetencao = $F('e21_sequencial');
    }
    oParam.k145_numeroprocesso = encodeURIComponent(tagString($F("k145_numeroprocesso")));
    oParam.recurso_reduzido = null;

    if (document.querySelector('#recurso_reduzido')) {
        oParam.recurso_reduzido = document.querySelector('#recurso_reduzido').value;
    }

    /**
     * Zera os slips parciais. Estes slips podem ser visto na depuracao
     * habilitando o display para Text em sua declaracao.
     */
    let slipsParciais = JSON.parse($('slipsParciais').value);
    if(slipsParciais.length > 0){
      slipsParciais = [];
      $('slipsParciais').value = JSON.stringify(slipsParciais);
    }

    var sParam           = js_objectToJson(oParam);
    js_divCarregando("Aguarde, pesquisando Movimentos.","msgBox");
    url       = 'emp4_manutencaoPagamentoRPC.php';
    var sJson = '{"exec":"getMovimentosSlip","params":['+sParam+']}';
    var oAjax   = new Ajax.Request(
      url,
      {
        method    : 'post',
        parameters: 'json='+sJson,
        onComplete: js_retornoConsultaMovimentos
      }
    );

  }

  function js_retornoConsultaMovimentos(oAjax) {

    js_removeObj("msgBox");
    js_liberaBotoes(true);
    var oResponse = JSON.parse(oAjax.responseText);
    gridNotas.clearAll(true);
    var iRowAtiva     = 0;
    var iTotalizador  = 0;

    if (oResponse.status == 1) {

      var lContaPagadoraInvalida = false;

      contasPagadoras = oResponse.contas;
      for (var iNotas = 0; iNotas < oResponse.aSlips.length; iNotas++) {

        with (oResponse.aSlips[iNotas]) {

          iTotalizador ++;
          var nValor    =  k17_valor;
          nValorTotal   = new Number(nValor).toFixed(2);
          var lDisabled = false;
          var sDisabled = "";
          if (e91_codmov != '' || e90_codmov != '' && e90_cancelado == 'f') {

            lDisabled = true;
            sDisabled = " disabled ";

          }
          var aLinha  = new Array();
          aLinha[0]   = e81_codmov;
          aLinha[1]   = "<a onclick='js_pesquisaSlip("+k17_codigo+");return false;' href='#'>";
          aLinha[1]  += k17_codigo+"</a>";
          aLinha[2]   = gestao;
          aLinha[3]   = recurso;
          aLinha[4]   = js_createComboContasPag(e81_codmov, e83_conta, false);
          if (e85_codtipo == '') {
            aLinha[4] = '  Nenhuma Conta Pag. Cadastrada';
            lContaPagadoraInvalida = true;
          }
          aLinha[5] = z01_nome.urlDecode().substring(0,20);
          if (tiposlip == 2) {
              var conteudoColunaCinco = "<span style='display:'  id='ctnBancoAgencia_" +e81_codmov + "'>" + js_createComboContasForne(aContasFornecedor, conta_configurada, e81_codmov, z01_numcgm, lDisabled, iUltimaContaUtilisada) + " </span>";
          } else {

            var conteudoColunaCinco = "<span style='display:' id='ctnBancoAgencia_" + e81_codmov + "'>" + "<span style='display:none'>con</span> <input readonly='readonly' style='width:350px;' type = 'text' id= 'ctapagfor_"+e81_codmov+"' value='" + k17_debito+ " - "+descricaodebito.urlDecode() + "'/></span>";
          }

          var conteudoColunaOito = js_formatar(k17_valor,"f");
          if (isPB) {
            conteudoColunaCinco += "<span  style='display:none' id='ctnHistorico_" + e81_codmov + "'> <input placeholder='Digite o Histórico' style='width:350px;' type='text' id = 'txtHistorico_"+e81_codmov+"' /> </span>";

            var slipTipoOperacaoPagamento = ['9','13'];
            if (slipTipoOperacaoPagamento.include(k153_slipoperacaotipo)){

              conteudoColunaOito = "<input  style='width:70px;text-align:right' type = 'text'";
              conteudoColunaOito += " name = 'valorSlip_"+k17_codigo+"' value='" + js_formatar(k17_valor,"f") + "'";
              conteudoColunaOito += " id = 'valorSlip_"+k17_codigo+"'";
              conteudoColunaOito += " data-mask='money'";
              conteudoColunaOito += " data-maxValue='"+ js_formatar(k17_valor,"f") + "'";
              conteudoColunaOito += " onchange = 'js_alertaValorSlip("+k17_codigo+","+k17_valor+")'";
              conteudoColunaOito += "/>";
            }

          }
          conteudoColunaCinco += "<input type='hidden' id='txtTipo_"+e81_codmov+"' value='"+tiposlip+"' > ";

          aLinha[6]   = conteudoColunaCinco;
          aLinha[7]   = js_createComboForma(e97_codforma, e81_codmov, lDisabled, permite_pagamento_remessa);
          aLinha[8]   = js_formatar(k17_data,"d");
          aLinha[9]   = conteudoColunaOito;
          gridNotas.addRow(aLinha, false, lDisabled);
          gridNotas.aRows[iRowAtiva].sEvents = "ondblclick='alterarHistoricoPagamento("+e81_codmov+")'";

          // acrescentado no if a condicao cancelado = true, pois agora os registros da empageconfgera
          // ao cancelar um arquivo, nao serão mais deletados
          if (e91_codmov != '' || e90_codmov != '' && e90_cancelado == 'f') {

            if (!$('comMovs').checked) {

              iTotalizador--;
              gridNotas.aRows[iRowAtiva].lDisplayed = false;

            }
            gridNotas.aRows[iRowAtiva].aCells[0].lDisabled  = true;
            gridNotas.aRows[iRowAtiva].setClassName('comMov');

          } else if (e86_codmov != '' || e97_codmov != '') {

            if (!$('configuradas').checked) {

              iTotalizador--;
              gridNotas.aRows[iRowAtiva].lDisplayed = false;

            }
            gridNotas.aRows[iRowAtiva].aCells[0].lDisabled  = true;
            gridNotas.aRows[iRowAtiva].setClassName('configurada');

          }
          gridNotas.aRows[iRowAtiva].aCells[5].sEvents  = "onmouseover='js_setAjuda(\""+z01_nome.urlDecode()+"\",true)'";
          gridNotas.aRows[iRowAtiva].aCells[5].sEvents += "onmouseOut='js_setAjuda(null,false)'";
          gridNotas.aRows[iRowAtiva].sValue  = e81_codmov;
          iRowAtiva++;

        }
      }
      gridNotas.renderRows();
      gridNotas.setNumRows(iTotalizador);
      $('gridNotasstatus').innerHTML = "&nbsp;<span style='color:blue' id ='total_selecionados'>0</span> Selecionados";

      if (lContaPagadoraInvalida) {
        alert("Slip de recebimento deve ser autenticado na rotina: \n\nCAIXA > PROCEDIMENTOS > OPERAÇÃO FINANCEIRA EXTRA ORÇAMENTÁRIA > AUTENTICAÇÃO DE RECEBIMENTOS");
      }

      js_inicializaMascaraSlipsParciais();

    }
  }

  function js_init() {

    gridNotas              = new DBGrid("gridNotas");
    gridNotas.nameInstance = "gridNotas";
    gridNotas.selectSingle = function (oCheckbox,sRow,oRow,lVerificaSaldo) {
      if (oRow.getClassName() == 'comMov') {
        oCheckbox.checked = false;
      }
      if (lVerificaSaldo == null) {
        var lVerificaSaldo = true;
      }

      if (oCheckbox.checked ) {

        oRow.isSelected    = true;
        $(sRow).className  += 'marcado';
        oRow.isSelected    = true;
        if (oRow.aCells[5].getValue() != "") {
          if ($('ctapag'+oRow.sValue) && lVerificaSaldo) {
            js_getSaldos($('ctapag'+oRow.sValue), oRow.aCells[0].getContent());
          } else if (lVerificaSaldo) {
            js_getSaldosMov(oRow.aCells[5].getValue(),oRow.aCells[5].getContent(), oRow.aCells[0].getValue());
          }
        }

        let codigoSlip = oRow.aCells[2].getValue();

        if(isPB && $('valorSlip_'+codigoSlip) != null){
          let strValorSlip = $('valorSlip_'+codigoSlip).value.replace('.','').replace(",",".");
          let valorSlip = parseFloat(strValorSlip).toFixed(2);
          let slipsParciais = JSON.parse($('slipsParciais').value);
          let contemSlipParcial = slipsParciais.includes(codigoSlip);
          let valorMaximo  = $('valorSlip_'+codigoSlip).dataset.maxvalue.replace('.','').replace(",",".");

          if(!contemSlipParcial && valorSlip <valorMaximo){
            slipsParciais.push(codigoSlip);
          }
          $('slipsParciais').value = JSON.stringify(slipsParciais);
          $('valorSlip_'+codigoSlip).setAttribute('disabled',true);
        }

        if (lVerificaSaldo) {
          $('TotalForCol10').innerHTML = js_formatar(gridNotas.sum(10).toFixed(2),'f');
        }
        $('total_selecionados').innerHTML = new Number($('total_selecionados').innerHTML)+1;
      } else {

        let slipsParciais = JSON.parse($('slipsParciais').value);
        let codigoSlip = oRow.aCells[2].getValue();
        if (isPB && $('valorSlip_'+codigoSlip) != null){
          let contemSlipParcial = slipsParciais.includes(codigoSlip);
          if (contemSlipParcial){
            slipsParciais.splice(slipsParciais.indexOf(codigoSlip),1);
            $('slipsParciais').value = JSON.stringify(slipsParciais);
          }
          $('valorSlip_'+codigoSlip).removeAttribute('disabled', false);
        }

        $(sRow).className = oRow.getClassName();
        oRow.isSelected   = false;
        $('total_selecionados').innerHTML = new Number($('total_selecionados').innerHTML)-1;
        if (lVerificaSaldo) {
          $('TotalForCol10').innerHTML = js_formatar(gridNotas.sum(10).toFixed(2),'f');
        }
      }
    }
    gridNotas.selectAll = function(idObjeto, sClasse, sLinha) {

      var obj = document.getElementById(idObjeto);
      if (obj.checked){
        obj.checked = false;
      } else{
        obj.checked = true;
      }

      itens = this.getElementsByClass(sClasse);
      for (var i = 0;i < itens.length;i++){

        if (itens[i].disabled == false){
          if (obj.checked == true){

            if ($(this.aRows[i].sId).style.display != 'none') {
              if (!itens[i].checked) {

                itens[i].checked=true;
                this.selectSingle($(itens[i].id), (sLinha+i), this.aRows[i], false);

              }

            }
          } else {

            if (itens[i].checked) {

              itens[i].checked=false;
              this.selectSingle($(itens[i].id), (sLinha+i), this.aRows[i], false);
            }
          }
        }
      }
      $('TotalForCol10').innerHTML = js_formatar(gridNotas.sum(10).toFixed(2),'f');
    }
    gridNotas.setCheckbox(0);
    gridNotas.allowSelectColumns(true);
    gridNotas.hasTotalizador = true;
    gridNotas.setCellAlign(new Array("right", "right", "center", "center", "left", "left", "left", "center", "center","right"));
    var tituloColunaBancoAg = "Banco/Ag";
    if (isPB) {
        var tituloColunaBancoAg =  "Banco/Ag / Número Doc";
    }
    gridNotas.setHeader(new Array("Mov.","Slip","Recurso","Subrecurso", "Cta. Pag", "Nome",
         tituloColunaBancoAg,
        "Forma Pgto",
        "Dt Slip",
        "Valor slip"
      )
    );
    gridNotas.setCellWidth(new Array( '30px' ,
                                            '40px',
                                            '40px',
                                            '60px',
                                            '200px',
                                            '120px',
                                            '180px',
                                            '70px',
                                            '60px',
                                            '70px'
                                           ));
    gridNotas.aHeaders[1].lDisplayed = false;
    gridNotas.show(document.getElementById('gridNotas'));
    $('gridNotasstatus').innerHTML = "&nbsp;<span style='color:blue' id ='total_selecionados'>0</span> Selecionados";
    $('TotalForCol8').innerHTML    = "0,00";

  }

  function js_createComboContasPag(iCodMov,iConta, lDisabled) {

    var sDisabled = "";
    if (lDisabled == null) {
      lDisabled = false;
    }
    if (lDisabled) {
      sDisabled = " disabled ";
    }
    var sCombo  = "<select id='ctapag"+iCodMov+"' class='ctapag' style='width:100%'";
    sCombo     += " onchange='js_getSaldos(this)' "+sDisabled+">";
    sCombo     += "<option  value='selecione'>Selecione</option>";

      var ultimoBanco = null;
      contasPagadoras.each(
          function (dadosConta) {

              if (ultimoBanco != dadosConta.banco) {

                  sCombo += "</optgroup>";
              }

              if (ultimoBanco === null || ultimoBanco != dadosConta.banco) {
                  sCombo += "<optgroup label='Banco: "+dadosConta.banco+"'>";
              }
              sCombo += "<option "+(dadosConta.e83_conta == iConta ? ' selected ' : '') +"  value="+dadosConta.e83_conta+">"+dadosConta.e83_conta+" - "+dadosConta.e83_descr+" - Rec. "+dadosConta.recurso +"</option>";
              ultimoBanco = dadosConta.banco;
          }
      );
      sCombo += "</optgroup>";
      sCombo  += "</select>";

    return sCombo;
  }

  function js_showFiltro(sQualFiltro,lMostrar) {

    var aMatched  = gridNotas.getElementsByClass(sQualFiltro);
    aMatched      = aMatched.concat(gridNotas.getElementsByClass(sQualFiltro+"marcado"));
    var iTotalizador = 0;
    for (var i = 0; i < aMatched.length; i++) {
      if (lMostrar) {

        aMatched[i].style.display = '';
        iTotalizador++;

      } else {

        aMatched[i].style.display = 'none';
        iTotalizador--;

      }
    }
    var iTotal  = gridNotas.getNumRows();
    gridNotas.setNumRows(iTotal +iTotalizador);
  }

  function js_createComboForma(iTipoForma, iCodMov, lDisabled, sPermitePagamentoRemessa) {

    var sDisabled = "";
    if (lDisabled == null) {
      lDisabled = false;
    }
    if (lDisabled) {
      sDisabled = " disabled ";
    }

    var sCombo  = "<select style='width:100%' class='formapag' id='forma"+iCodMov+"' "+sDisabled+"onchange='js_marcaEfetuarPagamento(this.value, "+iCodMov+")'>";
    if (sPermitePagamentoRemessa == "f") {
        sCombo     += "  <option "+(iTipoForma == 0?" selected ":" ")+" value='0'>NDA</option>";
        sCombo     += "  <option "+(iTipoForma == 4?" selected ":" ")+" value='4'>DEB</option>";
    } else {
      sCombo     += "  <option "+(iTipoForma == 0?" selected ":" ")+" value='0'>NDA</option>";
      sCombo     += "  <option "+(iTipoForma == 1?" selected ":" ")+" value='1'>DIN</option>";
      sCombo     += "  <option "+(iTipoForma == 2?" selected ":" ")+" value='2'>CHE</option>";
      sCombo     += "  <option "+(iTipoForma == 3?" selected ":" ")+" value='3'>TRA</option>";
      sCombo     += "  <option "+(iTipoForma == 4?" selected ":" ")+" value='4'>DEB</option>";
    }
    sCombo     += "</select>";
    return sCombo
  }

  function js_createComboContasForne(aContasForne, iContaForne, iCodMov, iNumCgm, lDisabled, iCodigoUltimaConta) {

    var sDisabled = "";
    if (lDisabled == null) {
      lDisabled = false;
    }
    if (lDisabled) {
      sDisabled = " disabled ";
    }
    var sRetorno  = "<select id='ctapagfor"+iCodMov+"' "+sDisabled+" onchange='js_novaContaBancaria(this.value, "+iNumCgm+");' class='cgm' style='width:100%'>";

    sRetorno     += "<option value=''>Selecione</option>";
    sRetorno     += "<option value='novaConta'>Nova conta</option>";
    if (aContasForne != null) {

      for (var i = 0; i < aContasForne.length; i++) {

        with (aContasForne[i]) {

          var sConferido = '';
          var sSelected = '';

          if (aContasForne[i].pc63_contabanco == iContaForne) {
            sSelected = " selected ";
          }else if ( iContaForne == '' && aContasForne[i].pc63_contabanco == iCodigoUltimaConta ) {
            sSelected = " selected ";
          }

          sRetorno += "<option value ='"+pc63_contabanco+"' "+sSelected+">";
          if (pc63_agencia_dig.trim() != ""){
            pc63_agencia_dig = "/"+pc63_agencia_dig;
          }
          if (pc63_conta_dig.trim() != ""){
            pc63_conta_dig = "/"+pc63_conta_dig;
          }

          if (pc63_dataconf.trim() != "" ){
            sConferido = "**";
          }
          sRetorno += sConferido+" - "+pc63_banco+' - '+pc63_agencia+""+pc63_agencia_dig+' - '+pc63_conta+""+pc63_conta_dig;
          sRetorno += "</option>";
        }
      }
    }
    sRetorno += "</select>";
    return sRetorno;
  }

  function js_novaContaBancaria(lNovaConta, iCgm){

    if (lNovaConta != 'novaConta') {
      return false;
    }

    js_OpenJanelaIframe('CurrentWindow.corpo',
      'db_iframe_NovaConta',
      'com1_pcfornecon001.php?novo=true&reload=true&z01_numcgm=' + iCgm,
      'Cadastro de Nova Conta',
      true);

  }

  function js_dbInputData(sName, value, lDisabled){

    var sDisabled = "";
    if (lDisabled == null) {
      lDisabled = false;
    }
    if (lDisabled) {
      sDisabled = " disabled ";
    }
    var sSaida  = '<input readonly name="'+sName+'" type="text" '+sDisabled+' style="height:100%;width:7em"  id="'+sName+'"';
    sSaida += '   value="'+value+'" size="10"  maxlength="10" autocomplete="off"';
    sSaida += '   onBlur="js_validaDbData(this);" onKeyUp="return js_mascaraData(this,event)"';
    sSaida += '   onSelect="return js_bloqueiaSelecionar(this);" onFocus="js_validaEntrada(this);">';
    sSaida += '<input name="'+sName+'_dia" type="hidden" title="" id="'+sName+'_dia" value=""  maxlength="2" >';
    sSaida += '<input name="'+sName+'_mes" type="hidden" title="" id="'+sName+'_mes" value=""  maxlength="2" >';
    sSaida += '<input name="'+sName+'_ano" type="hidden" title="" id="'+sName+'_ano" value=""  maxlength="4" >';

    return sSaida;
  }

  function js_novaConta(Movimento,iNumCgm, sOpcao ){
    erro = 0;
    if(sOpcao == 'n' || sOpcao == 'button'){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pcfornecon',
        'com1_pcfornecon001.php?novo=true&reload=true&z01_numcgm='+iNumCgm,
        'Cadastro de Contas de Fornecedores',true);
    }
  }

  function js_setAjuda(sTexto,lShow) {

    if (lShow) {

      el =  $('gridNotas');
      var x = 0;
      var y = el.offsetHeight;
      while (el.offsetParent && el.tagName.toUpperCase() != 'BODY') {

        x += el.offsetLeft;
        y += el.offsetTop;
        el = el.offsetParent;

      }
      x += el.offsetLeft;
      y += el.offsetTop;
      $('ajudaItem').innerHTML     = sTexto;
      $('ajudaItem').style.display = '';
      $('ajudaItem').style.top     = y+10;
      $('ajudaItem').style.left    = x;

    } else {
      $('ajudaItem').style.display = 'none';
    }
  }

  function js_configurar() {

    var aMovimentos = gridNotas.getSelection();
    /*
     * Validamos o movimento configurado, conforme a forma de pagamento escolhido.
     * - cheque, é obrigatorio ter informado a conta pagadora, e o valor;
     * - Transmissao é obrigatorio ter informado a conta pagadora, e a conta do fornecedor
     * - Dinheiro , apenas obrigatorio informar  valor.
     * - NDA, ignoramos o registro.
     */

    var lSemErro         = true;
    var sAviso           = '';

    if (aMovimentos.length == 0) {

      alert('Não há nenhum movimento selecionado.');
      return false;

    }

    if ($F('e42_dtpagamento') == "") {

      alert('Data de pagamento nao informado.');
      return false;

    }
    if (js_comparadata(sDataDia,$F('e42_dtpagamento'),">")) {

      alert("Data Informada Inválida.\nData menor que a data do sistema");
      return false;

    }

    let slipsParciais = JSON.parse($('slipsParciais').value);
    if (slipsParciais.length > 0){
      let msgSlipsParciais = "Você está pagando parcialmente o(s) Slip(s) ";
      msgSlipsParciais += slipsParciais;
      msgSlipsParciais += ". Deseja realmente avançar?";
      if(!confirm(msgSlipsParciais)){
        return false;
      }
    }

    var oEnvio                 = new Object();
    oEnvio.exec                = "configurarPagamento";
    oEnvio.dtPagamento         = $F('e42_dtpagamento');
    oEnvio.iTipoTransmissao    = $F('tipo_transmissao');
    oEnvio.aMovimentos         = new Array();
    oEnvio.lAutenticar         = false;
    oEnvio.lEmitirOrdeAuxiliar = false;
    oEnvio.lEfetuarPagamento   = false;



    if ($('autenticar').checked) {
      oEnvio.lAutenticar         = true;
    }
    if ($('efetuarpagamento').checked) {''
      oEnvio.lEfetuarPagamento   = true;
      oEnvio.exec                = "efetuarPagamentoSlip";
    }

    var aFormasSelecionadas = new Array();



    for (var iMov = 0; iMov < aMovimentos.length; iMov++) {
      var iForma           = aMovimentos[iMov][8];
      var iCodMov          = aMovimentos[iMov][0];
      var nValor           = js_strToFloat(aMovimentos[iMov][10]).valueOf();
      var iNota            = aMovimentos[iMov][2];

      var tipo = $('txtTipo_' + iCodMov).value;
      var iContaFornecedor = "con";

      if (tipo == 2) {
          var iContaFornecedor = $("ctapagfor"+iCodMov ).value;//.split(" ");   ;//aMovimentos[iMov][6];
      }
      var iContaPagadora = aMovimentos[iMov][5];

      var sHistorico = "";

      if (isPB) {

        var sHistorico = encodeURIComponent(tagString($F("txtHistorico_" + iCodMov)));
      }

     //console.log("CONTA:" + iContaFornecedor, aMovimentos[iMov][0]);

      /*
       * Fazemos a verificacao para Cheque;
       */
      aFormasSelecionadas.push(iForma);

      if (iForma == 2 || iForma == 3) {


        if (iContaPagadora == 0) {

          lSemErro = false;
          sAviso   = "Movimento ("+iCodMov+") do Slip "+iNota+" Sem conta pagadora Informada.";

        }
      }
      /*
       retirado tratamento para que seja possivel atualizar um movimento do tipo TRA sem conta bancaria
       if (iForma == 3 ) {

       if (iContaFornecedor == 'n' || iContaFornecedor == '') {

       lSemErro = false;
       sAviso   = "Movimento ("+iCodMov+") do Slip "+iNota+" sem Conta do fornecedor Configurada.";

       }
       }
       */
      if (!lSemErro) {

        alert(sAviso);
        return false;
        break;

      }
      oMovimento                  = new Object();
      oMovimento.iCodForma        = iForma;
      oMovimento.iCodMov          = iCodMov;
      oMovimento.nValor           = nValor;
      oMovimento.iContaFornecedor = iContaFornecedor;
      oMovimento.iContaPagadora   = iContaPagadora;
      oMovimento.iCodNota         = iNota;
      oMovimento.nValorRetencao   = 0
      oMovimento.sHistorico       = sHistorico;
      oEnvio.aMovimentos.push(oMovimento);
    }

    if ($('efetuarpagamento').checked) {

      for (var iInd = 0; iInd < aFormasSelecionadas.length; iInd++ ) {

        if (aFormasSelecionadas[iInd] == "2" ) {

          alert("Para efetuar pagamento automático somente são permitidas as forma de pagamento : Dinheiro (DIN), Débito (DEB) e Transmissão (TRA). Verifique.");
          return false;
        }
      }
    }
    //console.log(oEnvio);

    js_divCarregando("Aguarde, Configurando Movimentos.","msgBox");
    js_liberaBotoes(false);
    var sJson = js_objectToJson(oEnvio);
    var oAjax = new Ajax.Request(
      url,
      {
        method    : 'post',
        parameters: 'json='+sJson,
        onComplete: js_retornoConfigurarPagamentos
      }
    );
  }

  function js_retornoConfigurarPagamentos(oAjax) {

    js_removeObj("msgBox");
    js_liberaBotoes(true);
    var oRetorno = JSON.parse(oAjax.responseText);
    if (oRetorno.status == 1) {

      var movimentosSelecionados = [];
      gridNotas.getSelection().each(
        function (dadosSlip) {

          if (dadosSlip[8] === '3'){
            movimentosSelecionados.push(dadosSlip[1]);
          }
        }
      );

      if (movimentosSelecionados.length > 0) {

        var DBViewConfiguracaoEnvio = new DBViewConfiguracaoEnvioTransmissao(movimentosSelecionados, 2);
        DBViewConfiguracaoEnvio.verificarMovimentos();
      } else {
        alert("Movimentos atualizados com sucesso!");
      }

      /**
       * Na pesquisa, os slipsParciais sao removidos. Logo, precisam
       * ser evidenciados antes. A pesquisa tambem tem que vir antes
       * do PDF pois se nao ela deixa de funcionar.
       *  */
      let slipsParciais = JSON.parse($('slipsParciais').value);
      js_pesquisarOrdens();

      if (isPB && slipsParciais.length > 0){
        js_emitePDFSlip(oRetorno.novosSlipsParciais.toString());
        slipsParciais = [];
        $('slipsParciais').value = JSON.stringify(slipsParciais);
      }

      if ($('autenticar').checked) {

        aAutenticacoes       = oRetorno.aAutenticacoes;
        iIndice              = 0;
        js_autenticar(oRetorno.aAutenticacoes[0],false);
      }

    } else {
      alert(oRetorno.message.urlDecode());
    }



  }

  function js_emitePDFSlip(slips){
    window.open('cai1_slip003.php?numslip=' + slips, '', 'location=0');
  }

  function js_autenticar(oAutentica, lReautentica) {

    var sPalavra = 'Autenticar';
    if (lReautentica) {
      var sPalavra = "Autenticar novamente";
    }

    if (confirm(sPalavra + ' a Nota '+oAutentica.iNota+'?')) {

      var oRequisicao      = new Object();
      oRequisicao.exec     = "Autenticar";
      oRequisicao.sString  = oAutentica.sAutentica;
      var sJson            = js_objectToJson(oRequisicao);
      var oAjax = new Ajax.Request(
        'emp4_pagarpagamentoRPC.php',
        {
          method    : 'post',
          parameters: 'json='+sJson,
          onComplete: js_retornoAutenticacao
        }
      );
    } else {

      iIndice++;
      if (aAutenticacoes[iIndice]) {
        js_autenticar(aAutenticacoes[iIndice],false);
      } else {

        js_pesquisarOrdens();
      }
    }
  }

  function js_retornoAutenticacao(oAjax) {

    var oRetorno = JSON.parse(oAjax.responseText);
    if (oRetorno.status == 1 || oRetorno.status == '1') {

      js_autenticar(aAutenticacoes[iIndice], true);

    } else {

      alert(oRetorno.message.urlDecode());
      js_pesquisarOrdens();

    }
  }


  function js_calculaValor(oTextObj, iCodMov) {

    var nValorAut = js_strToFloat($('valoraut'+iCodMov).innerHTML);
    var nRetencao = js_strToFloat($('retencao'+iCodMov).innerHTML);
    var nValorMaximo = nValorAut  - nRetencao;
    if (new Number(oTextObj.value) > nValorMaximo.toFixed(2) || new Number(oTextObj.value) <= 0) {
      oTextObj.value  = nValorMaximo;
    }
  }

  function js_liberaBotoes(lLiberar) {

    if (lLiberar) {

      $('pesquisar').disabled = false;
      $('atualizar').disabled = false;

    } else {

      $('pesquisar').disabled = true;
      $('atualizar').disabled   = true;

    }
  }

  function js_getSaldos(objSelect,tipo, movimento) {

    if (objSelect.value != 0) {

      var dtBase = $F('e42_dtpagamento');
      if ($F('e42_dtpagamento') == '') {
        dtBase = sDataDia;
        $('e42_dtpagamento').focus();
      }
      if ($('descrConta').innerHTML == objSelect.options[objSelect.selectedIndex].innerHTML) {
        return false;
      }
      js_divCarregando("Aguarde, Verificando saldo da conta.","msgBox");
      $('descrConta').innerHTML = objSelect.options[objSelect.selectedIndex].innerHTML;
      var url       = 'emp4_agendaPagamentoRPC.php';
      var sJson = '{"exec":"getSaldos","params":[{"iCodTipo":"'+objSelect.value+'","dtBase":"'+dtBase+'", "movimento":'+movimento+'}]}';
      var oAjax   = new Ajax.Request(
        url,
        {
          method    : 'post',
          parameters: 'json='+sJson,
          onComplete: js_retornoGetSaldos
        }
      );
    }

  }

  function js_getSaldosMov(iCodTipo, sDescricao, movimento) {

    if (iCodTipo != 0) {

      var dtBase = $F('e42_dtpagamento');
      if ($F('e42_dtpagamento') == '') {
        dtBase = sDataDia;
        $('e42_dtpagamento').focus();
      }
      if ($('descrConta').innerHTML == sDescricao) {
        return false;
      }
      js_divCarregando("Aguarde, Verificando saldo da conta.","msgBox");
      $('descrConta').innerHTML = sDescricao;
      var url       = 'emp4_agendaPagamentoRPC.php';
      var sJson = '{"exec":"getSaldos","params":[{"iCodTipo":"'+iCodTipo+'","dtBase":"'+dtBase+'", "movimento":'+movimento+'}]}';
      var oAjax   = new Ajax.Request(
        url,
        {
          method    : 'post',
          parameters: 'json='+sJson,
          onComplete: js_retornoGetSaldos
        }
      );
    }

  }

  function js_retornoGetSaldos(oAjax) {

    js_removeObj("msgBox");
    var oRetorno               = JSON.parse(oAjax.responseText);
    $('saldotesouraria').value = new Number(oRetorno.oSaldoTes.rnvalortesouraria);
    $('totalcheques').value    = new Number(oRetorno.oSaldoTes.rnvalorreservado);
    $('saldoatual').value      = new Number(oRetorno.oSaldoTes.rnsaldofinal).toFixed(2);

  }

  function js_lancarRetencao(iCodNota, iCodOrd, iNumEmp, nValor, iCodMov, nValorRetido){

    var lSession     = "false";
    var dtPagamento  = $F('e42_dtpagamento');
    var dtPagaNota   = $F('dtpagamento'+iCodMov);
    var nValor       = new Number(nValor);
    var nValorRetido = new Number(nValorRetido);
    if (dtPagamento == '' && dtPagaNota == "") {

      alert('Antes de recalcular as retencoes, informe a data de pagamento');
      return false;

    }
    dtPagamento == dtPagaNota == ""?dtPagamento:dtPagaNota;
    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_retencao',
      'emp4_lancaretencoes.php?iNumNota='+iCodNota+'&nValorBase='+(nValor+nValorRetido)+
      '&iNumEmp='+iNumEmp+'&iCodOrd='+iCodOrd+"&lSession="+lSession
      +'&dtPagamento='+dtPagamento+'&iCodMov='+iCodMov+'&callback=true',
      'Lancar Retenções', true);

  }

  function js_atualizaValorRetencao(iCodMov, nValor, iNota, iCodOrdem) {

    $('valorrow'+iCodMov).value     = new Number(js_strToFloat($('valoraut'+iCodMov).innerHTML) - new Number(nValor)).toFixed(2);
    $('retencao'+iCodMov).innerHTML = js_formatar(nValor,'f');
    if (new Number(nValor).valueOf() > 0) {
      $('valorrow'+iCodMov).readOnly = true;
    } else {
      $('valorrow'+iCodMov).readOnly = false;
    }
    db_iframe_retencao.hide();

  }

  function js_setContaPadrao(iCodigoConta) {

    var aItens = gridNotas.getElementsByClass('ctapag');
    var oUltimoSelect = null;
    for (var i = 0; i < aItens.length; i++) {

      if (aItens[i].parentNode.parentNode.childNodes[0].childNodes[0].checked == true) {

        aItens[i].value = $F('e83_codtipo');
        oUltimoSelect = aItens[i];

      }

    }

    if (aItens.length > 0) {
      js_getSaldos(oUltimoSelect, 1);
    }

  }

  function js_setFormaPadrao(iForma) {


    var aItens = gridNotas.getElementsByClass('formapag');
    for (var i = 0; i < aItens.length; i++) {
      if (aItens[i].parentNode.parentNode.childNodes[0].childNodes[0].checked == true) {
        aItens[i].value = $F('e96_codigo');
      }
    }
  }

  function js_reset() {

    $('descrConta').innerHTML      = '';
    $('saldotesouraria').value     = '';
    $('totalcheques').value        = '';
    $('saldoatual').value          = '';

  }

  function js_pesquisaSlip(iCodigoSlip) {
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_slip2',
      'cai3_conslip003.php?slip='+iCodigoSlip,'Consulta Lançamento',true);
  }

  js_init();


  function alterarHistoricoPagamento(codigoMovimento) {

      var ViewHistoricoPagamento = new DBViewComplementoPagamento('ViewHistoricoPagamento', codigoMovimento);
      ViewHistoricoPagamento.abrir();
  }


    function js_pesquisaRetencoes() {
        var sUrl = 'func_retencaotiporec.php?chave_pesquisa_in=+&funcao_js=parent.js_mostra|e21_sequencial';
        js_OpenJanelaIframe('', 'db_iframe_retencaotiporec', sUrl, 'Pesquisa', true, 5, 5, 1500, 500);
    }

    function js_mostra(chave1) {
        $('e21_sequencial').value = chave1;
        js_ProcCod_e21_sequencial('e21_sequencial', 'e21_sequencialdescr');
        db_iframe_retencaotiporec.hide();
    }

    function js_alertaValorSlip(codigoSlip,valorMaximo){

      codigoSlip = codigoSlip.toString();
      let strValorSlip = $('valorSlip_'+codigoSlip).value.replace('.','').replace(",",".");
      let valorSlip = parseFloat(strValorSlip).toFixed(2);

      if(valorSlip > valorMaximo){
        $msgUsuario = "Valor informado para o slip "+codigoSlip;
        $msgUsuario += " não pode ser maior do que o valor original do Slip";
        alert ($msgUsuario);
        $('valorSlip_'+codigoSlip).value = valorMaximo.toString().replace('.',',');
      }

    }

    function js_inicializaMascaraSlipsParciais(){

      let elemets = document.querySelectorAll('[data-mask]');
      for (const el of elemets) {
        switch (el.dataset.mask) {
                case 'money':
                    el.setAttribute('placeholder', '00,00');
                    el.value = (el.value != '') ? js_formatar(el.value, 'f') : '';
                    el.addEventListener('input', e => jsFormataMoeda(e.target));
                    break;
        }
      }
  }


    $('gestao').classList.add('field-size2')
    $('descricao').classList.add('field-size6')
    function js_pesquisaRecurso(lMostraWindow) {
        if (!lMostraWindow && $('gestao').value == '') {
            $("o15_codigo").value = '';
            $("o15_recurso").value = '';
            $('descricao').value = '';
            return
        }

        let param = 'gestao='+ $('gestao').value;
        pesquisaRecurso(param);
    }

    const pesquisaRecurso = (parametroAdicional) => {
        let sUrl = 'func_fontesubrecurso.php?funcao_js=parent.js_preencheRecurso|gestao|o15_recurso|descricao|db_ids_recursos';
        if (parametroAdicional) {
            sUrl += `&${parametroAdicional}`;
        }
        js_OpenJanelaIframe('', 'db_iframe_recurso', sUrl, 'Pesquisa Fonte de Recurso', true);
    };

    function js_preencheRecurso(gestao, recurso, descricao, db_ids_recursos) {
        $('gestao').value = gestao;
        $('o15_recurso').value = recurso;
        $('descricao').value = descricao;
        $('o15_codigo').value = db_ids_recursos;
        db_iframe_recurso.hide();
    }
</script>
