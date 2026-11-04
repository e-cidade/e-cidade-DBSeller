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
 

  require_once(modification("libs/db_utils.php"));
  require_once(modification("libs/db_stdlibwebseller.php"));
  require_once(modification("libs/db_stdlib.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));
  require_once(modification("libs/db_usuariosonline.php"));
  require_once(modification("libs/db_app.utils.php"));
  require_once(modification("dbforms/db_funcoes.php"));
  
  $iEscola = db_getsession("DB_coddepto");

?>

<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    
    <?php
      $sLib  = "estilos.css,";
      $sLib .= "scripts.js, prototype.js, webseller.js, strings.js";
      db_app::load($sLib);
    ?>
    
  </head>
  <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
    <table width="790" height="18"  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    <br /><br />
    <center>
      <fieldset style="width:80%;"><legend><b>Relatório Diário de Classe - Dependência</b></legend>
        <form name="form1" id="form1" method="post" action="" >
          <table>
            <tr>
              <td>
                <b>Selecione o Calendário:</b>
              </td>
              <td>
                <select id="iCalendario" name="iCalendario" onChange="js_buscaTurmas(this.value);" >
                  <option value="">Escolha...</option>
                </select>
              </td>
              <td>
                <b>Selecione a Turma:</b>
              </td>
              <td>
                <select id="iTurma" name="iTurma" onChange="js_buscaPeriodos(this.value); js_buscaDisciplinas(this.value);" >
                  <option value="">Escolha o Calendário...</option>
                </select>
              </td>
              <td>
                <!--  
                <input type="button" value="Procurar" id="pesquisaTurmas" onClick="js_pesquisaTurmas();" />
                -->
              </td>
            </tr>
          </table>
          
          <div id="webAuxilia">
          
            <br />
            <fieldset style="text-align:center;"><legend><b>Configuração do Relatório</b></legend>
              <input type="checkbox" id="sexo" name="sexo" value="" checked />Sexo 
              <input type="checkbox" id="idade" name="idade" value="" checked />Idade 
              <input type="checkbox" id="abono" name="abono" value="" checked />Faltas Abonadas 
              <input type="checkbox" id="codigo" name="codigo" value="" checked />Código 
              <input type="checkbox" id="nasc" name="nasc" value="" checked />Nascimento 
              <input type="checkbox" id="resultant" name="resultant" value="" checked />Resultado Anterior 
              <input type="checkbox" id="totalfal" name="totalfal" value="" checked />Total de Faltas 
              <input type="checkbox" id="parecer" name="parecer" value="" checked />Parecer 
            </fieldset>
            <!-- Fim Configuração do Relatório -->
            
            <br />
            <table style="width:100%">
              <tr>
                <td style="width:45%;">
                  <b>Disciplinas: </b>
                  <select multiple id="oDisciplinas" name="oDisciplinas" style="width:100%; height:140px;"
                          onClick="js_mudaCheca(this, 'mudaInclui');" ></select>
                </td>
                <td style="width:10%;">
                
                  <!-- Inicio Botões de Trocas de Elementos -->
                  <table style="width:100%; text-align:center;">
                    <tr>
                      <td>
                        <input type="button" value=">" id="mudaInclui" style="width:30px;" 
                               onClick="js_mudaInclui();" disabled />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <input type="button" value=">>" id="mudaIncluiTodos" style="width:30px;" 
                               onClick="js_mudaIncluiTodos();" disabled />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <hr style="width:35px; color:#333;" />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <input type="button" value="<" id="mudaExclui" style="width:30px;" 
                               onClick="js_mudaExclui();" disabled />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <input type="button" value="<<" id="mudaExcluiTodos" style="width:30px;" 
                               onClick="js_mudaExcluiTodos();" disabled />
                      </td>
                    </tr>
                  </table>
                  <!-- Fim Botões de Trocas de Elementos -->
                  
                </td>
                <td style="width:45%;">
                  <b>Disciplinas parar gerar o diário de classe: </b>
                  <select multiple id="oEscolhidas" name="oEscolhidas" style="width:100%; height:140px;"
                          onClick="js_mudaCheca(this, 'mudaExclui'); js_checaBotao();" ></select>
                </td>
              </tr>
            </table>
            <!-- Fim dos Selects Multiplos das Disciplinas -->
            
            <table style="width:100%; text-align:center;">
              <tr>
                <td style="width:35%;">
                  <b>Período de Avaliação: </b>
                  <select id="iPeriodo" name="iPeriodo" ></select>
                </td>
                <td style="width:65%;">
                  <b>Informar Dias Letivos: </b>
                  <select id="informadiasletivos" name="informadiasletivos" onChange="js_checaDiasLetivos(this.value);" >
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                  </select>
                  <span id="oQtdColunas" style="display:none;">
                    &nbsp;&nbsp;&nbsp;
                    <b>Quantidade de Colunas (Presenças): </b>
                    <select id="colunas" name="colunas">
                      <?php
                        for ($iCont = 30; $iCont <= 70; $iCont++) {
                          echo('<option value="'.$iCont.'">'.$iCont.'</option>');
                        }
                      ?>
                    </select>
                  </span>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:center;">
                  <b>Mostrar somente alunos ativos (Matriculados): </b>
                  <select id="ativo" name="ativo">
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                  </select>
                </td>
              </tr>
            </table>
            
            <br />
            <center>
              <input type="button" id="btnProcessar" name="btnProcessar" value="Processar" 
                     onClick="js_processa();" disabled />
            </center>
          
          </div>
          
          <input type="hidden" id="iEscola" name="iEscola" value="<?=$iEscola?>" />
        </form>
      </fieldset>
    </center>
    <?php 
      db_menu(db_getsession("DB_id_usuario"),
              db_getsession("DB_modulo"),
              db_getsession("DB_anousu"),
              db_getsession("DB_instit")
             );
    ?>
  </body>
</html>

<script type="text/javascript">

  function js_webInicializa() {

    $('webAuxilia').style.display  = "none";
	js_buscaCalendario($('iEscola').value);
	  
  }

  function js_processa() {

    if (parseInt($('oEscolhidas').options.length, 10) < 1) {

      alert("Escolha ao menos uma disciplina para gerar o diário de classe.");
      return false;    

    } else {

      var iDisciplinas = js_getDisciplinas();
      var lSexo        = false;
      var lIdade       = false;
      var lAbono       = false;
      var lCodigo      = false;
      var lNasc        = false;
      var lResultAnt   = false;
      var lTotalFaltas = false;
      var lParecer     = false;

      if ($('sexo').checked == true) {
        lSexo = true;
      }

      if ($('idade').checked == true) {
        lIdade = true;
      }

      if ($('abono').checked == true) {
        lAbono = true;
      }

      if ($('codigo').checked == true) {
        lCodigo = true;
      }

      if ($('nasc').checked == true) {
        lNasc = true;
      }

      if ($('resultant').checked == true) {
        lResultAnt = true;
      }

      if ($('totalfal').checked == true) {
        lTotalFaltas = true;
      }

      if ($('parecer').checked == true) {
        lParecer = true;
      }
    	
      var oJanela = window.open('edu2_diarioclassedependencia002.php?iColunas='+$('colunas').value+'&sDiasLetivos='+
    	                        $('informadiasletivos').value+'&iPeriodo='+$('iPeriodo').value+'&iDisciplinas='+
    	                        iDisciplinas+'&iTurma='+$('iTurma').value+'&sAtivo='+$('ativo').value+'&lSexo='+
    	                        lSexo+'&lIdade='+lIdade+'&lAbono='+lAbono+'&lCodigo='+lCodigo+'&lNascimento='+lNasc+
    	                        '&lResultAnt='+lResultAnt+'&lTotalFaltas='+lTotalFaltas+'&lParecer='+lParecer+
    	                        '&iEscola='+$('iEscola').value, 
    	                        '', 
    	                        'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
          oJanela.moveTo(0,0);

    }
	  
  }

  function js_getDisciplinas() {

    var sDisciplinas = "";
    var sSeparador   = "";

    for (iCont = 0; iCont < $('oEscolhidas').options.length; iCont++) {

      sDisciplinas += sSeparador+$('oEscolhidas').options[iCont].value;
      sSeparador    = ", ";

    }

    return sDisciplinas;
	  
  }

  function js_checaBotao() {

    if (parseInt($('oEscolhidas').options.length, 10) > 0) {
      $('btnProcessar').disabled = false;
    } else {
      $('btnProcessar').disabled = true;
    }
	  
  }

  function js_buscaCalendario(iEscola) {

    var sUrl          = "edu4_escola.RPC.php";
    var jsRetorno     = "js_retornoBuscaCalendario";

    var oParam        = new Object();
        oParam.exec   = "PesquisaCalendario";
        oParam.escola = iEscola;

    js_webajax(oParam, jsRetorno, sUrl, false);
	    
  }

  function js_retornoBuscaCalendario(oRetorno) {

    var oRetorno = JSON.parse(oRetorno.responseText);

    if (oRetorno.iStatus != 1) {

      alert(oRetorno.sMessage.urlDecode());
      return false;

    } else {
      
      for (iCont = 0; iCont < oRetorno.aResult.length; iCont++) {
        
        var sDescricao = oRetorno.aResult[iCont].ed52_c_descr.urlDecode();
        var iCodigo    = oRetorno.aResult[iCont].ed52_i_codigo;
    	  
        $('iCalendario').options[$('iCalendario').options.length] = new Option(sDescricao, iCodigo);
          
      }

    }
	  
  }

  function js_buscaTurmas(iCalendario) {

    var sUrl               = "edu4_escola.RPC.php";
	var jsRetorno          = "js_retornoBuscaTurmas";

	var oParam             = new Object();
	    oParam.exec        = "getTurmasDependencia";
	    oParam.iEscola     = $('iEscola').value;
	    oParam.iCalendario = iCalendario;

	js_webajax(oParam, jsRetorno, sUrl, false);
	  
  }

  function js_retornoBuscaTurmas(oRetorno) {

    var oRetorno = JSON.parse(oRetorno.responseText);

    if (oRetorno.iStatus != 1) {

      alert(oRetorno.sMessage.urlDecode());
      return false;

    } else {

      $('iTurma').innerHTML  = "";
      $('iTurma').options[0] = new Option("Escolha a Turma...", "");
      
      for (iCont = 0; iCont < oRetorno.iRegistros; iCont++) {

        var sDescricao = oRetorno.aResultados[iCont].descricao.urlDecode();
        var iCodigo    = oRetorno.aResultados[iCont].codigo;
      	
        $('iTurma').options[$('iTurma').options.length] = new Option(sDescricao, iCodigo);
          
      }

    }
	  
  }

  function js_pesquisaTurmas() {

    var iEscola     = $('iEscola').value;
    var iCalendario = $('iCalendario').value;
    var iTurma      = $('iTurma').value;

    if (iCalendario != "" && iTurma != "" && iEscola != "") {
	
    	js_buscaDisciplinas(iTurma);
    	js_buscaPeriodos(iTurma);
    	
    } else {

      if (iEscola == "") {

        alert("Selecione a escola!");
        return false;

      } else if (iCalendario == "") {

        alert("Selecione o calendário!");
        return false;
          
      } else if (iTurma == "") {

        alert("Selecione a turma!");
        return false;
    	  
      }
        
    }
	  
  }

  function js_buscaDisciplinas(iTurma) {

	if (iTurma.trim() != "") {
		
      var sUrl           = "edu4_escola.RPC.php";
      var jsRetorno      = "js_retornoBuscaDisciplinas";

      var oParam         = new Object();
          oParam.exec    = "getDisciplinasDependenciaTurma";
          oParam.iTurma  = iTurma;

      js_webajax(oParam, jsRetorno, sUrl);

	}
	  
  }

  function js_retornoBuscaDisciplinas(oRetorno) {

    var oRetorno = JSON.parse(oRetorno.responseText);

    if (oRetorno.iStatus != 1) {

      $('webAuxilia').style.display = "none";
    	
      alert(oRetorno.sMessage.urlDecode());
      return false;

    } else {

      $('webAuxilia').style.display = "";
      $('oDisciplinas').innerHTML   = "";

      for (iCont = 0; iCont < oRetorno.iRegistros; iCont++) {

        var sDescricao = oRetorno.aResultados[iCont].descricao.urlDecode();
        var iCodigo    = oRetorno.aResultados[iCont].codigo;
    	  
        $('oDisciplinas').options[$('oDisciplinas').options.length] = new Option(sDescricao, iCodigo);
    	  
      }

    }
	  
  }

  function js_buscaPeriodos(iTurma) {

    if (iTurma.trim() != "") {

      var sUrl           = "edu4_escola.RPC.php";
      var jsRetorno      = "js_retornoBuscaPeriodos";

      var oParam         = new Object();
          oParam.exec    = "getPeriodosAvaliacao";
          oParam.iEscola = $('iEscola').value;
          oParam.iTurma  = iTurma;

      js_webajax(oParam, jsRetorno, sUrl);

    }
	  
  }

  function js_retornoBuscaPeriodos(oRetorno) {

    var oRetorno = JSON.parse(oRetorno.responseText);

    if (oRetorno.iStatus != 1) {

      alert(oRetorno.sMessage.urlDecode());
      return false;

    } else {

      $('iPeriodo').innerHTML = "";

      for (iCont = 0; iCont < oRetorno.iRegistros; iCont++) {

        var sDescricao = oRetorno.aResultados[iCont].descricao.urlDecode();
        var iCodigo    = oRetorno.aResultados[iCont].codigo;
    	  
        $('iPeriodo').options[$('iPeriodo').options.length] = new Option(sDescricao, iCodigo);
    	  
      }

    }
	  
  }

  function js_checaDiasLetivos(sOpcao) {

    if (sOpcao == 'S') {
      $('oQtdColunas').hide();
    } else {
      $('oQtdColunas').show();
    }
	  
  }

  js_webInicializa();

  var oOrigem  = $('oDisciplinas');
  var oDestino = $('oEscolhidas');

  function js_mudaInclui() {

    var iTamanho = oOrigem.options.length;

    for (iCont = 0; iCont < iTamanho; iCont++) {

      if (oOrigem.options[iCont].selected == true) {

        var sDescricao = oOrigem.options[iCont].text;
        var iCodigo    = oOrigem.options[iCont].value;
    	  
        oDestino.options[oDestino.options.length] = new Option(sDescricao, iCodigo);

        oOrigem.options[iCont] = null;
        iTamanho--;
        iCont--;
    	  
      }
    	
    }

    if (oOrigem.options.length > 0) {
      oOrigem.options[0].selected = true;
    } else {

      $('mudaInclui').disabled      = true;
      $('mudaIncluiTodos').disabled = true;
        
    }

    $('mudaExcluiTodos').disabled = false;

    js_checaBotao();
	  
  }

  function js_mudaIncluiTodos() {

	var iTamanho = oOrigem.options.length;

	for (iCont = 0; iCont < iTamanho; iCont++) {

      var sDescricao = oOrigem.options[iCont].text;
      var iCodigo    = oOrigem.options[iCont].value;
		
      oDestino.options[oDestino.options.length] = new Option(sDescricao, iCodigo);
	
	}

	oOrigem.innerHTML = '';

	$('mudaInclui').disabled      = true;
	$('mudaIncluiTodos').disabled = true;

	oDestino.options[0].selected = true;
	$('mudaExclui').disabled     = false;

	if (oDestino.options.length > 1) {
      $('mudaExcluiTodos').disabled = false;
	}

	js_checaBotao();
	  
  }

  function js_mudaExclui() {

	var iTamanho = oDestino.options.length;

	for (iCont = 0; iCont < iTamanho; iCont++) {

      if (oDestino.options[iCont].selected == true) {

    	var sDescricao = oDestino.options[iCont].text;
        var iCodigo    = oDestino.options[iCont].value;
      	  
        oOrigem.options[oOrigem.options.length] = new Option(sDescricao, iCodigo);

        oDestino.options[iCont] = null;
        iTamanho--;
        iCont--;
    	  
      }
	      
	}

	if (oDestino.options.length > 0) {
	  oDestino.options[0].selected = true;
	} else {

	  $('mudaExclui').disabled      = true;
	  $('mudaExcluiTodos').disabled = true;
	        
	}

	$('mudaIncluiTodos').disabled = false;

	js_checaBotao();
	  
  }

  function js_mudaExcluiTodos() {

	var iTamanho = oDestino.options.length;

	for (iCont = 0; iCont < iTamanho; iCont++) {

	  var sDescricao = oDestino.options[iCont].text;
	  var iCodigo    = oDestino.options[iCont].value;
			
	  oOrigem.options[oOrigem.options.length] = new Option(sDescricao, iCodigo);
		
	}

	oDestino.innerHTML = '';

	$('mudaExclui').disabled      = true;
	$('mudaExcluiTodos').disabled = true;

	oOrigem.options[0].selected   = true;
	$('mudaInclui').disabled      = false;

	if (oOrigem.options.length > 1) {
	  $('mudaIncluiTodos').disabled = false;
	}

	js_checaBotao();
		  
  }

  function js_mudaCheca(oElemento, sOpcao) {
    
    if (oElemento.value != "") {

      if (sOpcao == 'mudaInclui') {
        $('mudaInclui').disabled = false;
      } else {
    	$('mudaExclui').disabled = false;
      }

      if (parseInt(oElemento.options.length, 10) > 1) {

        if (sOpcao == 'mudaInclui') {
          $('mudaIncluiTodos').disabled = false;
        } else {
          $('mudaExcluiTodos').disabled = false;
        }
          
      }
      
        
    } else {

      if (sOpcao == 'mudaInclui') {
          
        $('mudaInclui').disabled      = true;
        $('mudaIncluiTodos').disabled = true;
        
      } else {
          
      	$('mudaExclui').disabled      = true;
      	$('mudaExcluiTodos').disabled = true;
      	
      }
        
    }

    js_checaBotao();
	  
  }

</script>