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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));

db_postmemory($_POST);

if ($_POST) {
    if (!isset($escolas)) {
        db_msgbox('Escolha pelo menos uma escola');
    }
}
$iModulo        = db_getsession("DB_modulo");
$iDepartamento  = db_getsession("DB_coddepto");
$sNomeEscola    = db_getsession("DB_nomedepto");
?>

<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/webseller.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <style type="text/css">
    select:not([rel="ignore-css"]) option {
      min-height: 18px;
      display: flex;
      align-items: center;
      font-size: 12px;
    }
    optgroup {
      font-size: 13px;
    }
    select option:nth-child(even) {
      background-color: #ececec;
    }
    select option:nth-child(odd) {
      background-color: #ffffff;
    }
    #etapa, #semestre, #necessidade, #subdivisao, #escolas {
      width: auto !important;
      min-width: 100%;
    }
    #necessidade, #subdivisao {
      height: 353px !important;
    }
    #escolas {
      height:370px !important;
    }
    #ctEscolas, #ctNecessidade, #ctEtapa, #ctAno, #ctSemestre, #ctSubdivisao, #ctTipoAtendimento {
      width: 200px !important;
      overflow-x:auto;
    }
    #fieldset-necessidade,#fieldset-subdivisao {
      height: 386px;
    }
    #fieldset-escola input[type="button"] {
      width: 100%;
      margin: 0 20px;
      height: 25px !important;
      font-weight: bold;
    }
    #cadeirante, #bpc {
      width: 100%;
    }
  </style>
</head>
<body>
<div class="container">
  <form name="form" method="post" action="" style="display: flex; flex-wrap: wrap; padding: 10px; justify-content: center;">
  
  <?php
    if (db_permissaomenu(db_getsession("DB_anousu"), 7159, 2001309) == 'false') {
        MsgAviso(db_getsession("DB_coddepto"), "escola");
    }
    ?>
  <table>
    <tr>
      <td>
      <fieldset>
      <legend> Alunos > Alunos PcD / Altas Habilidades </legend>
      <table>
        <tr>
          <td>
            <fieldset id="fieldset-escola">
              <legend>ESCOLAS:</legend>
                <table style="width: 100px">
                  <tbody style="display: table;">
                    <tr>
                      <td align="left">
                        <div id="ctEscolas">
                        <select name="escolas[]" id="escolas" multiple onclick="js_cleanBlock()">
                          <option>Carregando . . .</option>
                        </select>
                        </div>
                      </td>
                    </tr>
                    <tr style="width: 100%; display: block">
                      <td style="width: 100%; display: flex; height: 40px; align-items: center;">
                        <input type="button" value="Limpar" name="limpar" onclick="js_limpar();">
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
          </td>
          <td>
            <fieldset style="width: 100px">
              <legend>ANO:</legend>
                <table>
                  <tbody style="display: block; ">
                    <tr style="display: table; ">
                      <td style="display: table;">
                        <div id="ctAno">
                          <select id='ano' style="width: 100%" onchange='js_buscaEtapa();' >
                            <option value='' selected="selected">Selecione o ano</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
          
            <fieldset>
              <legend>CURSO / ETAPA:</legend>
                <table>
                  <tbody style="display: block;">
                    <tr style="display: table;">
                      <td style="display: table;">
                        <div id="ctEtapa">
                          <select id='etapa' style="height: 361px" disabled='disabled' multiple>
                            <option value='' selected="selected">Selecione uma etapa</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
      
            <fieldset id="fieldset-semestre" style="display: none;">
              <legend>SEMESTRE:</legend>
                <table>
                  <tbody style="display: block;">
                    <tr style="display: table; width: 100%">
                      <td style="display: table; width: 100%">
                        <div id="ctSemestre">
                          <select id='semestre'>
                            <option value='' selected="selected">Selecione o semestre</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
          
          </td>
          <td>
            <fieldset id="fieldset-necessidade">
              <legend>DEFICIÊNCIA / ALTAS HABILIDADES:</legend>
                <table style="height:100%;">
                  <tbody style="display: block; height:100%;">
                    <tr style="display: table; height:100%;width:100%">
                      <td style="display: table; height:100%;width:100%">
                        <div id="ctNecessidade">
                          <select id='necessidade' multiple onchange='js_buscaSubdivisao();'>
                            <option value='0' selected="selected">TODAS NECESSIDADES</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
            
            <fieldset id="fieldset-cadeirante"">
              <legend>MOSTRAR CADEIRANTES:</legend>
                <table style="width: 100%">
                  <tbody style="display: block;">
                    <tr style="display: table; width: 100%">
                      <td style="display: table; width: 100%">
                        <div id="ctCadeirante">
                          <?php
                            $aCadeirante = array(0 => 'TODOS', 1 => 'SIM',2 => 'NÃO');
                            db_select('cadeirante', $aCadeirante, true, 1);
                            ?>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
            
          </td>
          <td>
          
            <fieldset id="fieldset-subdivisao">
              <legend>SUBDIVISÃO:</legend>
                <table style="height:100%;">
                  <tbody style="display: block; height:100%;">
                    <tr style="display: table; height:100%;width:100%">
                      <td style="display: table; height:100%;width:100%">
                        <div id="ctSubdivisao">
                          <select id='subdivisao' name='subdivisao[]' multiple>
                            <option value='0'>Aguarde ...</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
      
            <fieldset id="fieldset-bpc">
              <legend>POSSUI BPC:</legend>
                <table style="width: 100%">
                  <tbody style="display: block;">
                    <tr style="display: table; width: 100%">
                      <td style="display: table; width: 100%">
                        <div id="ctBPC">
                          <?php
                            $aBPC = array(0 => 'TODOS', 1 => 'SIM',2 => 'NÃO');
                            db_select('bpc', $aBPC, true, 1);
                            ?>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
            
          </td>
        </tr>
        <tr>
          <td>
          <fieldset style="width: 100px">
              <legend>TIPO ATENDIMENTO:</legend>
                <table>
                  <tbody style="display: block; ">
                    <tr style="display: table; ">
                      <td style="display: table;">
                        <div id="ctTipoAtendimento">
                          <select id='tipoAtendimento' style="width: 100%" >
                            <option value='0' selected="selected">TODOS</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="4">
            <table style="display: flex; justify-content: center;">
              <tr style="width: 100%; display: block">
                <td style="width: 100%; display: flex;">
                  <fieldset style="width:725px;text-align: center;">
                  Para selecionar mais de uma Escola, Curso/Etapa ou Deficiência / Altas Habilidades,<br>
                  mantenha pressionada a tecla CTRL e clique sobre as opções desejadas.
                  </fieldset>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>      
      </fieldset>
    </td>
    </tr>
    <tr>
      <td align="center">
        <input type="button" name="imprimir" id="imprimir" value="Imprimir" onclick="js_imprime()" disabled='disabled' />
      </td>
    </tr>
  </table>  
  </form>
</div>

<script>
  var sRPC          = 'edu_educacaobase.RPC.php';
  var iModulo       = <?php echo $iModulo;?>;
  var iDepartamento = <?php echo $iDepartamento;?>;
  var selecteds = [];

  var oOptionSelecioneEtapa       = document.createElement('option');
  oOptionSelecioneEtapa.value     = "0";
  oOptionSelecioneEtapa.innerHTML = "Selecione uma etapa";

  var oOptionSelecioneAno       = document.createElement('option');
  oOptionSelecioneAno.value     = "";
  oOptionSelecioneAno.innerHTML = "Selecione o ano";

  var oOptionSelecioneSemestre       = document.createElement('option');
  oOptionSelecioneSemestre.value     = "";
  oOptionSelecioneSemestre.innerHTML = "Selecione uma semestre";

  var oOptionTodos       = document.createElement('option');
  oOptionTodos.value     = "0";
  oOptionTodos.addEventListener('click',(event)=>{
    event.stopPropagation();
    $('etapa').childNodes.forEach((optGroup)=>{
      optGroup.childNodes.forEach((opt)=>{
        opt.selected = false;
      });
    });
    $('etapa').childNodes[0].selected = true;
  })
  oOptionTodos.innerHTML = "TODOS";

  function js_buscaEscola() {

    var oParamentro           = new Object();
    oParamentro.exec          = 'pesquisaEscola';
    oParamentro.lTodasEscolas = true;

    if (iModulo == 1100747) {
      oParamentro.lTodasEscolas = false;
    }

    js_divCarregando("Aguarde, buscando escolas...", "msgBox");
     new Ajax.Request
      (sRPC,
       {
        method:     'post',
        parameters: 'json='+Object.toJSON(oParamentro),
        onComplete: js_retornoEscola
       }
      );
  };

  function js_retornoEscola(oAjax) {
    var oRetorno = eval('('+oAjax.responseText+')');
    var iEscola  = null;
    var escolas = [];

    <?php if (isset($escolas)) { ?>
      escolas = <?php echo json_encode($escolas); ?>;
    <?php } ?>
    var oOption = document.createElement('option');

    if(!!escolas.includes(0)) {
      oOption.setAttribute("selected","selected");
    }

    oOption.addEventListener('click',(event)=>{
      event.stopPropagation();
      js_cleanBlock();
      $('escolas').childNodes.forEach((opt)=>{
        opt.selected = false;
      });
      $('escolas').childNodes[0].selected = true;
    });

    oOption.value     = "0";
    oOption.innerHTML = "TODAS AS ESCOLAS"
    $('escolas').innerHTML = '';

    if (iModulo != 1100747) {
      $('escolas').appendChild(oOption);
    }

    oRetorno.dados.each(function (oEscola) {
      var oOption       = document.createElement('option');
      
      if(!!escolas.includes(oEscola.codigo_escola)) {
        oOption.setAttribute("selected","selected");
      }

      oOption.addEventListener('click',(event)=>{
        event.stopPropagation();
        js_cleanBlock();
        if($('escolas').childNodes.length>1) {
          $('escolas').childNodes[0].selected = false;
        }
      });

      oOption.value     = oEscola.codigo_escola;
      oOption.innerHTML = oEscola.codigo_escola+' - '+oEscola.nome_escola.urlDecode();
      
      $('escolas').appendChild(oOption);
      iEscola = oEscola.codigo_escola;
    });

    $('fieldset-escola').removeAttribute('style');
    js_removeObj('msgBox');
    if (oRetorno.dados.length == 1) {
      $('escolas').value = iEscola;
    }
  }

  function js_pesquisaEscola() {
    var options = document.form.escolas.options;
    selecteds = [];
    for(var i=0; i<options.length; i++) {
      if(options[i].selected)
        selecteds.push(options[i].value)
    }

    if(!selecteds.length>0) {
      $('ano').options[0].selected = true;
      alert('Selecione pelo menos uma escola.');
      return false;
    } else {
      return true;
    }
  }

  function js_buscaAno() {
    var oParamentro     = new Object();
    oParamentro.exec    = 'pesquisaAnoLetivoEscola';
    oParamentro.iEscola = $F('escolas');

    if (iModulo == 1100747) {
      oParamentro.iEscola = iDepartamento;
    }

    $('ano').options.length = 0;
    $('ano').appendChild(oOptionSelecioneAno);

    js_divCarregando("Aguarde, buscando ano...", "msgBoxAno");
    new Ajax.Request
     (sRPC,
      {
       method:     'post',
       parameters: 'json='+Object.toJSON(oParamentro),
       onComplete: js_retornoAno
      }
     );
  }

  function js_retornoAno(oAjax) {
    js_removeObj('msgBoxAno');
    $('etapa').options.length = 0;
    $('etapa').appendChild(oOptionSelecioneEtapa);
    $('etapa').setAttribute("disabled","disabled");

    var oRetorno = eval('('+oAjax.responseText+')');

    if (oRetorno.status == 2) {
      alert(oRetorno.message.urlDecode());
      return false;
    }

    oRetorno.aAno.each(function (oAno) {
      var oOption       = document.createElement('option');
      oOption.value     = oAno.ed52_i_ano;
      oOption.innerHTML = oAno.ed52_i_ano;
      $('ano').appendChild(oOption);
    });
  }

  /**
   * Busca as etapas do ano e escola selecionada ou todas as etapas de um ano selecionado
   */
  function js_buscaEtapa() {
    var bEscola = js_pesquisaEscola()
    if(!bEscola) return;
    
    if ($F('ano') == '') {
      $('etapa').options.length = 0;
      $('etapa').innerHTML = '';
      $('etapa').appendChild(oOptionSelecioneEtapa);
      $('etapa').setAttribute("disabled","disabled");
      $('imprimir').setAttribute("disabled","disabled");
      return false;
    }

    var oParamentro     = new Object();
    oParamentro.exec    = 'pesquisaEtapaAno';
    oParamentro.iEscola = $F('escolas').join(', ');
    oParamentro.iAno    = $F('ano');

    js_divCarregando("Aguarde, buscando cursos...", "msgBoxCurso");
    new Ajax.Request
      (
        sRPC,
        {
          method:     'post',
          parameters: 'json='+Object.toJSON(oParamentro),
          onComplete: js_retornoEtapa
        } 
      );
  }

  function js_retornoEtapa(oAjax) {
    js_removeObj('msgBoxCurso');
    var semestres = [];
    var hasSemetre = false;
    var oRetorno = eval('('+oAjax.responseText+')');
    $('etapa').options.length = 0;

    if (oRetorno.status == 2) {
      $('etapa').appendChild(oOptionSelecioneEtapa);
      $('etapa').setAttribute("disabled","disabled");
      $('imprimir').setAttribute("disabled","disabled");
      alert(oRetorno.message.urlDecode());
      return false;
    }

    $('etapa').appendChild(oOptionTodos);

    if (oRetorno.aEtapaAno.length > 0) {
      $('etapa').removeAttribute('disabled');
    }

    oRetorno.aEtapaAno.each(function (oEtapaAno) {
      var oOptGroup;

      if(!!$('curso-'+oEtapaAno.ed10_i_codigo)) {
        oOptGroup = $('curso-'+oEtapaAno.ed10_i_codigo);
      } else {
        oOptGroup = document.createElement('optgroup');
        oOptGroup.addEventListener("click",selectAll);
        oOptGroup.setAttribute('id','curso-'+oEtapaAno.ed10_i_codigo);
        oOptGroup.setAttribute('label',oEtapaAno.ed10_c_descr.urlDecode());
        oOptGroup.setAttribute('style','background-color:#ccc;cursor: pointer;');
      }

      $('etapa').appendChild(oOptGroup);

      if(oEtapaAno.ed52_i_periodo>0) {
        if(!semestres.includes(oEtapaAno.ed52_i_periodo)) {
          semestres.push(oEtapaAno.ed52_i_periodo);
          hasSemetre = true;
        }
      }

      if(!!$('etapa-'+oEtapaAno.ed11_i_codigo)) {
        return;
      } else {
        var oOption = document.createElement('option');
        oOption.setAttribute('id','etapa-'+oEtapaAno.ed11_i_codigo);
        oOption.addEventListener("click",stopPropagation)
        oOption.setAttribute('style','cursor: default;')
        oOption.value     = oEtapaAno.ed11_i_codigo;
        oOption.innerHTML = oEtapaAno.ed11_c_descr.urlDecode();
        oOptGroup.appendChild(oOption);
      }
    });

    $('semestre').innerHTML = '';
    semestres.forEach((periodo)=>{
      var oOption = document.createElement('option');
        oOption.value     = periodo;
        oOption.innerHTML = periodo+'º semestre';
        $('semestre').appendChild(oOption);
    });

    if(hasSemetre) {
      $('fieldset-semestre').setAttribute('style','display:inherit');
      $('etapa').setAttribute('style','width:100%;height:291px;');
    } else {
      $('etapa').setAttribute('style', 'width:100%;height:351px;')
    }

    $('imprimir').removeAttribute('disabled');
  }

  function js_buscaNecessidade() {
    var oParamentro     = new Object();
    oParamentro.exec    = 'pesquisaNecessidade';

    js_divCarregando("Aguarde, buscando necessidades...", "msgBoxNecessidade");
    new Ajax.Request
      (
        sRPC,
        {
          method:     'post',
          parameters: 'json='+Object.toJSON(oParamentro),
          onComplete: js_retornoNecessidade
        } 
      );
  }

  function js_limpar() {
    const tam = document.form.escolas.length;
    for(i=0;i<tam;i++){
      document.form.escolas[i].selected = false;
    }
    js_cleanBlock();
  }

  $('necessidade').children[0].addEventListener('click',(event)=>{
    event.stopPropagation();
    $('necessidade').childNodes.forEach((opt)=>{
      opt.selected = false;
    });
    $('necessidade').children[0].selected = true;
  });

  function js_retornoNecessidade(oAjax) {
    js_removeObj('msgBoxNecessidade');
    var oRetorno = eval('('+oAjax.responseText+')');

    oRetorno.necessidades.each(function (oNecessidade) {
      var oOption       = document.createElement('option');
      oOption.value     = oNecessidade.ed48_i_codigo;

      oOption.addEventListener('click',(event)=>{
        event.stopPropagation();
        $('necessidade').children[0].selected = false;
      });

      oOption.innerHTML = oNecessidade.ed48_i_codigo+' - '+oNecessidade.ed48_c_descr.urlDecode();
      $('necessidade').appendChild(oOption);
    });

    js_buscaSubdivisao();
  }

  function js_buscaSubdivisao() {
    var oParamentro     = new Object();
    oParamentro.exec    = 'pesquisaSubdivisao';
    oParamentro.iNecessidades = $F('necessidade').join(', ');

    js_divCarregando("Aguarde, buscando subdivisões...", "msgBoxSubdivisao");
    new Ajax.Request
      (
        sRPC,
        {
          method:     'post',
          parameters: 'json='+Object.toJSON(oParamentro),
          onComplete: js_retornoSubdivisao
        }
      );
  }

  function js_retornoSubdivisao(oAjax) {
    js_removeObj('msgBoxSubdivisao');
    $('subdivisao').innerHTML = "";
    let oRetorno = eval('('+oAjax.responseText+')');

    let oOption       = document.createElement('option');
    oOption.value     = 0;
    oOption.innerHTML ="TODAS SUBDIVISÕES";
    $('subdivisao').appendChild(oOption);

    if (oRetorno.status == 2) {
      let oOption       = document.createElement('option');
      oOption.value     = 0;
      oOption.innerHTML = oRetorno.message.urlDecode();
      $('subdivisao').appendChild(oOption);
      return false;
    }

    oRetorno.subdivisoes.each(function (oSubdivisao) {
      let oOption       = document.createElement('option');
      oOption.value     = oSubdivisao.ed185_sequencial;
      oOption.innerHTML = oSubdivisao.ed185_necessidade+'.'+oSubdivisao.ed185_sequencial.padStart(2, '0')+' - '+oSubdivisao.ed185_descricao.urlDecode();
      $('subdivisao').appendChild(oOption);
    });
  }

  function js_cleanBlock() {
    $('ano').options[0].selected = true;
    $('fieldset-semestre').setAttribute('style','display:none;');
    $('etapa').innerHTML = '';
    $('etapa').appendChild(oOptionSelecioneEtapa);
    $('imprimir').setAttribute("disabled","disabled");
    $('semestre').innerHTML = '';
    $('semestre').appendChild(oOptionSelecioneSemestre);
    $('etapa').setAttribute("disabled","disabled");
    $('etapa').setAttribute("style","width:100%;height: 361px;");
  }

  function js_imprime() {
      var sUrl  = 'edu2_alunonecessidade002.php?';
      sUrl += 'aEscola='+$F('escolas');
      sUrl += '&iAno='+$F('ano');
      sUrl += '&aSerie='+$F('etapa');
      sUrl += '&iSemestre='+$F('semestre');
      sUrl += '&aNecessidade='+$F('necessidade');
      sUrl += '&iCadeirante='+$F('cadeirante');
      sUrl += '&aSubdivisao='+(empty($F('subdivisao'))?0:$F('subdivisao'));
      sUrl += '&iBPC='+$F('bpc');
      sUrl += '&tipoAtendimento='+$F('tipoAtendimento');
      jan = window.open(sUrl, '', 'width='+(screen.availWidth-5)+', height='+(screen.availHeight-40)+',scrollbars=1,location=0');
      jan.moveTo(0,0);
  }

  function selectAll(event) {
    $('etapa').children[0].selected = false;
    if(event.target.tagName != "OPTGROUP") return;
    var todosClicado = true;
    event.target.childNodes.forEach((opt) => {
      if(!opt.selected) {
        opt.selected = true;
        todosClicado = false;
      }
    });

    if(todosClicado) {
      event.target.childNodes.forEach((opt) => {
        if(opt.selected) {
          opt.selected = false;
        }
      });
    }
  }

  function stopPropagation(event){
    $('etapa').children[0].selected = false;
    event.stopPropagation();
  }

  function js_buscaNecessidadesTipoAtendimentos() {
    
    var oParamentro = new Object();
    oParamentro.exec = 'pesquisaTipoAtendimento';
    oParamentro.order = 'ed186_descricao';    

    js_divCarregando("Aguarde, buscando tipos de atendimento...", "msgBox");
    new Ajax.Request
      (
        'edu2_necessidadeEspecial.RPC.php',   
        {
          method:     'post',
          parameters: 'json='+Object.toJSON(oParamentro),
          onComplete: js_retornoNecessidadesTipoAtendimentos
        }
      );
  }

  function  js_retornoNecessidadesTipoAtendimentos(oAjax) {
    var tiposNecessidades = JSON.parse(oAjax.responseText);
    
    if (tiposNecessidades.erro == true) {
      return;
    }

    tiposNecessidades.tiposAtendimentos.each(function (tipo) {
      let option = document.createElement('option');
      option.value = tipo.sequencial;   
      option.innerHTML = tipo.descricao.urlDecode();
      $('tipoAtendimento').appendChild(option);
    });

  }

  js_buscaEscola();
  js_buscaAno();
  js_buscaNecessidade();
  js_buscaNecessidadesTipoAtendimentos();

</script>
</body>
</html>
