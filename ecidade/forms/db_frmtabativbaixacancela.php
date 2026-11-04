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

include(modification("dbforms/db_classesgenericas.php"));
$cliframe_seleciona = new cl_iframe_seleciona;
$cltabativbaixa->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("q07_inscr");
$clrotulo->label("z01_nome");

if (!isset($veiculo)) {
  $veiculo = false;
}

?>
<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<form name="form1" method="post" action="">
<center>
<fieldset style="margin-top: 20px;">
<legend>Excluir Baixa Inscrição de Alvará</legend>

<table border="0">
  <tr>
    <td align="center">
    <?php
      db_input('calculo',5,0,true,'hidden',1);
      $q02_dtbaix_dia="";
      $q02_dtbaix_mes="";
      $q02_dtbaix_ano="";
      db_inputdata('q07_datafi',@$q07_datafi_dia,@$q07_datafi_mes,@$q07_datafi_ano,true,'hidden',3);
      db_inputdata('q07_databx',@$q07_databx_dia,@$q07_databx_mes,@$q07_databx_ano,true,'hidden',3);
      db_inputdata('q02_dtbaix',@$q02_dtbaix_dia,@$q02_dtbaix_mes,@$q02_dtbaix_ano,true,'hidden',3);
    ?>
    <table border="0">
      <tr>
        <td title="<?=$Tq07_inscr?>" >
        <?php
         db_ancora($Lq07_inscr,' js_inscr(true); ',1);
        ?>
        </td>
        <td title="<?=$Tq07_inscr?>" colspan="4">
        <?php
          db_input('q07_inscr',5,$Iq07_inscr,true,'text',1,"onchange='js_inscr(false)'");
          isset($q07_inscr)?$inscricao=$q07_inscr:"";
          db_input('inscricao',5,$Iq07_inscr,true,'hidden',1);
          db_input('veiculo',5,$veiculo,true,'hidden',1);
          db_input('z01_nome',50,0,true,'text',3);
        ?>
        </td>
      </tr>
      <tr>
        <td colspan="3" align="center">

        </td>
      </tr>
    </table>
</td>
</tr>
<tr>
<td>
  <tr>
    <td align="center" colspan="2">
    <?php
          $cliframe_seleciona->campos  = "q07_inscr,q07_seq,q88_inscr,q03_descr,q07_datain,q07_datafi,q07_databx,q07_perman,q07_quant,q11_tipcalc, q81_descr";
          $cliframe_seleciona->legenda="ATIVIDADES BAIXADAS";
          if(isset($q07_inscr) && $q07_inscr!=""){
             $cliframe_seleciona->sql=$cltabativ->sql_query_atividade_inscr($q07_inscr,"*","q07_seq","q07_inscr = $q07_inscr and q07_databx is  not null");
          }
          $cliframe_seleciona->textocabec ="darkblue";
          $cliframe_seleciona->textocorpo ="black";
          $cliframe_seleciona->fundocabec ="#aacccc";
          $cliframe_seleciona->fundocorpo ="#ccddcc";
          $cliframe_seleciona->iframe_height ="250";
          $cliframe_seleciona->iframe_width ="700";
          $cliframe_seleciona->iframe_nome ="atividades";
          $cliframe_seleciona->chaves ="q07_inscr,q07_seq";
          $cliframe_seleciona->iframe_seleciona($db_opcao);
    ?>
    </td>
  </tr>
  </table>
  </fieldset>
    <input type="submit" style="margin-top: 10px;" id="cancelar" value="Cancelar baixa" <?=($db_botao==false?"disabled":"")?> >
  </center>
</form>
<div class="container" id="container">
    <div class="alert alert-primary text-left" role="alert">
        Clicar em <kbd>Sim</kbd> irá excluir a baixa e recalcular as atividades reativadas.
        <br>
        Clicar em <kbd>Não</kbd> irá excluir a baixa, mas não irá recalcular as atividades reativadas.
    </div>
    <input type="button" value="Sim" style="width: 43px; font-weight: bold;" onclick="recalculate(1)">
    <input type="button" value="Não" style="width: 43px; font-weight: bold;" onclick="recalculate(2)">
</div>
<script>
function js_inscr(mostra){
  var inscr=document.form1.q07_inscr.value;

  let sVeiculo = '';

  if (!!document.form1.veiculo.value) {
    sVeiculo = '&veiculo=true';
  }

  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr',`func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome${sVeiculo}`,'Pesquisa',true);
  }else{
    if(inscr!=""){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr',`func_issbase.php?pesquisa_chave=${inscr}&funcao_js=parent.js_mostrainscr1${sVeiculo}`,'Pesquisa',false);
    }else{
      document.form1.z01_nome.value="";
      document.form1.submit();
    }
  }
}
function js_mostrainscr(chave1,chave2){
  document.form1.q07_inscr.value = chave1;
  document.form1.z01_nome.value = chave2;
  atividades.location.href="iss1_tabativbaixaiframe.php?q07_inscr="+chave1+"&z01_nome="+chave2;
  document.form1.submit();
  db_iframe_inscr.hide();
}
function js_mostrainscr1(chave,erro){
  document.form1.z01_nome.value = chave;
  if(erro==true){
    document.form1.q07_inscr.focus();
    document.form1.q07_inscr.value = '';
  }else{
    document.form1.submit();
  }
}

document.getElementById("cancelar").addEventListener("click", (oEvent) => {
    oEvent.preventDefault();
    js_verifica();
});

const windowMessage = new windowAux('windowMessage', 'Recalcular as atividades reativadas?', 550, 170);
windowMessage.setContent(document.getElementById("container"));

const btn = document.getElementById("windowwindowMessage_btnclose");
btn.parentNode.removeChild(btn);

function js_verifica(){
    const q07_inscr = document.getElementById("q07_inscr").value;

    if (q07_inscr == "" || q07_inscr == '0'|| isNaN(q07_inscr) == true) {
        alert('Verifique a inscrição');
        return false;
    }

    const inscricao = document.getElementById("inscricao").value;

    if(q07_inscr != inscricao){
        return false;
    }

    const aCheckbox = atividades.document.querySelectorAll("input[type='checkbox']:checked");

    if (aCheckbox.length == 0) {
        alert('Selecione uma atividade!');
        return false;
    }

    windowMessage.show(0, 0, true);
}

const fechaModal = modal => {
    if (!!modal.oDBMask) {
        modal.oDBMask.destroy();
    }

    modal.hide();
};

function recalculate(buttonNumber) {
    if (buttonNumber == 1) {
        document.form1.calculo.value="ok";
    } else {
        document.form1.calculo.value="no";
    }

    fechaModal(windowMessage);

    js_gera_chaves();

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "cancelar";

    document.form1.appendChild(input);
    document.form1.submit();
}
</script>
