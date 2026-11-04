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
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/db_stdClass.php"));

$clrotulo = new rotulocampo;
$clrotulo->label("ac16_sequencial");
$clrotulo->label("ac16_resumoobjeto");
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    // Includes padrão
    db_app::load("scripts.js, prototype.js, strings.js, datagrid.widget.js, webseller.js");
    db_app::load("estilos.css, grid.style.css");
    ?>
</head>
<body bgcolor="#cccccc" style='margin-top: 30px'>
<?php //MsgAviso(db_getsession("DB_coddepto"),"escola");?>
<center>
    <div id='divContainer' style="width: 500px;">
        <form id='formPadrao' action="">
            <fieldset>
                <legend>Professores da Rede Municipal</legend>
                <table>
                    <tr>
                        <td nowrap class="bold">Período de:</td>
                        <td nowrap="nowrap" colspan="2">
                            <?php db_inputdata('periodoInicial', '', '', '', true, 'text', 1); ?>
                        </td>
                        <td nowrap="nowrap" class="bold"> até:</td>
                        <td nowrap="nowrap" colspan="2">
                            <?php db_inputdata('periodoFinal', '', '', '', true, 'text', 1); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class='bold'>Tipo:</td>
                        <td colspan="5">
                            <select id='tipo' style="width: 100%;" onchange='js_validaTipoRelatorio()'>
                                <option selected="selected" value='A'>Analítico</option>
                                <option value='S'>Sintético</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class='bold'>Escola:</td>
                        <td colspan="5">
                            <select id='escola' style="width: 100%;" onchange='js_buscaEnsino();'>
                                <option value='0' selected="selected">Todas</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class='bold'>Curso:</td>
                        <td colspan="5">
                            <select id='ensino' style="width: 100%;" onchange='js_buscaAreaTrabalho();'>
                                <option value='0' selected="selected">Todos</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class='bold'>Área de Trabalho:</td>
                        <td colspan="5">
                            <select id='areaTrabalho' style="width: 100%;" onchange='js_buscaDisciplinas();'>
                                <option value='0' selected="selected">Todos</option>
                            </select>
                        </td>
                    </tr>

                    <tr id='ctnDisciplina'>
                        <td class='bold'>Disciplina:</td>
                        <td colspan="5">
                            <select id='disciplina' style="width: 100%;">
                                <option value='0' selected="selected">Todas</option>
                            </select>
                        </td>
                    </tr>
                    
                </table>
            </fieldset>
            <input type="button" id='imprimir' value='Imprimir' name='botao' onclick="js_imprime();" />
        </form>
    </div>
</center>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));

?>

</body>
</html>
<script type="text/javascript">

    function js_buscaEscola() {
        var oParamentro = new Object();
        oParamentro.exec = 'pesquisaEscola';
        oParamentro.lTodasEscolas = true;

        js_divCarregando("Aguarde, buscando escolas...", "msgBox");
        new Ajax.Request('edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParamentro),
                onComplete: js_retornoEscola
            }
        );
    }

    function js_retornoEscola(oAjax) {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        oRetorno.dados.each(function (oEscola) {

            var oOption = document.createElement('option');
            oOption.value = oEscola.codigo_escola;
            oOption.innerHTML = oEscola.nome_escola.urlDecode();

            $('escola').appendChild(oOption);
        });

    }

    function js_buscaEnsino() {
        var oParamentro = new Object();
        oParamentro.exec = 'pesquisaEnsino';
        oParamentro.iEscola = $F('escola');

        js_divCarregando("Aguarde, buscando cursos...", "msgBox");
        new Ajax.Request('edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParamentro),
                onComplete: js_retornoEnsino
            }
        );
    }

    function js_retornoEnsino(oAjax) {
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        $('ensino').options.length = 0;
        var oOption = document.createElement('option');
        oOption.value = 0;
        oOption.innerHTML = "Todos";
        $('ensino').appendChild(oOption);
        oRetorno.aEnsino.each(function (oCursos) {

            var oOption = document.createElement('option');
            oOption.value = oCursos.iCodigo;
            oOption.innerHTML = oCursos.sDescricao.urlDecode();
            $('ensino').appendChild(oOption);
        });
    }

    function js_buscaAreaTrabalho() {
      var oParametro = new Object();
      oParametro.exec = 'getAreasTrabalho';
      oParametro.iEscola = $F('escola');
      oParametro.iEnsino = $F('ensino');

      js_divCarregando("Aguarde, buscando áreas...", "msgBox");
      new Ajax.Request('edu_educacaobase.RPC.php',
          {
          method: 'post',
          parameters: 'json=' + Object.toJSON(oParametro),
          onComplete: js_retornoBuscaAreaTrabalho
          }
      );
    }

    function js_retornoBuscaAreaTrabalho(oAjax) {
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        $('areaTrabalho').options.length = 0;
        $('areaTrabalho').add(new Option('Todos', 0));

        oRetorno.aAreaTrabalho.each(function (areaTrabalho) {
            $('areaTrabalho').add(new Option(areaTrabalho.ed25_c_descr.urlDecode(), areaTrabalho.ed25_i_codigo));
        });

        if ($('areaTrabalho').options.length < 2) {
            alert('Não foi possivel encontrar uma Área de trabalho para o curso selecionado.')

        }
    }

    function js_buscaDisciplinas() {

        var parametro = {};
        parametro.exec = 'pesquisaDisciplinas';
        parametro.iEscola = $F('escola');
        parametro.iEnsino = $F('ensino');
        parametro.iArea = $F('areaTrabalho');

        js_divCarregando("Aguarde, buscando disciplinas...", "msgBox");
        new Ajax.Request('edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(parametro),
                onComplete: js_retornoDisciplinas
            }
        );
    }

    function js_retornoDisciplinas(oAjax) {
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);

        $('disciplina').options.length = 0;
        var oOption = document.createElement('option');
        oOption.value = 0;
        oOption.innerHTML = "Todos";
        $('disciplina').appendChild(oOption);

        oRetorno.aDisciplinas.each(function (oCursos) {

            var oOption = document.createElement('option');
            oOption.value = oCursos.iCodigo;
            oOption.innerHTML = oCursos.sDescricao.urlDecode();

            $('disciplina').appendChild(oOption);
        });
    }

    function js_imprime() {

        if (js_comparadata($F('periodoInicial'), $F('periodoFinal'), '>')) {

            alert("Data Inicial não pode ser maior que a Data Final.");
            return false;
        }

        var sLocation = "edu2_professorrede002.php?";
        if ($F('tipo') == 'S') {
            sLocation = "edu2_professorrede003.php?";
        }
        sLocation += "periodoInicial=" + $F('periodoInicial');
        sLocation += "&iAreaTrabalho="   + $F('areaTrabalho');
        sLocation += "&periodoFinal=" + $F('periodoFinal');
        sLocation += "&iEscola=" + $F('escola');
        sLocation += "&iEnsino=" + $F('ensino');
        sLocation += "&iDisciplina=" + $F('disciplina');
        sLocation += "&sDisciplina=" + $('disciplina').options[$('disciplina').selectedIndex].innerHTML;

        jan = window.open(sLocation, '',
            'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0');
        jan.moveTo(0, 0);
    }

    function js_validaTipoRelatorio() {
        $('ctnDisciplina').style.display = 'table-row';
        if ($F('tipo') == 'S') {
            $('ctnDisciplina').style.display = 'none';
        }
    }

    js_buscaEscola();
    js_buscaAreaTrabalho();
    js_buscaEnsino();
    js_buscaDisciplinas();

</script>
