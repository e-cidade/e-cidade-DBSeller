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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$rotulo = new rotulocampo;
$rotulo->label('arquivo');

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("dbmessageBoard.widget.js");
    db_app::load("windowAux.widget.js");
    db_app::load("DBTreeView.widget.js");
    ?>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
</head>
<body style='margin-top: 30px;background-color: #CCCCCC'>

<div class="container" style="height:80%">

    <form method='post' action='' class='container'>
        <table>
            <tr>
                <td>
                    <fieldset>
                        <legend class='bold'>Plano Conta Orçamentário</legend>
                        <div id='ctnArvoreOrcamentario' style="height: 95%; width: 45%;"></div>
                    </fieldset>
                </td>
                <td>
                    <fieldset>
                        <legend class='bold'>Ementário da Receita</legend>
                        <div id='ctnArvoreEmentarioReceita' style="height: 95%; width: 45%;"></div>
                    </fieldset>
                </td>
            </tr>
        </table>
        <input id='btnVincularContas' name='btnVincularContas' type='button' value='Vincular Contas'/>
        <input id='btnEmentario' name='btnEmentario' type='button' value='Nova Conta'/>
        <input id='btnImportarEmentario' name='btnImportarEmentario' type='button' value='Importar Contas Selecionadas do Ementário'/>
    </form>

</div>

<?php db_menu(); ?>
</body>
</html>

<script type="text/javascript">

  var sRPC = 'con1_vinculoementarioreceita.RPC.php';

  var documentHeight = document.body.getHeight();
  var oTreeViewContasOrcamentaria = null;
  var oTreeViewEmentarioReceita = null;


  $('ctnArvoreOrcamentario').style.height = documentHeight - 60;
  $('ctnArvoreOrcamentario').style.width  = document.body.getWidth() / 2.2;


  $('ctnArvoreEmentarioReceita').style.height = documentHeight - 60;
  $('ctnArvoreEmentarioReceita').style.width  = document.body.getWidth() / 2.2;

  function getConPlanoOrcamento()
  {
    var request = new AjaxRequest(
      sRPC,
      {
        exec: 'getPlanosContasOrcamentario'
      },
      function(response) {

        if (response.erro) {
          alert(response.sMensagem);
          return;
        }

        $('ctnArvoreOrcamentario').innerHTML = '';
        oTreeViewContasOrcamentaria = new DBTreeView( 'treeViewContasOrcamentaria' );
        oTreeViewContasOrcamentaria.allowFind( true );
        oTreeViewContasOrcamentaria.setFindOptions( 'matchedonly' );
        oTreeViewContasOrcamentaria.show( $('ctnArvoreOrcamentario') );
        oTreeViewContasOrcamentaria.addNode('0', "Plano Orcamentário - Não configuradas");
        oTreeViewContasOrcamentaria.addNode('1', "Plano Orcamentário - Configuradas");

        for (var index in response.conPlanoOrcamento) {

          for (var x in response.conPlanoOrcamento[index]) {

            var contaOrcamentaria = response.conPlanoOrcamento[index][x];

            if (typeof contaOrcamentaria.c60_estrut === 'undefined') {
              continue;
            }

            var oCheckBox = new Object();
            oCheckBox.checked  = false;
            oCheckBox.disabled = false;

            var oDadosContaOrcamentaria = new Object();
            oDadosContaOrcamentaria.c60_estrut  = contaOrcamentaria.c60_estrut;
            oDadosContaOrcamentaria.c60_codcon = contaOrcamentaria.c60_codcon;
            oDadosContaOrcamentaria.c60_descr = contaOrcamentaria.c60_descr;

            var descricaoConta = contaOrcamentaria.c60_estrut + ' - ' + contaOrcamentaria.c60_descr;
            if (contaOrcamentaria.possui_receita === 't') {
              descricaoConta = "<b>" + descricaoConta + "</b>";
            }

            oTreeViewContasOrcamentaria.addNode(
              contaOrcamentaria.c60_estrut,
              descricaoConta,
              index,
              '',
              '',
              oCheckBox,
              null,
              oDadosContaOrcamentaria
            );
          }
        }
      }
    );

    request
      .setMessage('Aguarde, buscando as contas orçamentárias.')
      .asynchronous(false)
      .execute();
  }

  function getEmentarioReceita()
  {
    var request = new AjaxRequest(
      sRPC,
      {
        exec: 'getEmentarioReceita'
      },
      function(response) {

        if (response.erro) {
          alert(response.sMensagem);
          return;
        }

        $('ctnArvoreEmentarioReceita').innerHTML = '';
        oTreeViewEmentarioReceita = new DBTreeView( 'treeViewEmentarioReceita' );
        oTreeViewEmentarioReceita.allowFind( true );
        oTreeViewEmentarioReceita.setFindOptions( 'matchedonly' );
        oTreeViewEmentarioReceita.show( $('ctnArvoreEmentarioReceita') );
        oTreeViewEmentarioReceita.addNode('0', "Ementário da Receita - Não configuradas");
        oTreeViewEmentarioReceita.addNode('1', "Ementário da Receita - Configuradas");

        for (var index in response.ementarioReceita) {

          for (var x in response.ementarioReceita[index]) {

            var ementarioReceita = response.ementarioReceita[index][x];

            if (typeof ementarioReceita.c95_estrutural === 'undefined') {
              continue;
            }

            var oCheckBox          = new Object();
            oCheckBox.checked  = false;
            oCheckBox.disabled = false;

            var oDadosEmentarioReceita              = new Object();
            oDadosEmentarioReceita.c95_estrutural  = ementarioReceita.c95_estrutural;
            oDadosEmentarioReceita.c95_sequencial = ementarioReceita.c95_sequencial;
            oDadosEmentarioReceita.c95_titulo = ementarioReceita.c95_titulo;

            oTreeViewEmentarioReceita.addNode(
              ementarioReceita.c95_estrutural,
              ementarioReceita.c95_estrutural + ' - ' + ementarioReceita.c95_titulo,
              index,
              '',
              '',
              oCheckBox,
              null,
              oDadosEmentarioReceita
            );
          }
        }
      }
    );

    request
      .setMessage('Aguarde, buscando os ementários da receita.')
      .asynchronous(false)
      .execute();
  }

  function processar()
  {

    var countContasOrcamentaria = oTreeViewContasOrcamentaria.getNodesChecked().length;
    var countEmentarioReceita = oTreeViewEmentarioReceita.getNodesChecked().length;

    if (countContasOrcamentaria > 1) {
      alert('Deve ser selecionado APENAS uma Conta Orçamentária.');
      return false;
    }

    if (countEmentarioReceita > 1) {
      alert('Deve ser selecionado APENAS um Ementário da Receita.');
      return false;
    }

    if (countContasOrcamentaria === 0) {
      alert('Deve ser selecionado uma Conta Orçamentária.');
      return false;
    }

    if (countEmentarioReceita === 0) {
      alert('Deve ser selecionado um Ementário da Receita.');
      return false;
    }

    var contaOrcamentariaNode = oTreeViewContasOrcamentaria.getNodesChecked()[0];
    var ementarioReceitaNode = oTreeViewEmentarioReceita.getNodesChecked()[0];

    var confirmed = confirm("Confirmação do vínculo:\n" +
      contaOrcamentariaNode.c60_estrut + " - " + contaOrcamentariaNode.c60_descr + "\n" +
      ementarioReceitaNode.c95_estrutural + " - " + ementarioReceitaNode.c95_titulo
    );

    if (!confirmed) {
      return false;
    }

    var request = new AjaxRequest(
      sRPC,
      {
        exec: 'processar',
        planocontadetalhe: ementarioReceitaNode.c95_sequencial,
        conplanoorcamento: contaOrcamentariaNode.c60_codcon
      },
      function(response, erro) {

        alert(response.sMensagem);
        if (erro) {
          return false;

        }
        getConPlanoOrcamento();
        getEmentarioReceita();

      }
    );

    request
      .setMessage('Criando vínculo na base de dados.')
      .asynchronous(false)
      .execute();
  }

  function salvarEmentario()
  {
    if (!$('c95_estrutural').value) {
      alert("Deve ser informado o campo Estrutural.");
      return false;
    }

    if ($('c95_estrutural').value.length < 15) {
      alert("Estrutural deve conter 15 caracteres.");
      return false;
    }

    if (!$('c95_titulo').value) {
      alert("Deve ser informado o campo Título.");
      return false;
    }

    if (!$('c95_funcao').value) {
      alert("Deve ser informado o campo Função.");
      return false;
    }

    var naturezasaldo = $('c95_naturezasaldo').value;

    if (naturezasaldo && !isInt(naturezasaldo)) {
      alert("Natureza do Saldo deve ser numérico.");
      return false;
    }

    var request = new AjaxRequest(
      sRPC,
      {
        exec: 'criarEmentarioReceita',
        c95_estrutural: $('c95_estrutural').value,
        c95_titulo: $('c95_titulo').value,
        c95_funcao: $('c95_funcao').value,
        c95_naturezasaldo: $('c95_naturezasaldo').value,
        c95_analitica: $('c95_analitica').value,
        c95_sistema: $('c95_sistema').value
      },
      function(response) {

        alert(response.sMensagem, function() {
          window.location.reload();
        });
      }
    );

    request
      .asynchronous(false)
      .execute();
  }

  function abrirFormularioEmentario()
  {
    var iWidth           = 450;
    var iHeight          = 240;
    var oWindowEmentario = new windowAux('wndEmentario', 'Nova Conta', iWidth, iHeight);

    var sContent  = "  <div id='divEmentario'><center>";
    sContent  += " <fieldset style=\"width: 80%\">";
    sContent  += "<legend class='bold'>Ementário da Receita</legend>";
    sContent  += "<table>";

    sContent  += "<tr>";
    sContent  += "    <td class=\"bold\">Estrutural:</td>";
    sContent  += "    <td><input type=\"text\" name=\"c95_estrutural\" id=\"c95_estrutural\" maxlength='15'></td>";
    sContent  += "</tr>";

    sContent  += "<tr>";
    sContent  += "    <td class=\"bold\">Título:</td>";
    sContent  += "    <td><input type=\"text\" name=\"c95_titulo\" id=\"c95_titulo\" maxlength='200'></td>";
    sContent  += "</tr>";

    sContent  += "<tr>";
    sContent  += "    <td class=\"bold\">Função:</td>";
    sContent  += "    <td><input type=\"text\" name=\"c95_funcao\" id=\"c95_funcao\"></td>";
    sContent  += "</tr>";

    sContent  += "<tr>";
    sContent  += "    <td class=\"bold\">Natureza do Saldo:</td>";
    sContent  += "    <td><input type=\"text\" name=\"c95_naturezasaldo\" id=\"c95_naturezasaldo\" maxlength='4' size='4'></td>";
    sContent  += "</tr>";

    sContent  += "<tr>";
    sContent  += "    <td class=\"bold\">Conta Analítica:</td>";
    sContent  += "    <td><select name=\"c95_analitica\" id=\"c95_analitica\">" +
      "<option value=\"0\">Não</option>" +
      "<option value=\"1\">Sim</option>" +
      "</select></td>";
    sContent  += "</tr>";

    sContent  += "<tr>";
    sContent  += "    <td class=\"bold\">Sistema:</td>";
    sContent  += "    <td><input type=\"text\" name=\"c95_sistema\" id=\"c95_sistema\" maxlength='4' size='4' value=\"0\"></td>";
    sContent  += "</tr>";

    sContent  += "</table>";
    sContent  += "</fieldset>";
    sContent  += "  <br/><input type='button' id='btnSalvarEmentario' name='btnSalvarEmentario' value='Salvar' onclick='salvarEmentario()'></center>";
    sContent  += "  </div>";

    oWindowEmentario.setContent(sContent);
    oWindowEmentario.setShutDownFunction(function (){
      oWindowEmentario.destroy();
    });

    oWindowEmentario.show();
  }

  function isInt(n) {
    return n % 1 === 0;
  }

  function importarContaEmentario()
  {

    var contasSelecionadas = oTreeViewEmentarioReceita.getNodesChecked();
    if (contasSelecionadas.length === 0) {

      alert("Selecione ao menos uma conta do Ementário da Receita para ser importada.");
      return false;
    }


    var contasImportar = [];
    contasSelecionadas.forEach(
      function (conta) {
        contasImportar.push(Number(conta.c95_sequencial));
      }
    );

    AjaxRequest.create(
      sRPC,
      {'exec' : 'importarEmentario', 'contas' : contasImportar},
      function (retorno, erro) {

        alert(retorno.sMensagem);
        if (!erro) {
          window.location.reload();
        }
      }
    ).execute();
  }

  $('btnVincularContas').observe('click', function() {
    processar();
  });

  $('btnEmentario').observe('click', function() {
    abrirFormularioEmentario();
  });

  $('btnImportarEmentario').observe('click', function() {
    importarContaEmentario();
  });

  getConPlanoOrcamento();
  getEmentarioReceita();


</script>
