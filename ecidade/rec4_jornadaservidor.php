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
require_once(modification("libs/db_conecta_plugin.php"));
include_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$aFiltro = array(
  'S'=>'Seleção',
  'M'=>'Matrícula'
);

?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <style type="text/css">
    .container {
      max-width: 600px;
    }
    #linhaMatricula,
    #ctnLancadorMatricula {
      display: none;
    }

  </style>
</head>

<body bgcolor="#cccccc" style='margin-top: 30px'>

<div id="ctnAlterarJornadaServidor" class="container">

  <fieldset>
    <legend>Alterar Jornada do Funcionário</legend>

    <form id="lancarJornada">
      <table>
        <tr>
          <td>
            <label for="rh212_data_inicio"><strong>Período:</strong></label>
          </td>
          <td colspan="3">
            <input type="text" maxlength="10" size="10" value="" id="rh212_data_inicio" name="rh212_data_inicio" title="Data inicial do período:rh212_data_inicio" />
            <label for="rh212_data_fim"><strong>Até:</strong></label>
            <input type="text" maxlength="10" size="10" value="" id="rh212_data_fim" name="rh212_data_fim" title="Data final do período:rh212_data_fim" />
          </td>
        </tr>
        <tr>
          <td>
            <label for="rh212_jornada"><a href="#" id="jornada"><strong>Jornada:</strong></a></label>
          </td>
          <td colspan="3">
            <?php db_input('rh212_jornada',     10, 3, true, 'text', 1, 'lang="rh188_sequencial" class="field-size2" autocomplete="off"  title="Código da Jornada:rh212_jornada"') ?>
            <?php db_input('descricao_jornada', 50, 3, true, 'text', 3, 'lang="rh188_descricao" class="field-size9"') ?>
          </td>
        </tr>
        <tr>
          <td>
            <label for="tipo_jornada"><strong>Tipo da Jornada:</strong></label>
          </td>
          <td colspan="3">
            <?php db_input('tipo_jornada', 30, 3, true, 'text', 3, 'class="field-size5"') ?>
          </td>
        </tr>
        <tr id="linhafiltroServidores">
          <td>
            <label for="filtroServidores" class="bold">Filtro:</label>
          </td>
          <td colspan="3">
            <?php db_select('filtroServidores', $aFiltro, '', 1) ?><br/>
          </td>
        </tr>

        <tr id="linhaSelecao">
          <td>
            <label for="r44_selec"><a href="#" id="selecao"><b>Seleção:<b></a></label>
          </td>
          <td colspan="3">
            <?php db_input('r44_selec', 10, 1, true, 'text', 1, 'class="field-size2"') ?>
            <?php db_input('r44_descr', 30, 3, true, 'text', 3, 'class="field-size9"') ?>
          </td>
        </tr>

        <tr id="linhaMatricula">
          <td>
            <label for="rh212_matricula" id="matricula"><b>Matrícula:<b></label>
          </td>
          <td colspan="3">
            <?php db_input('rh212_matricula', 10, 1, true, 'text', 1, 'lang="rh01_regist" class="field-size2"') ?>
            <?php db_input('z01_nome',        10, 1, true, 'text', 1, 'class="field-size9"') ?>
          </td>
        </tr>
      </table>
      <div id="ctnLancadorMatricula"></div>
    </form>
  </fieldset>

  <input type="button" name="salvarJornada"    id="salvarJornada"    value="Salvar" />
  <input type="button" name="excluirJornada"   id="excluirJornada"   value="Excluir" />
</div>
</body>
</html>