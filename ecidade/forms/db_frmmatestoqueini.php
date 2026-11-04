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
//MODULO: material

use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use ECidade\Saude\Farmacia\Repositories\TipoMovimentacaoBnafarRepository;

require_once(modification("classes/db_matmater_classe.php"));
require_once(modification("classes/db_matmaterunisai_classe.php"));
require_once(modification("classes/db_matestoqueitemnotafiscalmanual_classe.php"));

$clmatmater = new cl_matmater;
$clmatmaterunisai = new cl_matmaterunisai;
$clmatestoqueini->rotulo->label();
$oDaoMatEstoqueItemNotaFiscal = new cl_matestoqueitemnotafiscalmanual();
$oDaoMatEstoqueItemNotaFiscal->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("m60_codmater");
$clrotulo->label("m60_descr");
$clrotulo->label("m61_descr");
$clrotulo->label("m70_codigo");
$clrotulo->label("m71_codlanc");
$clrotulo->label("m71_quant");
$clrotulo->label("m71_valor");
$clrotulo->label("m77_lote");
$clrotulo->label("m77_dtvalidade");
$clrotulo->label("m78_matfabricante");
$clrotulo->label("m76_nome");
$clrotulo->label("coddepto");
$clrotulo->label("descrdepto");
$clrotulo->label("m66_codcon");

$tranca = 1;
$m60_controlavalidade = 3;

if($db_opcao==2 || $db_opcao==22 || $db_opcao==3 || $db_opcao==33){
  $tranca = 3;
}

if (isset($m60_codmater) && trim($m60_codmater) != "" && ( USE_PCASP && db_getsession("DB_anousu") > 2012) ) {

  $oDaoMaterialEstoqueGrupo = new cl_matmatermaterialestoquegrupo;
  $sWhere                   = "    m68_matmater = {$m60_codmater} ";
  $sWhere                  .= "and m60_ativo    = true ";
  $sSqlValidaContaContabil  = $oDaoMaterialEstoqueGrupo->sql_query_grupo_conta(null, "m66_codcon", null, $sWhere);
  $rsValidaContaContabil    = $oDaoMaterialEstoqueGrupo->sql_record($sSqlValidaContaContabil);

  if ($oDaoMaterialEstoqueGrupo->numrows == 0) {

    $sMsgErro  = 'Material '.$m60_codmater.' sem vínculo com grupo/subgrupo. \n';
    $sMsgErro .= 'Para vincular acesse o menu: Cadastro > Cadastro de Material > Alteracao';
    db_msgbox($sMsgErro);

    $iRedirecionaMenu = 1;
    switch ($db_opcao) {

      case 2:
      case 22:
        $iRedirecionaMenu = 2;
      case 3:
      case 33:
        $iRedirecionaMenu = 3;
    }
    db_redireciona("mat1_matestoqueini00{$iRedirecionaMenu}.php");
    return true;
  }

  $m66_codcon = db_utils::fieldsMemory($rsValidaContaContabil, 0)->m66_codcon;
}

if (isset($coddepto) && $coddepto != "") {
    $daoDeposito = new cl_db_almox();
    $sqlDeposito = $daoDeposito->sql_query_file(null, 'm91_codigo', null, " m91_depto = {$coddepto}");
    $rsDeposito = db_query($sqlDeposito);
    if ($rsDeposito) {
        $m91_codigo = pg_fetch_array($rsDeposito)['m91_codigo'];
    }
}

$title = "Implantação de Estoque";
if (isset($entrada) && $entrada == 'true') {
    $title = "Entrada Manual";
}
?>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<?php require_once("mat_aviso_deposito.php"); ?>
<div class="container" <?= $isDeposito ?: 'style="display: none"' ?>>
    <form name="form1" method="post" action="">
        <table class="form-container">
            <tr>
                <td>
                    <fieldset>
                        <legend><b><?=$title?></b></legend>
                        <table border='0'>
                            <tr>
                                <td nowrap title="<?= @$Tcoddepto ?>">
                                    <label for="m91_codigo">Depósito:</label>
                                </td>
                                <td colspan="3">
                                    <?php
                                    db_input('m91_codigo', 10, $Icoddepto, true, 'text', 3);
                                    db_input('descrdepto', 40, $Idescrdepto, true, 'text', 3);
                                    db_input('m66_codcon', 10, $Im66_codcon, true, 'hidden', 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?= @$Tm60_codmater ?>">
                                    <?php
                                    db_ancora(@$Lm60_codmater, "js_pesquisam60_codmater(true);", $tranca);
                                    ?>
                                </td>
                                <td colspan="3">
                                    <?php
            db_input('m60_codmater',10,$Im60_codmater,true,'text',$tranca," onchange='js_pesquisam60_codmater(false);'");
            db_input('m60_descr',40,$Im60_descr,true,'text',3,'');
          ?>
        </td>
      </tr>
    <?php if (FarmaciaHelper::utilizaIntegracaoBnafar()): ?>
      <tr>
            <td nowrap title="Tipo de Entrada"><b>Tipo:</b></td>
            <td>

              <?php
                $tipoMovimentacaoRepository = new TipoMovimentacaoBnafarRepository();
                $tipos = $tipoMovimentacaoRepository->scopeEntrada()->get();
                $oTipo = [];
                $oTipo[''] = 'Selecione...';
                foreach ($tipos as $tipo) {
                    $oTipo[$tipo->getCodigo()] = $tipo->getDescricao();
                }
                db_select("cod_tipo",$oTipo,true,$db_opcao,"onchange='js_verificatipo(this);'");
              ?>
            </td>
          </tr>
        </table>
        <fieldset id="fieldset-fornecedor"style="display: none;">
          <legend>Fornecedor/Distribuidor</legend>
          <table>
              <tr id="unidadedoador" style="display:none;">
                  <td nowrap title="Unidade Origem">
                      <?php
                      db_ancora("<b>Unidade Origem:</b>","js_pesquisaunidadedoador(true)",$db_opcao);
                      ?>
                  </td>
                  <td colspan="3">
                      <?php
                      db_input('unidade',10,null,true,'text',$db_opcao,"onchange='js_pesquisaunidadedoador(false);'");
                      db_input('unidadedescr',40, null ,true,'text',3,'');
                      ?>
                  </td>
              </tr>
              <tr id="cgmdoador" style="display:none;">
                  <td nowrap title="CGM Origem">
                      <?php
                      db_ancora("<b>CGM Origem:</b>","js_pesquisacgmdoador(true)",$db_opcao);
                      ?>
                  </td>
                  <td colspan="3">
                      <?php
                      db_input('cgm',10,null,true,'text',$db_opcao,"onchange='js_pesquisacgmdoador(false);'");
                      db_input('descr',40, null ,true,'text',3,'');
                      ?>
                  </td>
              </tr>
          </table>
        </fieldset>
        <table>
        <?php
          endif;

          if (isset($m60_codmater) && trim($m60_codmater) != "" && $db_opcao != 3 && $db_opcao != 33) {
            $result = $clmatmater->sql_record($clmatmater->sql_query($m60_codmater,"m61_descr,m60_controlavalidade"));
            if ($clmatmater->numrows>0){
              db_fieldsmemory($result,0);
            }
            $result_unisai = $clmatmaterunisai->sql_record($clmatmaterunisai->sql_query($m60_codmater,null,"matunid.m61_descr as m61_descrsai"));
            if ($clmatmaterunisai->numrows>0){
              db_fieldsmemory($result_unisai,0);
            }
        ?>
      <tr>
        <td nowrap title="Unid. Entrada:"><strong>Unid. Entrada:</strong></td>
        <td>
          <?php
            db_input('m61_descr',20,$Im61_descr,true,'text',3)
          ?>
        </td>
        <td nowrap title="Unid. Saída" align="right"><strong>Unid. Saída:</strong></td>
        <td align="right">
          <?php
            db_input('m61_descrsai',20,$Im61_descr,true,'text',3)
          ?>
        </td>
      </tr>
      <?php
          }
      $colspan = 3;
      $onchange= "";
      if(isset($m80_codigo) && trim($m80_codigo)!="" && $db_opcao==2){
        $colspan = 1;
        $onchange= "js_verificaquant(this.value);";
      }
      ?>
<tr>
        <td nowrap title="<?=@$Tm71_quant?>">
<?=@$Lm71_quant?>
</td>
        <td nowrap colspan="<?=$colspan?>">
<?php
db_input('m71_quant',10,$Im71_quant,true,'text',$tranca,"onchange='js_calculavalortotal(this.value,\"quant\");$onchange'")
?>
</td>
<?php
if(isset($m80_codigo) && trim($m80_codigo)!="" && $db_opcao==2){
  echo "
  <td nowrap title='Quantidade já solicitada' align='right'>
  <strong>Qtd. já solicitada:</strong>
  </td>
  <td align='right'>";
  // if(isset($m71_quantatend) && isset($m70_quant)){
    if(isset($m71_quantatend)){
      // $quantrest = ($m71_quant-$m71_quantatend)." / ".$m70_quant;
      // $quantrest = ($m71_quant-$m71_quantatend);
      $quantrest = ($m71_quantatend);
    }
    db_input('quantrest',10,$Im71_quant,true,'text',3);
    echo "
    </td>
    ";
  }
  ?>
  </tr>
  <?php
  if(isset($m60_codmater) && trim($m60_codmater)!="" && $db_opcao!=3 && $db_opcao!=33){
    $result_transmater = $cltransmater->sql_record($cltransmater->sql_query_file(null,"distinct m63_codpcmater",null,"m63_codmatmater=$m60_codmater"));
    if($cltransmater->numrows>0){
      db_fieldsmemory($result_transmater,0);
    }
    if(isset($m63_codpcmater) && trim($m63_codpcmater) == "") {
      $mostrar = 'ok';
      echo '
      <tr>
      <td nowrap title="Média de valor deste material no empenho">
      <strong>Média de valor no empenho:</strong>
      </td>
      <td colspan="3">
      ';
      $media = 3;
      db_input('media',10,$Im71_quant,true,'text',$tranca);
      echo '
      <input name="calcular" type="button" id="calcula" value="Calcular média" onclick="js_calcularmedia();" onchange="js_calcularmedia();">
      </td>
      </tr>
      ';

      $result_valoritens = $clempempitem->sql_record($clempempitem->sql_query_file(null,null,"e62_vltot/e62_quant as valorunit","e62_numemp desc","e62_item=$m63_codpcmater"));
    }else{
      //$m71_valorunit = "";
      //$m71_valor = "";
    }
  }
  ?>
  <tr>
        <td nowrap title="Valor unitário do item"><strong>Valor unitário do item:</strong></td>
        <td>
  <?php
  db_input('m71_valorunit',10,$Im71_valor,true,'text',$tranca,"onchange='js_calculavalortotal(this.value,\"unit\");'")
  ?>
  </td>
        <td nowrap title="Valor total" align="right"><strong>Valor total:</strong></td>
        <td align="left">
  <?php
  db_input('m71_valor',10,$Im71_valor,true,'text',$tranca,"onchange='js_calculavalortotal(this.value,\"total\");'")
  ?>
  </td>
      </tr>
      <tr>
        <td><b>Lote:</b></td>
        <td>
      <?php db_input('m77_lote',10,$Im77_lote,true,'text',$tranca);?>
    </td>
        <td align="right"><b>Validade:</b></td>
        <td>
      <?php

      if (!isset($m77_dtvalidade)) {
        $m77_dtvalidade_dia = "";
        $m77_dtvalidade_mes = "";
        $m77_dtvalidade_ano = "";
      }
      db_inputdata('m77_dtvalidade',$m77_dtvalidade_dia,$m77_dtvalidade_mes,$m77_dtvalidade_ano,true,'text',$tranca);?>
       </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Tm78_matfabricante?>">
        <?php
        db_ancora(@$Lm78_matfabricante,"js_pesquisam78_matfabricante(true);",$tranca);
        ?>
       </td>
       <td colspan="3">
       <?php
       db_input('m78_matfabricante',10,$Im78_matfabricante,true,'text',$tranca," onchange='js_pesquisam78_matfabricante(false);'");
       db_input('m76_nome',40,$Im76_nome,true,'text',3,'')
       ?>
    </td>
  <tr>
    <td nowrap title="<?=@$Tm79_notafiscal?>"><b><?=@$Lm79_notafiscal?></b></td>
    <td>
      <?php
        db_input("m79_sequencial", 10, null, true, 'hidden', 3);
        db_input("m79_notafiscal", 10, $Im79_notafiscal, true, 'text', $db_opcao, '');
      ?>
    </td>
    <td nowrap title="<?=@$Tm79_data?>" align="right"><b><?=@$Lm79_data?></b></td>
    <td>
      <?php
        if (!isset($m79_data)) {
          $m79_data_dia = "";
          $m79_data_mes = "";
          $m79_data_ano = "";
        }
        db_inputdata("m79_data", $m79_data_dia, $m79_data_mes, $m79_data_ano, true, 'text', $db_opcao);
      ?>
    </td>
  </tr>
  </tr>
      <tr>
        <td nowrap title="<?=@$Tm80_obs?>" colspan="4">
          <fieldset>
            <legend><b><?=@$Lm80_obs?></b></legend>
            <?php
              if($db_opcao==3 || $db_opcao==33){
                $m80_obs = "";
              }
              db_textarea('m80_obs',4,70,$Im80_obs,true,'text',1,"");
            ?>
          </fieldset>
        </td>
      </tr>
    </table>
    </fieldset>
    </td>
  </tr>
</table>

<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit"
  id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
  <?=($db_botao==false?"disabled":"")?> onclick="return js_controlavalidadade()"> <input name="pesquisar" type="button"
  id="pesquisar" value="Pesquisar" onclick="js_pesquisainicio();">
  <?php
  db_input('m60_controlavalidade',10,$Im80_codigo,true,'hidden',3);
  if(isset($m80_codigo) && trim($m80_codigo)!=""){
    db_input('m80_codigo',10,$Im80_codigo,true,'hidden',3);
    db_input('m70_codigo',10,$Im70_codigo,true,'hidden',3);
    db_input('m71_codlanc',10,$Im71_codlanc,true,'hidden',3);
  }
  $m80_codtipo = "1";
  $naoinill = "1,14" ;
  if(isset($entrada) && $entrada == true){
    $m80_codtipo = "3";
    $naoinill = "3,15" ;
  }
  db_input('m80_codtipo',10,$Im80_codtipo,true,'hidden',3);
  ?>
    </form>
</div>
<script>

    function js_calculavalortotal(valor, opcao) {
        if (document.form1.m71_quant.value != "") {
            if (opcao != "quant") {
                pos = valor.indexOf('.');
                if (pos != -1) {
                    tam = valor.length;
                    qts = valor.slice((pos + 1), tam);
                    dec = qts.length;
                    if (dec == 1) {
            dec   = 2;
          }
        }else{
          dec = 2;
        }
      }
      if(opcao=="unit"){
        valor = new Number(valor);
        quant = new Number(document.form1.m71_quant.value);
        VALOR = valor*quant;
        document.form1.m71_valorunit.value = valor.toFixed(dec);
        document.form1.m71_valor.value     = VALOR.toFixed(2);
      }else if(opcao=="total"){
        valor = new Number(valor);
        quant = new Number(document.form1.m71_quant.value);
        VALOR = valor/quant;
        document.form1.m71_valorunit.value = VALOR.toFixed(dec);
        document.form1.m71_valor.value     = valor.toFixed(2);
      }else if(opcao=="quant"){
        if(document.form1.m71_valorunit.value!=""){
          val = document.form1.m71_valorunit.value;
          pos = val.indexOf('.');
          if(pos!=-1){
            tam = val.length;
            qts = val.slice((pos+1),tam);
            dec = qts.length;
            if(dec==1){
              dec   = 2;
            }
          }else{
            dec = 2;
          }
          quant = new Number(valor);
          valor = new Number(document.form1.m71_valorunit.value);
          VALOR = valor*quant;
          document.form1.m71_valor.value = VALOR.toFixed(2);
        }
      }
    }
  }
  function js_pesquisam60_codmater(mostra) {

    if(mostra==true){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matmater','func_matmater.php?funcao_js=parent.js_mostramatmater1|m60_codmater|m60_descr','Pesquisa',true);
    }else{
      if(document.form1.m60_codmater.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matmater','func_matmater.php?pesquisa_chave='+document.form1.m60_codmater.value+'&funcao_js=parent.js_mostramatmater','Pesquisa',false);
      }else{
        document.form1.m60_descr.value = '';
        document.form1.submit();
      }
    }
  }
  function js_mostramatmater(chave,erro) {
    document.form1.m60_descr.value = chave;
    if(erro==true){
      document.form1.m60_codmater.focus();
      document.form1.m60_codmater.value = '';
    }else{
      document.form1.submit();
    }
  }

  function js_mostramatmater1(chave1, chave2) {
      document.form1.m60_codmater.value = chave1;
      document.form1.m60_descr.value = chave2;
      db_iframe_matmater.hide();
      document.form1.submit();
  }

  function js_mostradepart(chave, erro) {
      document.form1.descrdepto.value = chave;
      if (erro == true) {
          document.form1.m91_codigo.focus();
          document.form1.m91_codigo.value = '';
      }
  }

  function js_mostradepart1(chave1, chave2) {
      document.form1.m91_codigo.value = chave1;
      document.form1.descrdepto.value = chave2;
      db_iframe_depart.hide();
  }

  function js_pesquisainicio() {
      qry = "&chave_m80_codtipo=<?=$naoinill?>";
    qry += "&chave_m80_coddepto=<?=db_getsession("DB_coddepto")?>";
    qry += "&naoinill=<?=($naoinill)?>";
    <?php
    if($db_opcao!=3 && $db_opcao!=33){
      echo "qry += '&naoatendido=true';";
    }
    ?>
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matestoqueini','func_matestoqueini.php?funcao_js=parent.js_preenchepesquisa|m80_codigo'+qry,'Pesquisa',true);
  }
  function js_preenchepesquisa(chave){
    db_iframe_matestoqueini.hide();
    qry = "";
    <?php
    if((isset($entrada) && $entrada == true) || $m80_codtipo==3 || $m80_codtipo==15 ){
      echo "qry = '&entrada=true';\n";
    }
    ?>
    <?php
    if($db_opcao!=1){
      echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave+qry";
    }
    ?>
  }
  <?php
  if(isset($m71_valor) && trim($m71_valor)!="" && isset($m71_quant) && trim($m71_quant)!=""){
    echo "
    js_calculavalortotal('$m71_valor','total');
    ";
  }
  ?>
  function js_pesquisaimplanta(){
    qry = "&chave_m80_codtipo=<?=$m80_codtipo?>";
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matestoqueini','func_matestoqueini.php?funcao_js=parent.js_preenchepesquisa|m60_codmater|m80_codigo'+qry,'Pesquisa',true);
  }
  function js_verificaquant(valor){
    //  splitar = document.form1.quantrest.value.split(" / ");
    valor = new Number(valor);
    //  soli  = new Number(splitar[0]);
    //  rest  = new Number(splitar[1]);
    soli  = 0;
    rest  = new Number(document.form1.quantrest.value);
    erro  = 0;
    if(valor <= 0){
      alert("Usuário:\n\nA quantidade deve ser superior à informada.\n\nAdministrador:");
      erro++;
    }else if(valor<rest){
      alert("Usuário:\n\nA quantidade informada não deve ser menor que a quantidade restante.\n\nAdministrador:");
      erro++;
    }else if(valor<soli){
      alert("Usuário:\n\nA quantidade informada não deve ser menor que a quantidade solicitada.\n\nAdministrador:");
      erro++;
    }
    if(erro>0){
      document.form1.m71_valor.value = "";
      document.form1.m71_valorunit.value = "";
      document.form1.m71_quant.value = "";
      document.form1.m71_quant.focus();
    }
  }
  <?php
  if(isset($mostrar)){
    echo '
    function js_calcularmedia(){
      arr_valores = new Array();
      ';
      $index = 1;
      $valortotal = 0;
      for($i=0;$i<$clempempitem->numrows;$i++){
        db_fieldsmemory($result_valoritens,$i);
        $valortotal += $valorunit;
        $valormedia  = $valortotal/$index;
        echo '
        arr_valores['.$index.'] = new Number('.$valormedia.');
        ';
        $index++;
      }
      echo '
      indexmaximo = new Number('.($clempempitem->numrows).');
      valormedia  = new Number(document.form1.media.value);
      if(document.form1.m71_quant.value!=""){
        quantidade  = new Number(document.form1.m71_quant.value);
      }else{
        quantidade  = new Number(1);
      }
      if(document.form1.media.value!=""){
        if(valormedia<=indexmaximo){
          valor = new Number(arr_valores[valormedia]);
          valvezesqtd = new Number(quantidade*valor);
        }else{
          valor = new Number(arr_valores['.($index-1).']);
          valvezesqtd = new Number(quantidade*valor);
          document.form1.media.value = '.($index-1).';
          alert("Quantidade máxima de empenhos com este item: '.($index-1).'");
        }

        document.form1.m71_valor.value = valvezesqtd.toFixed(2);
        document.form1.m71_valorunit.value = valor.toFixed(2);

      }else{
        document.form1.m71_valorunit.value = "";
        document.form1.m71_valor.value = "";
      }
    }';
    if ($db_opcao == 2 || $db_opcao == 22) {
    } else {
      echo 'js_calcularmedia();';
    }
  }

  ?>
  function js_controlavalidadade() {
    if ($F("m80_obs") == "") {

      alert("Necessário preenchimento da observação");
      return false;
    }

    iControleValidade = $F('m60_controlavalidade');
    if (iControleValidade == 1 || iControleValidade == 2) {

      if ($F('m77_lote') == '' || $F('m77_dtvalidade') == '') {

        if (!confirm('Não foi informado o lote/data de validade do item.\nDeseja Prosseguir?')) {
          return false;
        } else {
          return true;
        }
      }
    }
   }
  function js_pesquisam78_matfabricante(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matfabricante','func_matfabricante.php?funcao_js=parent.js_mostramatfabricante1|m76_sequencial|m76_nome','Pesquisa',true);
  }else{
     if(document.form1.m78_matfabricante.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matfabricante','func_matfabricante.php?pesquisa_chave='+document.form1.m78_matfabricante.value+'&funcao_js=parent.js_mostramatfabricante','Pesquisa',false);
     }else{
       document.form1.m76_nome.value = '';
     }
  }
}
function js_mostramatfabricante(chave,erro){
  document.form1.m76_nome.value = chave;
  if(erro==true){
    document.form1.m78_matfabricante.focus();
    document.form1.m78_matfabricante.value = '';
  }
}
function js_mostramatfabricante1(chave1,chave2){
  document.form1.m78_matfabricante.value = chave1;
  document.form1.m76_nome.value = chave2;
  db_iframe_matfabricante.hide();
}
  </script>
<?php if (FarmaciaHelper::utilizaIntegracaoBnafar()): ?>
<script type="text/javascript">
  js_verificatipo(document.form1.cod_tipo);
  function js_verificatipo( obj ){
    const tipoMovimentacao = obj.options[obj.selectedIndex].value;

    document.getElementById("fieldset-fornecedor").style.display = "none";
    document.getElementById("unidadedoador").style.display = "none";
    document.getElementById("cgmdoador").style.display = "none";

    if (tipoMovimentacao === '3') {
        document.getElementById("unidade").value = "<?php echo db_getsession("DB_coddepto") ?>";
        document.getElementById("cgm").value = "";
        document.getElementById("descr").value = "";
        return;
    }

    const deParaUnidade = ['4', '6', '7', '8'];
    const deParaCgm = ['1', '4', '5', '6'];
    if (deParaUnidade.includes(tipoMovimentacao)) {
        document.getElementById("fieldset-fornecedor").style.display = "";
        document.getElementById("unidadedoador").style.display = "";
        document.getElementById("cgm").value = "";
        document.getElementById("descr").value = "";
    }
    if (deParaCgm.includes(tipoMovimentacao)) {
        document.getElementById("fieldset-fornecedor").style.display = "";
        document.getElementById("cgmdoador").style.display = ""
        document.getElementById("unidade").value = "";
        document.getElementById("unidadedescr").value = "";
    }

    // Limpa os campos de quem não está sendo exibido
    if (document.getElementById("unidadedoador").style.display == 'none' || document.getElementById("unidadedescr").value == "") {
        document.getElementById("unidade").value = "";
        document.getElementById("unidadedescr").value = "";
    }
    if (document.getElementById("cgmdoador").style.display == 'none') {
        document.getElementById("cgm").value = "";
        document.getElementById("descr").value = "";
    }

    js_ValidaBloqueioCampo(document.form1.cgm, document.form1.unidade.value != "");
    js_ValidaBloqueioCampo(document.form1.unidade, document.form1.cgm.value != "");
  }

  function js_pesquisacgmdoador(mostra){
    if (document.form1.unidade.value.trim() != "") {
      return false;
    }
    if(mostra==true){
      js_OpenJanelaIframe('top.corpo','db_iframe_cgm','func_cgm.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
    }else{
        if(document.form1.cgm.value != ''){
          js_OpenJanelaIframe('top.corpo','db_iframe_cgm','func_cgm.php?pesquisa_chave='+document.form1.cgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
        }else{
          document.form1.descr.value = '';
          js_ValidaBloqueioCampo(document.form1.unidade, false);
        }
    }
  }
  function js_mostracgm(erro,chave){
    document.form1.descr.value = chave;
    if(erro==true){
      document.form1.cgm.focus();
      document.form1.cgm.value = '';
      js_ValidaBloqueioCampo(document.form1.unidade, false);
    } else {
      js_ValidaBloqueioCampo(document.form1.unidade, true);
    }
  }
  function js_mostracgm1(chave1,chave2){
    js_ValidaBloqueioCampo(document.form1.unidade, true);
    document.form1.cgm.value = chave1;
    document.form1.descr.value     = chave2;
    db_iframe_cgm.hide();
  }

  function js_pesquisaunidadedoador(mostra){
    if (document.form1.cgm.value.trim() != "") {
      return false;
    }
    if(mostra==true){
      js_OpenJanelaIframe('top.corpo','db_iframe_unidade','func_unidades.php?funcao_js=parent.js_mostraunidade1|sd02_i_codigo|descrdepto','Pesquisa',true);
    }else{
        if(document.form1.unidade.value != ''){
          js_OpenJanelaIframe('top.corpo','db_iframe_unidade','func_unidades.php?pesquisa_chave='+document.form1.unidade.value+'&funcao_js=parent.js_mostraunidade','Pesquisa',false);
        }else{
          js_ValidaBloqueioCampo(document.form1.cgm, false);
          document.form1.unidadedescr.value = '';
        }
    }
  }
  function js_mostraunidade(chave, erro){
    document.form1.unidadedescr.value = chave;
    if(erro==true){
      document.form1.unidade.focus();
      document.form1.unidade.value = '';
      js_ValidaBloqueioCampo(document.form1.cgm, false);
    } else {
      js_ValidaBloqueioCampo(document.form1.cgm, true);
    }
  }
  function js_mostraunidade1(chave1,chave2){
    js_ValidaBloqueioCampo(document.form1.cgm, true);
    document.form1.unidade.value = chave1;
    document.form1.unidadedescr.value = chave2;
    db_iframe_unidade.hide();
  }
  function js_ValidaBloqueioCampo (oCampo, lBloqueio) {
      if (lBloqueio) {
          oCampo.readOnly = true;
          oCampo.style.background = '#DEB887';
      } else {
          oCampo.readOnly = false;
          oCampo.style.background = '#FFFFFF';
      }
  }
</script>
<?php endif; ?>
