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

//MODULO: educação
$oDaoMatricula->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("ed57_i_codigo");
$clrotulo->label("ed57_i_codigo");
$clrotulo->label("ed31_i_curso");
$clrotulo->label("ed57_i_turno");
$clrotulo->label("ed57_i_calendario");
$clrotulo->label("ed223_i_serie");
$clrotulo->label("ed57_i_numvagas");
$clrotulo->label("ed57_i_nummatr");
$clrotulo->label("ed248_t_obs");
$clrotulo->label("ed248_i_motivo");
$clrotulo->label("ed297_data");
?>
<form name="form1" method="post" action="">
    <center>
        <table border="0" width="100%">
            <tr>
                <td colspan="3">
                    <fieldset style="width:97%">
                        <legend><b>Turma</b></legend>
                        <table border="0">
                            <tr>
                                <td nowrap title="<?php echo @$Ted60_i_turma ?>">
                                    <?php db_ancora(@$Led60_i_turma, "js_pesquisaed60_i_turma();", ''); ?>
                                </td>
                                <td>
                                    <?php db_input('ed60_i_turma', 15, $Ied60_i_turma, true, 'text', 3, '') ?>
                                    <?php db_input('ed57_c_descr', 20, @$Ied57_c_descr, true, 'text', 3, '') ?>
                                    <?php echo @$Led57_i_calendario ?>
                                    <?php db_input('ed52_c_descr', 20, @$Ied52_c_descr, true, 'text', 3, '') ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?php echo @$Ted31_i_curso ?>">
                                    <?php echo @$Led31_i_curso ?>
                                </td>
                                <td>
                                    <?php db_input('ed29_c_descr', 40, @$Ied29_c_descr, true, 'text', 3, '') ?>
                                    <?php echo @$Led223_i_serie ?>
                                    <?php db_input('nometapa', 30, @$Inometapa, true, 'text', 3, '') ?>
                                    <?php echo @$Led57_i_turno ?>
                                    <?php db_input('ed15_c_nome', 20, @$Ied15_c_nome, true, 'text', 3, '') ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?php echo @$Ted31_i_curso ?>">
                                    <?php echo @$Led57_i_numvagas ?>
                                </td>
                                <td>
                                    <?php db_input('ed57_i_numvagas', 10, @$Ied57_i_numvagas, true, 'text', 3, '') ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php echo @$Led57_i_nummatr ?>
                                    <?php db_input('ed57_i_nummatr', 10, @$Ied57_i_nummatr, true, 'text', 3, '') ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>Vagas Disponíveis:</b>
                                    <?php db_input('restantes', 10, @$Irestantes, true, 'text', 3, '') ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <?php //if (isset($chavepesquisa) && $db_opcao == 1) {?>
            <tr>
                <td nowrap title="<?php echo @$Ted60_i_aluno ?>">
                    <?php db_ancora(@$Led60_i_aluno, "js_pesquisaed60_i_aluno(true);", $db_opcao); ?>
                </td>
                <td>
                    <?php db_input('ed60_i_aluno', 15, $Ied60_i_aluno, true, 'text', $db_opcao, " onchange='js_pesquisaed60_i_aluno(false);'") ?>
                    <?php db_input('ed47_v_nome', 50, @$Ied47_v_nome, true, 'text', 3, '') ?>
                </td>
            </tr>
            <?php ///}?>

            <?php //if ($db_opcao == 1) {?>
            <tr>
                <td nowrap title="<?php echo @$Ted297_data ?>">
                    <?php echo @$Led297_data ?>
                </td>
                <td>
                    <?php db_inputdata(
                        'ed297_data', @$ed297_data_dia, @$ed297_data_mes,
                        @$ed297_data_ano, true, 'text', $db_opcao, ""
                    )
?>
                </td>
            </tr>
            <?php //}?>


 <?php
   if (isset($chavepesquisa)) {

     $data      = @$ed297_data_ano."-".@$ed297_data_mes."-".@$ed297_data_dia;
     $datamodif = @$ed60_datamodif_ano."-".@$ed60_d_datamodif_mes."-".@$ed60_d_datamodif_dia;
     $inicio    = @$ed52_d_inicio;
     $fim       = @$ed52_d_fim;

   } else {

     $data      = @$ed297_data_ano."-".@$ed297_data_mes."-".@$ed297_data_dia;
     $datamodif = @$ed60_d_datamodif_ano."-".@$ed60_d_datamodif_mes."-".@$ed60_d_datamodif_dia;
     $inicio    = @$ed60_d_datamatricula_ano."-".@$ed60_d_datamatricula_mes."-".@$ed60_d_datamatricula_dia;
     $fim       = @$ed60_d_datamatricula_ano."-".@$ed60_d_datamatricula_mes."-".@$ed60_d_datamatricula_dia;

 }
 ?>
</table>

        <div id="oDisciplinas"></div>

        <?php
if (isset($ed60_i_turma)) {

  $sSqlTurmaSerieRegimeMat = $oDaoTurmaSerieRegimeMat->sql_query("","ed220_i_codigo",""," ed220_i_turma = $ed60_i_turma");
  $rsTurmaSerieRegimeMat   = $oDaoTurmaSerieRegimeMat->sql_record($sSqlTurmaSerieRegimeMat);
  $iNumEtapas              = $oDaoTurmaSerieRegimeMat->numrows;

} else {
  $iNumEtapas = 0;
}
?>
  <input name="ed60_i_codigo" id="ed60_i_codigo" type="hidden" value="<?php echo @$ed60_i_codigo ?>">
        <input name="ed57_i_escola" id="ed57_i_escola" type="hidden" value="<?php echo @$ed57_i_escola ?>">
        <input name="ed57_i_base" type="hidden" value="<?php echo @$ed57_i_base ?>">
        <input name="ed57_i_calendario" type="hidden" value="<?php echo @$ed57_i_calendario ?>">
        <input name="codetapa" type="hidden" value="<?php echo @$codetapa ?>">
        <input name="ed57_i_turno" type="hidden" value="<?php echo @$ed57_i_turno ?>">
        <input name="<?php echo($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
         type="submit" id="db_opcao" style="display:none;"
         value="<?php echo($db_value_botao == 1 ? "Incluir" :
                      ($db_value_botao == 2 || $db_value_botao == 22 ? "Alterar" : "Excluir"))?>"
                <?php echo($db_botao==false?"disabled":"")?> />
  <input name="db_opcao_show" type="button" id="db_opcao_show" onClick="return js_valida();"
         value="<?php echo($db_value_botao == 1 ? "Incluir" :
                      ($db_value_botao == 2 || $db_value_botao == 22 ? "Alterar" : "Excluir"))?>"
                <?php echo ($db_botao == false ? "disabled" : "") ?> />
        <?php if ($db_opcao == 2) { ?>
            <input name="dbCancelar" type="button" value="Cancelar" onClick="js_cancela();"/>
        <?php } ?>
</form>
</center>

<div>
    <center>
        <fieldset width="95%">
            <legend><b>Registros</b></legend>
            <div id="oAlunos"></div>
        </fieldset>
    </center>
</div>

<script language="JavaScript">

function js_validaRPC() {

  var sUrl                = "edu4_escola.RPC.php";
  var jsRetorno           = "js_retornoValidaRPC";

  var oParam              = new Object();
      oParam.exec         = "validaMatriculaDependencia";
      oParam.iEscola      = <?php echo $iEscola ?>;
      oParam.iNumMarcados = getNumeroMarcados();
      oParam.aDisciplinas = getDisciplinasMarcadas();

  js_webajax(oParam, jsRetorno, sUrl, false);

}

function js_retornoValidaRPC(oRetorno) {
  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus == 0) {
    alert(oRetorno.sMessage.urlDecode());
    return false;
  } else {
    $('db_opcao').click();
  }

}

function js_valida() {
  var iTurma = $('ed60_i_turma').value;
  var iAluno = $('ed60_i_aluno').value;

  if (iTurma.trim() == "") {
    alert("Informe a Turma para realizar a matrícula.");
    return false;
  }

  if (iAluno.trim() == "") {
    alert("Informe o Aluno para realizar a matrícula.");
    return false;

  }

  if (checaDisciplinasMarcadas()) {
    return js_validaRPC();
  } else {
    alert("É necessário informar ao menos uma disciplina para a matrícula.");
    return false;

  }

}

function getNumeroMarcados() {

  var iMarcados = 0;
  var iOpcoes   = document.form1.disciplina.length;

  if (iOpcoes == undefined) {
    if ($('disciplina').checked == true) {
      iMarcados++;
    }
  } else {
    for (iCont = 0; iCont < iOpcoes; iCont++) {
      if (document.form1.disciplina[iCont].checked == true) {
        iMarcados++;
      }
    }
  }

  return iMarcados;
}

function getDisciplinasMarcadas() {
    var aDisc = Array();
    var iOpcoes = document.form1.disciplina.length;
    if (iOpcoes == undefined) {
        if ($('disciplina').checked == true) {
            aDisc[aDisc.length + 1] = $('disciplina').value;
        }
    } else {
        for (iCont = 0; iCont < iOpcoes; iCont++) {
            if (document.form1.disciplina[iCont].checked == true) {
                aDisc[aDisc.length + 1] = document.form1.disciplina[iCont].value;
            }
        }
    }
    return aDisc;
}

function checaDisciplinasMarcadas() {

  var iMarcados  = 0;
  var iOpcoes    = document.form1.disciplina.length;

  if (iOpcoes == undefined) {

    if ($('disciplina').checked == false) {
      return false;
    } else {
      iMarcados++;
      return true;

        }
  } else {

    var lErro = true;

    for (iCont = 0; iCont < iOpcoes; iCont++) {

      if (document.form1.disciplina[iCont].checked == true) {
        iMarcados++;
        lErro = false;
      }
}
        if (lErro == false) {
            return true;
        } else {
            return false;
        }
    }
}

function js_pesquisaed60_i_aluno(mostra) {

  if (document.form1.ed60_i_turma.value == "") {

    alert("Informe a Turma!");
	document.form1.ed60_i_aluno.value                 = '';
	document.form1.ed60_i_turma.style.backgroundColor = '#99A9AE';
	document.form1.ed60_i_turma.focus();

  } else {

	if (mostra == true) {

	  js_OpenJanelaIframe('','db_iframe_aluno',
	   	                  'func_matriculadependencia.php?turma='+document.form1.ed60_i_turma.value+
	   	                  '&funcao_js=parent.js_mostraaluno1|ed60_i_aluno|ed47_v_nome|ed60_i_codigo','Pesquisa de Alunos',true
	   	                 );

	} else {

	  if (document.form1.ed60_i_aluno.value != '') {

	    js_OpenJanelaIframe('','db_iframe_aluno',
	                        'func_matriculadependencia.php?turma='+document.form1.ed60_i_turma.value+
	                        '&pesquisa_chave='+document.form1.ed60_i_aluno.value+
	                        '&funcao_js=parent.js_mostraaluno','Pesquisa',false

);
	  } else {

	    document.form1.ed47_v_nome.value          = '';
	    document.form1.ed60_i_codigo.value        = '';
	    document.form1.ed60_c_situacaoatual.value = '';
	    document.form1.datamat.value              = '';
	    document.form1.datasaida.value            = '';
	    document.form1.alterarnada.disabled       = true;
	    document.form1.alterar.disabled           = true;
}
	  }

    }
}

function js_mostraaluno(chave1,chave2,chave3,chave4,chave5,erro) {

  document.form1.ed47_v_nome.value          = chave1;
  document.form1.ed60_i_codigo.value        = chave2;
  document.form1.ed60_c_situacaoatual.value = chave3;

  if (chave4 != "") {
    document.form1.datamat.value = chave4.substr(8,2)+"/"+chave4.substr(5,2)+"/"+chave4.substr(0,4);
  }

  if(chave5 != "") {
    document.form1.datasaida.value = chave5.substr(8,2)+"/"+chave5.substr(5,2)+"/"+chave5.substr(0,4);
  }

  if (erro == true) {

    document.form1.ed60_i_aluno.focus();
    document.form1.ed60_i_aluno.value         = '';
    document.form1.ed60_i_codigo.value        = '';
    document.form1.ed60_c_situacaoatual.value = '';
    document.form1.datamat.value              = '';
    document.form1.datasaida.value            = '';
    document.form1.alterarnada.disabled       = true;
    document.form1.alterar.disabled           = true;

  } else {

    document.form1.alterarnada.disabled = false;
    document.form1.alterar.disabled     = false;
    js_situacao(chave3);

  }
}

function js_mostraaluno1(chave1,chave2,chave3) {

  $('ed60_i_aluno').value  = chave1;
  $('ed47_v_nome').value   = chave2;
  $('ed60_i_codigo').value = chave3;
  db_iframe_aluno.hide();

  if ((chave1 != "" || chave1 != undefined) && (chave3 != "" || chave3 != undefined)) {
    js_buscaDisciplinas(chave1);

    }
  $('db_opcao_show').disabled = false;
}

function js_buscaDisciplinas(iAluno) {

  var sUrl             = "edu4_escola.RPC.php";
  var jsRetorno        = "js_retornoBuscaDisciplinas";

  var oParam           = new Object();
      oParam.exec      = "getDisciplinasDependencia";
      oParam.iAluno    = iAluno;
      oParam.iEscola   = $('ed57_i_escola').value;

  js_webajax(oParam, jsRetorno, sUrl, false);
}

function js_retornoBuscaDisciplinas(oRetorno) {

  var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus != 1) {

    alert(oRetorno.sMessage.urlDecode());
    $('db_opcao').disabled = true;
    return false;

  } else {

    if (parseInt(oRetorno.iRegistros, 10) > 0) {

      js_criaDisciplinas(oRetorno.aResultados);
      $('db_opcao').disabled = false;
}
    }


}

function js_criaDisciplinas(aDisciplinas) {
    var oElemento = $('oDisciplinas');
    oElemento.innerHTML = "";

    var sHtml = ' <br /> ';
    sHtml += ' <fieldset width="95%"><legend><b>Escolha as Disciplinas</b></legend> ';
    sHtml += '   <table> ';
    sHtml += '     <tr> ';
    sHtml += '       <td align="center"> ';

      for (iCont = 0; iCont < aDisciplinas.length; iCont++) {
        sHtml += '         <input type="checkbox" name="disciplina[]" id="disciplina"';
        sHtml += '               '+(aDisciplinas[iCont][2] == true ? "checked" : "");
        sHtml += '                value="'+aDisciplinas[iCont][0]+'" /> '+aDisciplinas[iCont][1].urlDecode()+' <br />';
}


      sHtml += '       </td> ';
      sHtml += '     </tr> ';
      sHtml += '   </table> ';
      sHtml += ' </fieldset> ';
      sHtml += ' <br /> ';

  oElemento.innerHTML = sHtml;
}

function js_pesquisaed60_i_turma() {

  js_OpenJanelaIframe('','db_iframe_turma','func_turmadependencia.php?funcao_js=parent.js_preenchepesquisaturma|ed57_i_codigo',
		              'Pesquisa de Turmas',true
		             );
}

function js_preenchepesquisaturma(chave) {

  db_iframe_turma.hide();
  <?php
   echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  ?>
}

function js_calcvagas() {

  var iVagas        = $('ed57_i_numvagas').value;
  var iMatriculados = $('ed57_i_nummatr').value;

  if ((parseInt(iVagas, 10) - parseInt(iMatriculados, 10)) < 0) {
    $('restantes') = 0;
  } else {
    $('restantes') = parseInt(iVagas, 10) - parseInt(iMatriculados, 10);
  }
}


function js_situacao(atual) {

  var F = document.getElementById("ed60_c_situacao");

  for (i = 0; i < F.length; i++) {
    F.options[i] = null;
  }
  atual = atual.replace(/^\s+|\s+$/g, '');

  if (atual == "MATRICULADO") {
    opcoes = new Array("CANCELADO|CANCELADO","EVADIDO|EVADIDO","ABANDONO ESCOLAR|ABANDONO ESCOLAR", "FALECIDO|FALECIDO");
    } else if (atual == "CANCELADO") {
        opcoes = new Array("MATRICULADO|RETORNO", "EVADIDO|EVADIDO", "ABANDONO ESCOLAR|ABANDONO ESCOLAR", "FALECIDO|FALECIDO");
    } else if (atual == "ABANDONO ESCOLAR" || atual=="EVADIDO") {
        opcoes = new Array("MATRICULADO|RETORNO", "FALECIDO|FALECIDO");
    } else if (atual == "FALECIDO") {
        opcoes = new Array("MATRICULADO|RETORNO", "CANCELADO|CANCELADO", "EVADIDO|EVADIDO", "ABANDONO ESCOLAR|ABANDONO ESCOLAR");
}
  for (i = 0; i < opcoes.length; i++) {

    v_array = opcoes[i].split("|");
    document.form1.elements["ed60_c_situacao"].options[i] = new Option(v_array[1],v_array[0]);

    if (v_array[0] == atual) {
      F.options[i] = null;

}
  }

  for (i = 0; i < F.length; i++) {

    if (F.options[i].text == atual) {
      F.options[i] = null;
    }
}


  document.form1.ed60_c_situacao.disabled = false;
  if (F.options[0].value == "MATRICULADO") {

    document.getElementById("eliminar").style.visibility = "visible";
    document.form1.eliminamov.checked                    = true;

  }
}

function js_selecionar(data,inicio,fim,numetapas) {

  if (document.form1.ed60_d_datamatricula.value == "") {

    alert("Informe a data para matricular o aluno!");
    document.form1.ed60_d_datamatricula.focus();
    document.form1.ed60_d_datamatricula.style.backgroundColor='#99A9AE';
    return false;

  } else {

    datamat = document.form1.ed60_d_datamatricula_ano.value+"-"+document.form1.ed60_d_datamatricula_mes.value+
              "-"+document.form1.ed60_d_datamatricula_dia.value;
    dataini = inicio;
    datafim = fim;
    check   = js_validata(datamat,dataini,datafim);

    if (check == false) {

      data_ini = dataini.substr(8,2)+"/"+dataini.substr(5,2)+"/"+dataini.substr(0,4);
      data_fim = datafim.substr(8,2)+"/"+datafim.substr(5,2)+"/"+datafim.substr(0,4);
      alert("Data da matrícula fora do periodo do calendario ( "+data_ini+" a "+data_fim+" ).");
      document.form1.ed60_d_datamatricula.focus();
      document.form1.ed60_d_datamatricula.style.backgroundColor='#99A9AE';
      return false;
}
    }



  var F = document.getElementById("alunos").options;
  for (var i = 0; i < F.length; i++) {
    F[i].selected = true;
  }

  if (F.length > document.form1.restantes.value) {

    alert("Número de alunos selecionados é maior que as vagas disponíveis!");
    return false;
}


  if (numetapas > 1) {

    alunos = "";
    sep    = "";

    for (var i = 0; i < F.length; i++) {

      alunos += sep+F[i].value;
      sep     = ",";
}


    js_OpenJanelaIframe('','db_iframe_matric','edu1_matriculaetapas001.php?turma='+document.form1.ed60_i_turma.value+
    	                '&codalunos='+alunos+'&datamat='+document.form1.ed60_d_datamatricula.value,
    	                'Matrícular Alunos',true
    	               );
    return false;


}
  return true;
}

if ((parseInt($('ed57_i_numvagas').value, 10) - parseInt($('ed57_i_nummatr').value, 10)) < 0) {
    $('restantes').value = 0;
} else {
    $('restantes').value = parseInt($('ed57_i_numvagas').value, 10) - parseInt($('ed57_i_nummatr').value, 10);
}

<?php
if ($db_value_botao == 1 && isset($chavepesquisa)) {
    ?>

  if (parseInt($('restantes').value, 10) == 0) {
    alert("Não há vagas disponíveis nesta turma!");
}
    <?php
}
?>

oDBGridAlunos = js_criaGridAlunos();

function js_criaGridAlunos() {
    oDBGrid = new DBGrid('oAlunos');
    oDBGrid.nameInstance = 'oDBGridAlunos';
    oDBGrid.hasTotalizador = false;
    oDBGrid.setCellWidth(new Array('20%', '30%', '30%', '10%', '10%'));
    oDBGrid.setHeight(150);

    var aHeader = new Array();
    aHeader[0] = "Matrícula";
    aHeader[1] = "Nome";
    aHeader[2] = "Disciplina";
    aHeader[3] = "Data do Vínculo";
    aHeader[4] = "Opções";
    oDBGrid.setHeader(aHeader);

    var aAligns = new Array();
    aAligns[0] = "center";
    aAligns[1] = "left";
    aAligns[2] = "center";
    aAligns[3] = "center";
    aAligns[4] = "center";
    oDBGrid.setCellAlign(aAligns);

    oDBGrid.show($('oAlunos'));
    oDBGrid.clearAll(true);

    return oDBGrid;
}

function js_buscaAlunos() {
    var sUrl = "edu4_escola.RPC.php";
    var jsRetorno = "js_retornoBuscaAlunos";

    var oParam = new Object();
    oParam.exec = "getAlunosMatriculaDependencia";
    oParam.iEscola = $('ed57_i_escola').value;
    oParam.iTurma = $('ed60_i_turma').value;

    js_webajax(oParam, jsRetorno, sUrl);
}

function js_retornoBuscaAlunos(oRetorno) {
    var oRetorno = JSON.parse(oRetorno.responseText);

  if (oRetorno.iStatus != 1) {

    alert(oRetorno.sMessage.urlDecode());
    return false;

  } else {

    if (oRetorno.iRegistros == 0) {

      oDBGridAlunos.clearAll(true);
      oDBGridAlunos.renderRows();

    } else {

      var aLinha = new Array();
      oDBGridAlunos.clearAll(true);
      oDBGridAlunos.renderRows();

            for (var iCont = 0; iCont < oRetorno.iRegistros; iCont++) {
                var sOpcoes = '<span onClick="js_alteraMatricula(' + oRetorno.aResultados[iCont].matricula + ', ' +
                    oRetorno.aResultados[iCont].turma + ');"> <u>A</u> </span>';

                aLinha[0] = oRetorno.aResultados[iCont].matricula;
                aLinha[1] = oRetorno.aResultados[iCont].nome.urlDecode();
                aLinha[2] = oRetorno.aResultados[iCont].disciplina.urlDecode();
                aLinha[3] = oRetorno.aResultados[iCont].data.urlDecode();
                aLinha[4] = sOpcoes;

                oDBGridAlunos.addRow(aLinha);
            }

            oDBGridAlunos.renderRows();
        }
    }
}

function js_alteraMatricula(iMatricula, iTurma) {
    location.href = "edu1_matriculadependencia001.php?iMatricula=" + iMatricula + "&iTurma=" + iTurma;
}

<?php if (isset($iTurma)) { ?>
function js_cancela() {
    location.href = "edu1_matriculadependencia001.php?chavepesquisa=<?php echo $iTurma?>";
}
<?php } ?>

<?php if (isset($chavepesquisa) && trim($chavepesquisa) != "") { ?>
js_buscaAlunos();
<?php } ?>

<?php if (isset($iMatricula) && isset($iTurma)) { ?>
  js_buscaDisciplinas($('ed60_i_aluno').value);
<?php } ?>
</script>
