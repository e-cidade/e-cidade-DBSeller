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


require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oPost = db_utils::postMemory($HTTP_POST_VARS);
$oGet = db_utils::postMemory($HTTP_GET_VARS);

$oDaoObrasConstr = new cl_obrasconstr;
$oDaoObrasEnder = new cl_obrasender;
$oDaoParProjetos = new cl_parprojetos;
$oDaoCaracter = new cl_caracter;

$clcaracter = new cl_caracter;
$oRotulo = new rotulocampo;

$oDaoObrasConstr->rotulo->label();
$oDaoObrasEnder->rotulo->label();

$sSqlParProjetos = $oDaoParProjetos->sql_query_pesquisaParametros(db_getsession("DB_anousu"));

$rsParProjetos = $oDaoParProjetos->sql_record($sSqlParProjetos);

if ($oDaoParProjetos->numrows > 0) {
    $oParProjetos = db_utils::fieldsMemory($rsParProjetos, 0);
}

$ob08_ocupacao = null;
$ob08_tipoconstr = null;
$ob08_tipolanc = null;


if ($oDaoObrasConstr->numrows > 0 && $oDaoObrasEnder->numrows > 0) {
    $oObrasConstr = db_utils::fieldsMemory($rsObrasConstr, 0);
    $oObrasEnder = db_utils::fieldsMemory($rsObrasEnder, 0);

    $ob08_ocupacao = $oObrasConstr->ob08_ocupacao;
    $ob08_tipoconstr = $oObrasConstr->ob08_tipoconstr;
    $ob08_tipolanc = $oObrasConstr->ob08_tipolanc;
}

/**
 * Dados para os Combos das caracteristicas
 */
$sSqlCaracterOcupacao = $oDaoCaracter->sql_query("", "j31_codigo, j31_descr", "j31_codigo",
  " j32_grupo = {$oParProjetos->ob21_grupotipoocupacao}");
$sSqlCaracterConstrucao = $oDaoCaracter->sql_query("", "j31_codigo, j31_descr", "j31_codigo",
  " j32_grupo = {$oParProjetos->ob21_grupotipoconstrucao}");
$sSqlCaracterLancamento = $oDaoCaracter->sql_query("", "j31_codigo, j31_descr", "j31_codigo",
  " j32_grupo = {$oParProjetos->ob21_grupotipolancamento}");

$rsCaracterOcupacao = $oDaoCaracter->sql_record($sSqlCaracterOcupacao);
$rsCaracterConstrucao = $oDaoCaracter->sql_record($sSqlCaracterConstrucao);
$rsCaracterLancamento = $oDaoCaracter->sql_record($sSqlCaracterLancamento);

$codigoobra = isset($codigoobra) ? $codigoobra : '';

?>

<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBInputHora.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link href="grid.style.css" rel="stylesheet" type="text/css">
  <title>DBSeller Sistemas Integrados</title>
</head>
<body>
<div class="container">
  <form name="formEvento" id="formEvento"><br/>
    <fieldset>
      <legend class="bold">Área Complementar</legend>
      <input type="hidden" value="" id="sequencialEvento" readonly/>
      <table class="form-container">
        <tr>
          <td>
            <input type="hidden" id="obra" value="<?=$codigoobra;?>"/>
            <label for="descricao">Descrição:</label>
          </td>
          <td>
            <input id="descricao" class="field-size7" onkeyup="altrarMaiusculo(this)" />
          </td>
        </tr>
        <tr>
          <td>
            <label for="medida">Área Total:</label>
          </td>
          <td>
            <input id="medida" class="readonly field-size2" />
          </td>
        </tr>
        <tr>
          <td>
            <label for="medidacoberta">Área Coberta:</label>
          </td>
          <td>
            <input id="medidacoberta" onkeypress='return campoSomenteNumero(event)' class="field-size2" />
          </td>
        </tr>
        <tr>
          <td>
            <label for="medidadescoberta">Área Descoberta:</label>
          </td>
          <td>
            <input id="medidadescoberta" onkeypress='return campoSomenteNumero(event)' class="field-size2" />
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= $Tob08_ocupacao ?>">
              <?= $Lob08_ocupacao ?>
          </td>
          <td>
              <?php
              db_selectrecord("ob08_ocupacao", $rsCaracterOcupacao, true, 1, "", "ob08_ocupacao");
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= $Tob08_tipoconstr ?>">
              <?= $Lob08_tipoconstr ?>
          </td>
          <td>
              <?php
              db_selectrecord("ob08_tipoconstr", $rsCaracterConstrucao, true, 1, "", "ob08_tipoconstr");
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= $Tob08_tipolanc ?>">
              <?= $Lob08_tipolanc ?>
          </td>
          <td>
              <?php
              db_selectrecord("ob08_tipolanc", $rsCaracterLancamento, true, 1, "", "ob08_tipolanc");
              ?>
          </td>
        </tr>
        <tr>
          <td>
            <label for="tipoareacomplementar">Tipo de Área Complementar:</label>
          </td>
          <td>
            <select id="tipoareacomplementar" class="field-size7">
              <option value="1" selected>QUADRA</option>
              <option value="2">ESTACIONAMENTO TÉRREO</option>
              <option value="3">PISCINA</option>
              <option value="4">ÁREA POSTO GASOLINA</option>
            </select>
          </td>
        </tr>
      </table>
    </fieldset>
    <input type="button" value="Enviar" id="btnSalvar" onclick="salvarEvento()"/>
    <input type="button" value="Limpar" id="btnLimpar" onclick="limparCampos()"/>
  </form>
</div>

<div class="container">
  <fieldset style="width: 900px">
    <legend class="bold">Áreas</legend>
    <div id="ctnGridEventos"></div>
  </fieldset>
</div>

<?php db_menu(); ?>

</body>
</html>

<script type="text/javascript">

  const RPC_AREAS_COMPLEMENTARES = 'pro4_areascomplementares.RPC.php'

  require_once("scripts/widgets/DBLookUp.widget.js");

  var input = {
    sequencial: $('sequencialEvento'),
    descricao: $('descricao'),
    medida: $('medida'),
    medidacoberta: $('medidacoberta'),
    medidadescoberta: $('medidadescoberta'),
    ocupacao: $('ob08_ocupacao'),
    tipoconstrucao: $('ob08_tipoconstr'),
    tipolancamento: $('ob08_tipolanc'),
    tipoareacomplementar: $('tipoareacomplementar'),
    obra: $('obra')
  };

  var eventosCadastrados = new Collection();
  eventosCadastrados.setId('sequencial');

  var gridEventosCadastrados = new DatagridCollection(eventosCadastrados, 'gridMatriculas').configure({'order': false});

  gridEventos()

  function gridEventos() {

    gridEventosCadastrados.addColumn('codigo', {width: '20%', 'label': 'Código'});
    gridEventosCadastrados.addColumn('descricao', {width: '45%', 'label': 'Descrição'});
    gridEventosCadastrados.addColumn('medida', {width: '15%', 'label': "Medida"});
    gridEventosCadastrados.addColumn('tipoAreaComplementarDescricao', {width: '30%', 'label': "Tipo Área Complementar"});
    gridEventosCadastrados.configure({'height': '200px'});
    gridEventosCadastrados.hideColumns([0]);
    gridEventosCadastrados.addAction('A', 'Alterar', function(evento, collectionLinha) {
      preencheCampos(collectionLinha);
    });

    gridEventosCadastrados.addAction('E', 'Excluir', function(evento, collectionLinha) {
      exluiCampos(collectionLinha)
    });
    gridEventosCadastrados.show($('ctnGridEventos'));

    if($F('obra') !== '') {

      getEventosCadastrados();
    }
  }

  function limparCampos() {
    input.descricao.value = ''
    input.medida.value = ''
    input.medidacoberta.value = ''
    input.medidadescoberta.value = ''
    input.sequencial.value = ''
  }

  function salvarEvento() {
    
    if ( empty ($F('medidacoberta' ))) {
      $('medidacoberta').value = 0
    }

    if ( empty ($F('medidadescoberta' ))) {
      $('medidadescoberta').value = 0
    }
    
    if( $F('descricao').trim() === '') {
      return alert('Campo Descrição deve ser preenchido.');
    }

    var evento = {
      sequencial: input.sequencial.value,
      descricao: input.descricao.value,
      medida: input.medida.value,
      medidaAreaCoberta: input.medidacoberta.value,
      medidaAreaDescoberta: input.medidadescoberta.value,
      ocupacao: input.ocupacao.value,
      tipoConstrucao: input.tipoconstrucao.value,
      tipoLancamento: input.tipolancamento.value,
      tipoAreaComplementar: input.tipoareacomplementar.value,
      obra: input.obra.value
    };

    AjaxRequest.create(
      RPC_AREAS_COMPLEMENTARES,
      {'executa': 'salvar', evento: evento},
      function(retorno, erro) {

        alert(retorno.mensagem);

        if(erro === true) {
          return false;
        }

        getEventosCadastrados();        
      }
    ).setMessage('Aguarde, salvando área complementar...').execute();
  }

  function getEventosCadastrados() {

    AjaxRequest.create(
      RPC_AREAS_COMPLEMENTARES,
      {
        'executa': 'buscar',
        'obra': $F('obra')
      },
      function(retorno, erro) {

        if(erro === true) {
          return alert(retorno.mensagem);
        }

        gridEventosCadastrados.clear();
        
        if(retorno.areasComplementares.length === 0) {
          return false;
        }

        retorno.areasComplementares.each(
          function(evento) {        

            var areaCoberta = new Number(evento.medidaAreaCoberta);
            var areaDescoberta = new Number(evento.medidaAreaDescoberta);

            var eventoGrid = {
              sequencial: evento.sequencial,
              descricao: evento.descricao,
              medida: areaCoberta + areaDescoberta,
              medidaAreaCoberta: evento.medidaAreaCoberta,
              medidaAreaDescoberta: evento.medidaAreaDescoberta,
              tipoAreaComplementar: evento.tipoAreaComplementar,
              tipoAreaComplementarDescricao: evento.tipoAreaComplementarDescricao,
              ocupacao: evento.ocupacao,
              tipoConstrucao: evento.tipoConstrucao,
              tipoLancamento: evento.tipoLancamento
            };

            eventosCadastrados.add(eventoGrid);
          }
        );
        gridEventosCadastrados.reload()
        limparCampos()
      }
    ).setMessage('Aguarde, carregando áreas complementares cadastradas...').execute();       
  }

  function exluiCampos(collectionLinha) {
    
    if (!confirm('Confirma a exclusão da área complementar?')) {
      return false;
    }    

    AjaxRequest.create(
      RPC_AREAS_COMPLEMENTARES,
      {
        'executa':'excluir',
        'sequencial': collectionLinha.sequencial
      },
      
      function (retorno, erro) {
        alert(retorno.mensagem);
        if(erro === true) {
          return false;
        }
        getEventosCadastrados()
      }
    ).setMessage('Aguarde, excluindo área complementar...').execute();
  }

  somaMedidas()

  function somaMedidas() {

    document.getElementById('medida').readOnly = 'true'
    $('medidacoberta').observe('change', function() {
      let medidaDescoberta = new Number($('medidadescoberta').value);
      let medidaCoberta = new Number($('medidacoberta').value);
      $('medida').value = medidaCoberta + medidaDescoberta;
    });
    $('medidadescoberta').observe('change', function() {
      let medidaDescoberta = new Number($('medidadescoberta').value);
      let medidaCoberta = new Number($('medidacoberta').value);
      $('medida').value = medidaCoberta + medidaDescoberta;
    });
  }

  function campoSomenteNumero(e) {

    var tecla = (window.event) ? event.keyCode : e.which
    if((tecla > 47 && tecla < 58)) return true
    else {
      if(tecla == 8 || tecla == 0 || tecla == 13 || tecla == 46) {
        return true
      }      
      else {
        alert('Campos de medida devem ser preenchidos somente com números decimais!');
        input.medidacoberta.value = ''
        input.medidadescoberta.value = ''
        input.medida.value = ''
        return false
      }
    }
  } 

  function preencheCampos(collectionLinha) {
    var areaCoberta = new Number(collectionLinha.medidaAreaCoberta);
    var areaDescoberta = new Number(collectionLinha.medidaAreaDescoberta);

    $('sequencialEvento').value = collectionLinha.sequencial;
    $('descricao').value = collectionLinha.descricao;
    $('medida').value = areaCoberta + areaDescoberta;
    $('medidacoberta').value = collectionLinha.medidaAreaCoberta;
    $('medidadescoberta').value = collectionLinha.medidaAreaDescoberta;
    $('ob08_ocupacao').value = collectionLinha.ocupacao;
    $('ob08_ocupacaodescr').value = collectionLinha.ocupacao;
    $('ob08_tipoconstr').value = collectionLinha.tipoConstrucao;
    $('ob08_tipoconstrdescr').value = collectionLinha.tipoConstrucao;
    $('ob08_tipolanc').value = collectionLinha.tipoLancamento;
    $('ob08_tipolancdescr').value = collectionLinha.tipoLancamento;
    $('tipoareacomplementar').value = collectionLinha.tipoAreaComplementar;
  }

  function altrarMaiusculo(l) {
    valor = l.value.toUpperCase()
    l.value = valor
  }

  $('ob08_ocupacao').setStyle({'width' : '60px'});
  $('ob08_ocupacaodescr').setStyle({'width' : '235px'});
  $('ob08_tipoconstr').setStyle({'width' : '60px'});
  $('ob08_tipoconstrdescr').setStyle({'width' : '235px'});
  $('ob08_tipolanc').setStyle({'width' : '60px'});
  $('ob08_tipolancdescr').setStyle({'width' : '235px'});
</script>