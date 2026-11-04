<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
$clrotulo->label("rh02_salari");
$clrotulo->label("h12_tiporeajuste");
$clrotulo->label("eso39_tipo");
$clrotulo->label("eso39_descricao");
?>

<center>
<form name="form1" id="form1" method="post" action="" lass="container"  style="width: 365px;">

<input type="hidden" id="Th12_tiporeajuste" value="<?php echo $Th12_tiporeajuste; ?>"/>
<input type="hidden" id="Lh12_tiporeajuste" value="<?php echo $Lh12_tiporeajuste; ?>"/>


  <table>
    <?php
    $clformulario_rel_pes = new cl_formulario_rel_pes;
    $clformulario_rel_pes->tipresumo = "Tipo";
    $clformulario_rel_pes->lo1nome = "lotini";
    $clformulario_rel_pes->lo2nome = "lotfim";
    $clformulario_rel_pes->lo3nome = "sellotac";
    $clformulario_rel_pes->tfinome = "tipfil";
    $clformulario_rel_pes->onchpad = true;
    $clformulario_rel_pes->usaregi = true;
    $clformulario_rel_pes->desabam = true;
    $clformulario_rel_pes->gera_form(db_anofolha(),db_mesfolha());
    ?>
    <tr>
      <td align='right'><b>Reajuste:</b></td>
      <td align='left'>
        <?php
        if(!isset($lancar)){
          $lancar = "p";
        }
        $arr_forma = Array('p'=>'Reajusta padrões','f'=>'Atualizar fórmulas');
        db_select("lancar",$arr_forma,true,1,"onchange='js_desabcampos(this.value);'");
        ?>
      </td>
    </tr>
    <tr>
      <td align='right'><b>% reajuste:</b></td>
      <td align='left'> 
        <?php
        db_input('rh02_salari',10, 4, true, 'text', 1, "");
        ?>
      </td>
    </tr>
  </table>
  <fieldset>
    <legend>Dados do Reajuste Salarial eSocial</legend>

    <table border="0" class="form-container">
        <tr>
          <td width="120">
            <label>Data efeitos remuneratórios do reajuste <br>/ Data assinatura acordo </label>
          </td>
          <td>
            <input type="date" name="eso39_dataefeito" id="idDataEfeito" />
          </td>
        </tr>
        <tr>
          <td align="right" nowrap title="<?=$Teso39_tipo?>"><?=$Leso39_tipo?></td>
          <td align="left" nowrap>
            <?php
		  	       $matriz = array("T"=>"TODOS","f"=>"NAO","t"=>"SIM");
               $matriz = [""  => "Selecione...",
                          "A" => "A - Acordo Coletivo de Trabalho",
                          "B" => "B - Legislação federal, estadual, municipal ou distrital",
                          "C" => "C - Convenção Coletiva de Trabalho",
                          "D" => "D - Sentença normativa - Dissídio",
                          "E" => "E - Conversão de licença saúde em acidente de trabalho",
                          "F" => "F - Outras verbas de natureza salarial ou não salarial devidas após o desligamento",
                          "G" => "G - Antecipação de diferenças de acordo, convenção ou dissídio coletivo",
                          "H" => "H - Recolhimento mensal de FGTS anterior ao início de obrigatoriedade dos eventos periódicos<",
                          "I" => "I - Sentença judicial (exceto reclamatória trabalhista)",
                          "J" => "J - Parcelas complementares conhecidas após o fechamento da folha"
                ];
				       db_select("eso39_tipo",$matriz,true,1,"style='width : 250px;'");
				    ?>
            </td>
        </tr>
          <tr colspan="2">
            <td colspan="5">
              <fieldset>
                <legend><b>Descrição da alteração ou do instrumento que a gerou:</b></legend>
                <?php db_textarea('eso39_descricao', 3, 93, $Ieso39_descricao, true, 'text',1,"","","","255") ?>
              </fieldset>
          </td>
        </tr>
      </table>
  </fieldset>
<input name="incluir" type="submit" id="db_opcao" value="Processar dados" onblur="js_setfocus(true);" onclick="return js_enviar();">

</form>
</center>
<script>
function js_desabcampos(valor){
  if(valor == "p"){
    document.form1.rh02_salari.readOnly = false;
    document.form1.rh02_salari.style.backgroundColor = "";
  }else{
    document.form1.rh02_salari.value = "";
    document.form1.rh02_salari.readOnly = true;
    document.form1.rh02_salari.style.backgroundColor = "#DEB887";
  }
  js_setfocus(false);
}
function js_enviar(){

  if(document.form1.lancar.value == "p"){
    if(document.form1.rh02_salari.value == ""){
      alert("Informe o percentual de reajuste!");
      document.form1.rh02_salari.focus();
      return false;
    }
  }else if(document.form1.selmatri){
    js_seleciona_combo(document.form1.selmatri);
  }else if(document.form1.sellotac){
    js_seleciona_combo(document.form1.sellotac);
  }
  
  const instrumentoPermitido = ['A', 'B', 'C', 'D', 'E'];

  if (!$F('eso39_tipo') || !$F('idDataEfeito')) {
    alert(!$F('eso39_tipo') ? 'Tipo de Instrumento é obrigatório.' : 'Data efeitos remuneratórios do reajuste é obrigatória.');
    return false;
  }

  if (!$F('eso39_descricao') && (instrumentoPermitido.includes($F('eso39_tipo')))) {
    alert('Descrição da alteração é obrigatorio.');
    return false;
  }

  js_validaReajustePadrao();

  return false;
}
function js_setfocus(acao){
  if(document.form1.matini){
    js_tabulacaoforms("form1","matini",acao,1,"matini",acao);
  }else if(document.form1.lotini){
    js_tabulacaoforms("form1","lotini",acao,1,"lotini",acao);
  }else if(document.form1.rh01_regist){
    js_tabulacaoforms("form1","rh01_regist",acao,1,"rh01_regist",acao);
  }else if(document.form1.r70_estrut){
    js_tabulacaoforms("form1","r70_estrut",acao,1,"r70_estrut",acao);
  }else if(document.form1.tipfil){
    js_tabulacaoforms("form1","tipfil",acao,1,"tipfil",acao);
  }else{
    js_tabulacaoforms("form1","tipres",acao,1,"tipres",acao);
  }
}
js_desabcampos(document.form1.lancar.value);

function js_validaReajustePadrao() {

}

(function() {

  var sUrlRpc = 'pes1_reajustepad.RPC.php';


  var oTr = document.createElement("tr");

  var oTdLabel = document.createElement("td");
      oTdLabel.setAttribute("nowrap", true);
      oTdLabel.setAttribute("align", "right");
      oTdLabel.setAttribute("title", $F("Th12_tiporeajuste"));

  var oTdCampo = document.createElement("td");
      oTdCampo.setAttribute("align", "left");

  var oStrong = document.createElement("strong");
      oStrong.innerHTML = $F("Lh12_tiporeajuste");

  var oTdCampo = document.createElement("td");

  var oCampoTipoReajuste = document.createElement("SELECT");
      oCampoTipoReajuste.setAttribute("name", "h12_tiporeajuste");

  var oOptions = {
    "0": "----------",
    "1": "REAL",
    "2": "PARIDADE"
  }

  for (iIndex in oOptions) {
    var oOption = document.createElement("option")
        oOption.setAttribute("value", iIndex);
        oOption.innerHTML = oOptions[iIndex];

    oCampoTipoReajuste.add(oOption);
  }

  oTr.appendChild(oTdLabel);
  oTr.appendChild(oTdCampo);
  oTdLabel.appendChild(oStrong);
  oTdCampo.appendChild(oCampoTipoReajuste);

  var oNext = $$("form table tr")[0].next()
  oNext.parentNode.insertBefore(oTr, oNext);

  /**
   * Reescreve a função para centralizar o código dentro do autocast
   */
  js_validaReajustePadrao = function() {

    //js_divCarregando('Gerando Reajuste...', 'carregando_reajuste', true);

    var oParam = {
      oDados    : $("form1").serialize(true),
      sExecucao : "validar"
    }

    var oDadosExec = {
      method       : 'POST',
      asynchronous : false,
      parameters   : 'json='+Object.toJSON(oParam),
      onComplete   : function(oAjax) {

        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.iStatus == "2") {
          alert(oRetorno.sMessage.urlDecode());

          /**
           * Remove DBDownload caso ja exista.
           */
          if( $('window01') ){
            $('window01').outerHTML = '';
          }

          var oDownload = new DBDownload();
          oDownload.addGroups("pdf", "Relatório de Inconsistências");
          oDownload.addFile(oRetorno.sArquivo.urlDecode(), oRetorno.sNomeArquivo.urlDecode(), "pdf");
          oDownload.show();
          js_removeObj('carregando_reajuste');
          return false;
        }

        var oIncluir = document.createElement("input");
            oIncluir.setAttribute("type", "hidden");
            oIncluir.setAttribute("name", "incluir");
        $("form1").appendChild(oIncluir);
        $("form1").submit();
      }
    }
    new Ajax.Request(sUrlRpc, oDadosExec);
  }

})();


</script>
