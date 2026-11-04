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

require_once('libs/db_stdlib.php');
require_once('libs/db_conecta_plugin.php');
require_once('libs/db_sessoes.php');
require_once('libs/db_utils.php');
require_once('libs/db_app.utils.php');
require_once('dbforms/db_funcoes.php');

$oRotulo = new rotulocampo();
$oRotulo->label("ac16_sequencial");
$oRotulo->label("ac16_resumoobjeto");
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">

<div class="container">

  <?php if (db_getsession('DB_login') != 'dbseller') : ?>
    <p>Rotina habilitada somente para o usuário DBSeller.</p>
  <?php else : ?>

  <form name="form1" method="post">

    <fieldset>
      <legend>Excluir Aditamento</legend>
      <table>

        <tr>
          <td>
            <label class="bold" for="ac16_sequencial">
              <a id="acordo_ancora">Acordo:</a>
            </label>
          </td>

          <td>
            <?php
            db_input('ac16_sequencial', 10, $Iac16_sequencial, true, 'text', 1);
            db_input('ac16_resumoobjeto', 40, $Iac16_resumoobjeto, true, 'text', 3);
            ?>
          </td>
        </tr>

      </table>

      <fieldset style="margin-top: 10px;">
        <legend>Aditamentos</legend>
        <div style="width: 600px" id="container-aditamentos"></div>
      </fieldset>

    </fieldset>

  </form>

</div>


<script type="text/javascript">
  (function() {
    var oAcordoAncora    = $('acordo_ancora');
    var oAcordoCodigo    = $('ac16_sequencial');
    var oAcordoDescricao = $('ac16_resumoobjeto');

    function buscarAditamentos() {
      var oParametros = {
        "exec"            : "aditamentosConsulta",
        "ac16_sequencial" : oAcordoCodigo.value,
        "detalhe"         : "aditamentos"
      };
      new AjaxRequest("ac4_acordoconsulta.RPC.php", oParametros, function(oRetorno, lErro){
        oCollectionAditamentos.clear();
        for (oAditamento of oRetorno.dados) {
          oCollectionAditamentos.add({
            codigo    : oAditamento.codigo,
            descricao : oAditamento.situacao.urlDecode(),
            vigencia  : oAditamento.vigencia.urlDecode()
          });
        }
        oGridAditamentos.reload();
      }).execute();
    }
    var oLookUpAcordo = new DBLookUp(oAcordoAncora, oAcordoCodigo, oAcordoDescricao, {
      "sArquivo"              : "func_acordo.php",
      "sObjetoLookUp"         : "db_iframe_acordo",
      "sLabel"                : "Pesquisar Acordo",
      "aParametrosAdicionais" : ["descricao=true", "lDepartamento=true"],
      "fCallBack" : function() {

        oCollectionAditamentos.clear();
        oGridAditamentos.reload();
        if (oAcordoCodigo.value) {
          buscarAditamentos();
        }
      }
    });

    oAcordoCodigo.observe('change', function () {

      oCollectionAditamentos.clear();
      oGridAditamentos.reload();
    });



    var oCollectionAditamentos = new Collection().setId("codigo");
    var oGridAditamentos       = new DatagridCollection(oCollectionAditamentos).configure({
      order : false,
      height : 200
    });
    oGridAditamentos.addColumn("codigo", {
      label : "Código",
      width : "10%",
      align : "center"
    });
    oGridAditamentos.addColumn("descricao", {
      label : "Descrição",
      align : "left",
      width : "40%"
    });
    oGridAditamentos.addColumn("vigencia", {
      label : "Vigência",
      align : "left",
      width : "30%"
    });
    oGridAditamentos.addAction("Excluir", null, function(oEvento, oItem) {

      var sMensagemExcluir = 'Essa operação não pode ser desfeita.\n' +
      'O Evento gerado por esse aditamento também será excluído.\n' +
      'Deseja prosseguir e excluir a posição ' + oItem.codigo +
      ' do acordo ' + oAcordoCodigo.value + '?';
      if (!confirm(sMensagemExcluir)) {
        return;
      }

      var oParametros = {
        "exec"            : "excluirAditamento",
        "iAcordoPosicao"  : oItem.codigo
      };
      new AjaxRequest("aco4_excluiraditamento.RPC.php", oParametros, function(oRetorno, lErro){

        alert(oRetorno.message.urlDecode());
        if (lErro) {
          return;
        }

        buscarAditamentos();
      }).execute();
    });

    oGridAditamentos.show($("container-aditamentos"));
  })();
</script>
<?php endif ?>
<?php db_menu(); ?>
</body>
</html>