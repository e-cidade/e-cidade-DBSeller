<?php

/*
 *  E-cidade Software Publico para Gestao Municipal
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
require_once(modification("classes/db_atividaderh_classe.php"));

$nomeEscola = db_getsession("DB_nomedepto");
$escola = db_getsession("DB_coddepto");
$modulo = db_getsession("DB_modulo");

$clatividaderh = new cl_atividaderh();

define('MODULO_SECRETARIA', 7159);
db_postmemory($_POST);

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
  <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
 </head>
 <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="" >
   <form name="form1" method="post" action="" class="container">
        <fieldset ><legend><b>Relatório de Profissionais com Certificações Anexadas</b></legend>
            <table class='form-container' style="width: 500px;">
                <tr>    
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6">
                        <fieldset>
                            <legend><b>Função Exercida:</b></legend>
                            <select name="funcao_exercida" id="funcao_exercida" 
                                style="font-size:9px;width:100%;height:180px;" multiple>
                            <?php
                            $campos = "ed01_i_codigo, ed01_c_descr";
                            $sqlFuncoes = $clatividaderh->sql_query($ed01_i_codigo, $campos, "ed01_c_descr");
                            $resultFuncoes = db_query($sqlFuncoes);
                            $funcoesCollection = db_utils::getCollectionByRecord($resultFuncoes);

                            foreach ($funcoesCollection as $funcao) {
                                echo "<option value='{$funcao->ed01_i_codigo}'>{$funcao->ed01_c_descr}</option>";
                            }
                            ?>
                            </select>
                            <fieldset style="width:95%;margin-top:6px;">
                                Para selecionar mais de uma função exercida mantenha pressionada a tecla CTRL
                                <br>e clique sobre o nome da função.
                            </fieldset>
                        </fieldset>
                    </td>
                </tr>
                <tr>    
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6">
                        <fieldset>
                            <legend><b>Ano:</b></legend>
                            <input type="text" name="ano_filtro" id="ano_filtro" class="field-size2" value="<?= db_getsession("DB_anousu") ?>" />
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>         
            </table>
      
        <fieldset>
            <legend><b>Unidades de Ensino</b></legend>
            <div id="ctnEscolas">
            </div>
        </fieldset>
    </fieldset>
      <button type="button" id="imprimir" onclick="js_valida()">
          <i class="fa fa-print"></i>
          Imprimir
      </button>
  </form>
<?php
db_menu();
?>
 </body>
</html>
<script>

sUrlRPC = 'edu4_escola.RPC.php';

function js_valida() 
{
    if (document.form1.ano_filtro.value.trim() == "") {
        alert("Informe o ano!");
        return false;
    }

    if (!isNaN(document.form1.ano_filtro.value) === false) {
        alert("O ano precisa ser um número!");
        return false;
    }

    if (oDataGridEscola.getSelection("object").length == 0) {
        alert('Nenhuma unidade de ensino selecionada.');
        return false;
    }

    js_emite();
}


function js_pesquisaEscola() 
{
    var oParametro = {};
    oParametro.exec = 'getEscola';
    oParametro.filtraModulo = true;
    oParametro.ordemAlfabetica = true;

    var oAjax = new Ajax.Request(
        sUrlRPC,
        {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: js_retornaPesquisaEscola
        }
    );
}

function js_retornaPesquisaEscola(oResponse) 
{
    var oRetorno = JSON.parse(oResponse.responseText);
    oDataGridEscola.clearAll(true);
    oRetorno.itens.each(function (oLinha, iContador) {
        var aLinha = [];

        aLinha[0] = oLinha.codigo_escola;
        aLinha[1] = oLinha.nome_escola.urlDecode();
        if (oRetorno.iTotalLinhas == 1) {
            oDataGridEscola.addRow(aLinha, false, false, true);
        } else {
            oDataGridEscola.addRow(aLinha);
        }
    });

    oDataGridEscola.renderRows();
}

function js_emite()
{
    const linhas = oDataGridEscola.getSelection("object");
    const escolas = [];
    linhas.each(function (linha) {
        escolas.push(linha.aCells[0].getValue());
    });

    const filtros = {
        "ano" : $F('ano_filtro'),
        "escolas": escolas,
        "funcao": $F('funcao_exercida')
    }

    var sUrl  = 'edu2_profissionaiscertificacoes002.php?filtros=' + btoa(JSON.stringify(filtros));

    jan = window.open(sUrl);
    jan.moveTo(0,0);
}

function js_gridEscola() 
{
    oDataGridEscola = new DBGrid("gridEscola");
    oDataGridEscola.nameInstance = 'oDataGridEscola';
    oDataGridEscola.setCheckbox(0);
    oDataGridEscola.setCellAlign(new Array("center", "left"));
    oDataGridEscola.setHeader(new Array("Código", "Nome"));
    oDataGridEscola.setCellWidth(new Array("20%", "80%"));
    oDataGridEscola.show($('ctnEscolas'));
}

js_gridEscola();
js_pesquisaEscola();

</script>