<?php
/*
 *
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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

$cltabrec           = new cl_tabrec;
$clretencaotipocalc = new cl_retencaotipocalc;
$cltabrec->rotulo->label();
$clretencaotipocalc->rotulo->label();

?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
      .grid {
        width: 800px;
      }

      .avisoLinha {
        display: none;
      }

      .avisoLinha span{
        width: 25px;
        height: 14px;
        background: #f78481;
        float: left;
        margin: 0 5px 0 0;
      }

      .notificacao {
        display:block;
        text-align: left;
        background-color: #fcf8e3;
        border: 1px solid #fcc888;
        padding: 5px;
        width: calc(100% - 10px);
        margin-bottom: 5px;
      }

      .notificacao  p {
        margin: 2px;
      }

    </style>
  </head>
  <body>
    <form action="" method="post" class="container">
      <fieldset>
        <legend>Receitas para retenções por tipo de cálculo</legend>
        <table class="form-container">
          <tr>
              <td>
                  <label id="lblTipocalculo" for="e32_sequencial">Tipo de cálculo:</label>
              </td>
              <td colspan="3">
                   <?php
                   db_input('e32_sequencial', 10, $Ie32_sequencial, true, 'text', 3);
                   db_input('e32_descricao', 40, $Ie32_descricao, true, 'text', 3);
                   ?>
              </td>
          </tr>
          <tr>
            <td>
              <label id="lblReceita" for="k02_codigo">Receita:</label>
            </td>
            <td colspan="3">
              <?php
                db_input('k02_codigo', 10, $Ik02_descr, true, 'text', 1);
                db_input('k02_descr',  40, $Ik02_descr, true, 'text', 3);
              ?>
            </td>
          </tr>

        </table>
      </fieldset>
      <input type="button" id="salvar" value="Salvar">
    </form>

    <fieldset class="container grid">
      <div id="gridRegistros"></div>
    </fieldset>

    <?php
      db_menu();
    ?>
    <script type="text/javascript">

      var oCollectionRegistros;
      var oGridRegistros;
      var sUrlRpc = 'emp4_retencaotipocalcreceitas.RPC.php';

      $('salvar').addEventListener('click', salvarConfiguracoes, false);

      (function() {

        var oReceita     = new DBLookUp($('lblReceita'),     $('k02_codigo'),     $('k02_descr'),     {sArquivo: 'func_tabrec.php'} );
        var oTipocalculo = new DBLookUp($('lblTipocalculo'), $('e32_sequencial'), $('e32_descricao'), {sArquivo: 'func_retencaotipocalc.php' ,
            fCallBack: function () {
              getConfiguracoesReceitas(arguments[0]);
            }
          }
        );
        montaGrid();
      })();

      function getConfiguracoesReceitas(iTipoCalculo){

        var oDados = {
          exec: 'getConfiguracoesReceitas',
          oParams: {
            iTipocalc: iTipoCalculo
          }
        };

        console.log(oDados);

        var oAjaxRequest = new AjaxRequest(sUrlRpc, oDados, function(oRetorno, lErro) {

          oGridRegistros.clear();
          oRetorno.aConfiguracoes.forEach(function (oConf) {
              oCollectionRegistros.add(
                {
                  sequencial: oConf.id,
                  receita: oConf.codigo_receita,
                  descricao_receita: oConf.descricao_receita,
                  tipo_calculo: oConf.descricao_tipocalculo
                });
          });
          oGridRegistros.reload();

        }).setMessage("Buscando configurações cadastradas...").execute();
      }

      function montaGrid() {

        oCollectionRegistros = new Collection().setId('sequencial');
        oGridRegistros = DatagridCollection.create(oCollectionRegistros).configure("order", false);

        oGridRegistros.addColumn("receita",            {label: "Cód. Receita",      align: "center", width: "80px"  });
        oGridRegistros.addColumn("descricao_receita",  {label: "Descrição Receita", align: "left",   width: "300px" });
        oGridRegistros.addColumn("tipo_calculo",       {label: "Tipo de Cálculo",   align: "left",   width: "120px" });
        oGridRegistros.addAction("Excluir", "Clique para excluir o registro",function (event, oItem){
           excluir(oItem.sequencial);
        });

        oGridRegistros.show($('gridRegistros'));
      }

      function salvarConfiguracoes() {

        var codigoReceita    = $F('k02_codigo');
        var codigoTipoCalulo = $F('e32_sequencial');

        if ( codigoReceita == ""){
          alert("Selecione a receita antes de salvar.");
        }

        if (codigoTipoCalulo == ""){
          alert("Selecione o tipo de calculo antes de salvar.")
        }

        var oDados = {
                        exec: 'salvar',
                        oParams: {
                          codigo_receita:     codigoReceita,
                          codigo_tipocalculo: codigoTipoCalulo
                        }
                     };

        var oAjax = new AjaxRequest(sUrlRpc, oDados, function(oRetorno, lErro) {

          alert(oRetorno.message.urlDecode());

          getConfiguracoesReceitas($F('e32_sequencial'));
          limpaCampos();

        }).setMessage('Salvando Configuração de receita por tipo de calculo...').execute();

        return false;
      }

      function excluir(iSequencial) {

        if ( iSequencial == null ) {
          alert("Sequencial não encontrado para exclusão.");
        }

        if ( ! confirm("Você realmente deseja excluir esta configuração?")){
          return false;
        }

        var oDados = {
          exec: 'excluir',
          oParams: {
            id: iSequencial
          }
        };

        var oAjax = new AjaxRequest(sUrlRpc, oDados, function(oRetorno, lErro) {
            alert(oRetorno.message.urlDecode());
            getConfiguracoesReceitas($F('e32_sequencial'));
        }).setMessage('Excluindo registro selecionado...').execute();

      }
      function limpaCampos(){

        $('k02_codigo').value = '';
        $('k02_descr').value  = '';

      }

    </script>
  </body>
</html>
