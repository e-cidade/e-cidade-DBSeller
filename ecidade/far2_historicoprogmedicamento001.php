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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta charset="iso-8859-1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link type="text/css" href="grid.style.css" rel="styleshet">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
  <script rel="script" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
  <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
  <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
</head>
<body>
<div class="container">
  <fieldset>
    <legend>Histórico de Retiradas Programa / Medicamento</legend>
    <div id="lancadorProgramas" style="width: 600px;"></div>
    <div id="lancadorMedicamentos" style="width: 600px;"></div>
    <table>
      <tr>
        <td><b>Período: </b></td>
        <td><input id="periodo-inicio" type="text"></td>
        <td><b> Até: </b></td>
        <td><input id="periodo-fim" type="text"></td>
      </tr>
    </table>
  </fieldset>
  <button name="emite2" id="emite2" type="button" onclick="gerarRelatorio();">
    <i class="fas fa-print"></i>
    Imprimir
  </button>
</div>
<?php db_menu(); ?>
</body>
</html>
<script>
document.getElementById('periodo-fim').value = (new Date()).toLocaleDateString('pt-BR');
const periodoInicial = new DBInputDate(document.getElementById('periodo-inicio'));
const periodoFinal = new DBInputDate(document.getElementById('periodo-fim'));

var lancadorProgramas = new DBLancador('lancadorProgramas');
lancadorProgramas.iGridHeight = 100;
lancadorProgramas.selecionarAposPesquisar = true;
lancadorProgramas.setNomeInstancia('lancadorProgramas');
lancadorProgramas.setLabelAncora('Ação Programática:');
lancadorProgramas.setTextoFieldset('Filtrar Programas');
lancadorProgramas.setParametrosPesquisa('func_far_programa.php', ['fa12_i_codigo', 'fa12_c_descricao']);
lancadorProgramas.show(document.getElementById('lancadorProgramas'));

var lancadorMedicamentos = new DBLancador('lancadorMedicamentos');
lancadorMedicamentos.iGridHeight = 100;
lancadorMedicamentos.selecionarAposPesquisar = true;
lancadorMedicamentos.setNomeInstancia('lancadorMedicamentos');
lancadorMedicamentos.setLabelAncora('Medicamento:');
lancadorMedicamentos.setTextoFieldset('Filtrar Medicamentos');
lancadorMedicamentos.setParametrosPesquisa('func_far_matersaude.php', ['fa01_i_codigo', 'm60_descr'], 'lancador');
lancadorMedicamentos.show(document.getElementById('lancadorMedicamentos'));

function validarPeriodo() {
  if (empty(periodoInicial.__toLocaleDateString()) || empty(periodoFinal.__toLocaleDateString())) {
    alert('Necessário informar o período para imprimir!');
    return false;
  }
  if (js_comparadata(periodoInicial.__toLocaleDateString(), periodoFinal.__toLocaleDateString(), '>')) {
    alert('O período inicial não pode ser maior que o período final!');
    return false;
  }

  return true;
}

function validarEnvio(){
  if (lancadorProgramas.getRegistros().length <= 0){
    alert('Selecione ao menos um programa.');
    return false;
  }
  if (!validarPeriodo()) {
    return false;
  }

  return true;
}

function gerarRelatorio() {
  if (!validarEnvio()) {
    return;
  }
  let programas = lancadorProgramas.getRegistros().map(programa => {
    return programa.sCodigo;
  }).join(',');
  let nomesProgramas = lancadorProgramas.getRegistros().map(programa => {
    return programa.sDescricao;
  }).join(',');
  let medicamentos = lancadorMedicamentos.getRegistros().map(programa => {
    return programa.sCodigo;
  }).join(',');

  let get = [
    `programas=${programas}`,
    `nomes_programas=${nomesProgramas}`,
    `medicamentos=${medicamentos}`,
    `periodo_inicio=${js_formatar(periodoInicial.__toLocaleDateString(), 'd')}`,
    `periodo_fim=${js_formatar(periodoFinal.__toLocaleDateString(), 'd')}`
  ];

  let features = 'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0';
  window.open('far2_historicoprogmedicamento002.php?' + get.join('&'), '', features);
}

</script>
