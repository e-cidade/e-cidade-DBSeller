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

// Busca dados dos tipos de veículos
$tiposVeiculos = array();
$daoVeiccadtipo = new cl_veiccadtipo();
$sqlTiposVeiculos = $daoVeiccadtipo->sql_query_file(
    null,
    "ve20_codigo as codigo, ve20_descr as descricao",
    "ve20_descr");
$rsTiposVeiculos = db_query($sqlTiposVeiculos);

if ($rsTiposVeiculos && pg_num_rows($rsTiposVeiculos) > 0) {
    for ($i = 0; $i < pg_num_rows($rsTiposVeiculos); $i++) {
        $dados = db_utils::fieldsMemory($rsTiposVeiculos, $i);
        $tiposVeiculos[$dados->codigo] = $dados->descricao;
    }
}

// Busca dados das marcas
$marcas = array();
$daoVeiccadmarca = new cl_veiccadmarca();
$sqlMarcas = $daoVeiccadmarca->sql_query_file(
    null,
    "ve21_codigo as codigo, ve21_descr as descricao",
    "ve21_descr");
$rsMarcas = db_query($sqlMarcas);

if ($rsMarcas && pg_num_rows($rsMarcas) > 0) {
    for ($i = 0; $i < pg_num_rows($rsMarcas); $i++) {
        $dados = db_utils::fieldsMemory($rsMarcas, $i);
        $marcas[$dados->codigo] = $dados->descricao;
    }
}

//Busca as cores
$cores = array();
$daoVeiccadcor = new cl_veiccadcor();
$sqlCores = $daoVeiccadcor->sql_query_file(
    null,
    "ve23_codigo as codigo, ve23_descr as descricao",
    "ve23_descr");
$rsCores = db_query($sqlCores);

if ($rsCores && pg_num_rows($rsCores) > 0) {
    for ($i = 0; $i < pg_num_rows($rsCores); $i++) {
        $dados = db_utils::fieldsMemory($rsCores, $i);
        $cores[$dados->codigo] = $dados->descricao;
    }
}

//Busca as procedências
$procedencias = array();
$daoVeiccadproced = new cl_veiccadproced();
$sqlProcedencias = $daoVeiccadproced->sql_query_file(
    null,
    "ve25_codigo as codigo, ve25_descr as descricao",
    "ve25_codigo");
$rsProcedencias = db_query($sqlProcedencias);

if ($rsProcedencias && pg_num_rows($rsProcedencias) > 0) {
    for ($i = 0; $i < pg_num_rows($rsProcedencias); $i++) {
        $dados = db_utils::fieldsMemory($rsProcedencias, $i);
        $procedencias[$dados->codigo] = $dados->descricao;
    }
}

//Busca as categorias
$categorias = array();
$daoVeiccadcateg = new cl_veiccadcateg();
$sqlCategorias = $daoVeiccadcateg->sql_query_file(
    null,
    "ve32_codigo as codigo, ve32_descr as descricao",
    "ve32_codigo");
$rsCategorias = db_query($sqlCategorias);

if ($rsCategorias && pg_num_rows($rsCategorias) > 0) {
    for ($i = 0; $i < pg_num_rows($rsCategorias); $i++) {
        $dados = db_utils::fieldsMemory($rsCategorias, $i);
        $categorias[$dados->codigo] = $dados->descricao;
    }
}

?>
<html>
<head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBInputHora.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <style type="text/css">
      .campo_opcional {
        background-color:#E6E4F1;
      }
    </style>
</head>
<body class="body-default abas">
	<div id="conteudo_abas" class="container"></div>

	<div id="abaInscricao" class="container">
	  <?php include modification("forms/db_frminscricaoveiculos_aba_incricao.php"); ?>
  </div>

  <div id="abaCondutores"  class="container">
  	<?php include modification("forms/db_frminscricaoveiculos_aba_condutores.php"); ?>
  </div>

  <div id="abaAtividades" class="container">
    <?php include modification("forms/db_frminscricaoveiculos_aba_atividades.php"); ?>
  	<?php //include modification("iss1_tabativ004.php"); ?>
  </div>
</body>
</html>
<script type="text/javascript">
	const url = "<?php echo ECIDADE_REQUEST_PATH;?>",
	apiUrl = `${url}v4/api/`,
	urlRpc = 'iss04_inscricaoveiculo.RPC.php';

  	var oDBAbas = new DBAbas($('conteudo_abas'));
  	var abaInscricao = oDBAbas.adicionarAba('Inscrição' , $('abaInscricao'));
  	var abaCondutoresAuxiliares = oDBAbas.adicionarAba('Condutores Auxiliares', $('abaCondutores'));
  	abaCondutoresAuxiliares.id = 'abaCondutoresAuxiliares';
  	var abaAtividades = oDBAbas.adicionarAba('Atividades', $('abaAtividades'));

  	if ($('db_opcao').value == '1') {
  		abaCondutoresAuxiliares.bloquear();
  		abaAtividades.bloquear();
  	} else {
      $('pesquisarInscricao').disabled = false;
      $('pesquisarInscricao').dispatchEvent(new Event('click'));
    }

  	if ($('db_opcao').value == '3' || $('db_opcao').value == '4') {
      setFormReadOnly($('frmInscricao'), true);
      setFormReadOnly($('frmCondutores'), true);
      setFormReadOnly($('form_atividade'), true);
      $('salvarInscricao').style.display = 'none';
      $('adicionarCondutor').style.display = 'none';
      $('limparCondutor').style.display = 'none';
      $('excluirInscricao').style.display = '';
      $('excluirInscricao').disabled = false;
      $('pesquisarInscricao').disabled = false;
  	}

    if ($('db_opcao').value == '4') {
      $('excluirInscricao').style.display = 'none';
      $('linhaTipoAlvara').style.display = '';
      $('imprimirBIC').style.display = '';
      $('imprimirBIC').disabled = false;
    }

</script>