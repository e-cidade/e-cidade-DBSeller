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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("std/DBDate.php"));

$disable = true;
$oMatricula = "";


if (isset($_GET['iAluno']) && $_GET['iAluno'] != "") {
    $ed47_i_codigo = $_GET['iAluno'];
}
$ed60_i_codigo = '';
if (isset($_GET['iMatricula']) && $_GET['iMatricula'] != "") {
    $ed60_i_codigo = $_GET['iMatricula'];
}

if (isset($_GET['disabled']) && $_GET['disabled'] != 1) {
    $disable = $_GET['disabled'];
}

$ed57_i_codigo = '';
if (isset($_GET['iTurma']) && $_GET['iTurma'] != "") {
    $ed57_i_codigo = $_GET['iTurma'];
}

if (isset($_GET['sAluno']) && $_GET['sAluno'] != "") {
    $ed47_v_nome = $_GET['sAluno'];
}

$iEscola = db_getsession("DB_coddepto");
if (!isset($iEscola)) {
    $iEscola = 0;
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js, prototype.js, strings.js");
    db_app::load("estilos.css");
    db_app::load("classes/educacao/escola/ListaEscola.classe.js");
//    db_app::load("classes/educacao/escola/ListaCalendarioAnosAntigos.classe.js");
    db_app::load("classes/educacao/escola/ListaEtapa.classe.js");
    db_app::load("classes/educacao/escola/ListaTurma.classe.js");
    ?>
    <script type="text/javascript">
        require_once('scripts/widgets/DBToggleList.widget.js');

        function js_imprimir(){

        var sUrl = "edu2_declaracaoexalunos002.php";
        sUrl += "?iMatriculas=<?=$ed60_i_codigo?>";
        sUrl += "&iTurma=<?=$ed57_i_codigo?>";
        sUrl += "&sDiretor="+$('emissor').value;
        sUrl += "&lExibeGradeAluno="+$F('gradeAluno');
        sUrl += "&sObservacao=" + tagString($F('observacao'));

        jan = window.open(sUrl, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0');
        jan.moveTo(0, 0);

        location.href = "edu2_declaracaoexalunos001.php";
        }

        function habilitarBotao() {
            var imprimir = document.getElementById('imprimir');
            imprimir.disabled = false;

        }
    </script>
</head>
<body class="body-container">
<?php
/**
 * Validamos se estamos no módulo escola
 */
if (db_getsession("DB_modulo") == 1100747) {
    MsgAviso(db_getsession("DB_coddepto"), "escola");
}
?>
<div class='container'>
        <fieldset style="width: 640px; height: 476px;">
            <legend>Declaração Ex-Alunos</legend> <!-- 2018-06-21 Wallace (ATMA) Troca do Título-->
            <table class="form-container">
                <tr>
                    <td nowrap="nowrap" class='bold field-size4'>Código:
                        <input type="text" id="pesquisaCodigo" style="width:80px"
                               onkeypress="return submeter(this,event)"
                    </td>
                    <td rowspan="2">
                        <fieldset class="alert alert-primary text-center" role="alert"
                             style="padding: 2px; font-size: 12px;">
                            Para pesquisar aluno por código ou nome,<br>digite a informação e pressione
                            <kbd>ENTER</kbd>.
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td nowrap="nowrap" class='bold field-size4'>Aluno: <input 
                                                                            class='bold field-size4'
                                                                            type="text"
                                                                            id="pesquisaAluno"
                                                                            style=" margin-left:8px; width:300px"
                                                                            onkeypress="return submeter(this,event)"
                                                                        />
                    </td>
                    <td nowrap="nowrap"></td>
                </tr>

                <tr>

            </table>
            <fieldset id="tabelaAlunos" class='separator'>
                <legend>Ex-Alunos</legend>
                <fieldset style="height: 162px; width: 596px">
                    <legend><b>Selecione o aluno</b></legend>
                    <?php
                        $where = " ed52_d_fim < CURRENT_DATE AND 
                                        turma.ed57_i_escola = {$iEscola} ";

                        $where .= isset($ed47_i_codigo) && (trim($ed47_i_codigo) != "") ?
                                                            " AND ed47_i_codigo = {$ed47_i_codigo}" : "";
                            
                        $where .= isset($ed47_v_nome) && (trim($ed47_v_nome) != "") ?
                                                        " AND ed47_v_nome like'" . strtoupper($ed47_v_nome) . "%'" : "";  
                        
                        $sql = "SELECT 
                                    x.ed60_i_aluno, 
                                    x.ed60_i_codigo, 
                                    turma.ed57_i_codigo, 
                                    aluno.ed47_v_nome AS dl_Nome,
                                    matricula.ed60_c_situacao AS dl_Situação, 
                                    calendario.ed52_c_descr AS dl_Calendario
                                from (
                                        SELECT  max(ed60_i_codigo) as ed60_i_codigo, ed60_i_aluno
                                        FROM matricula
                                        JOIN aluno ON aluno.ed47_i_codigo = matricula.ed60_i_aluno
                                        JOIN turma ON turma.ed57_i_codigo = matricula.ed60_i_turma
                                        JOIN calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                                        WHERE  {$where}
                                        group by ed60_i_aluno
                                    ) as x
                                JOIN matricula ON matricula.ed60_i_codigo = x.ed60_i_codigo
                                JOIN aluno ON aluno.ed47_i_codigo = x.ed60_i_aluno
                                JOIN turma ON turma.ed57_i_codigo = matricula.ed60_i_turma
                                JOIN calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                                order by 4";

                        $repassa = [];
                        if (isset($ed47_i_codigo)) {
                            $repassa = array("ed47_i_codigo" => $ed47_i_codigo);
                        }
                        db_lovrot(@$sql, 5, "()", "", "js_redireciona|ed60_i_aluno|ed57_i_codigo|ed60_i_codigo", "", "NoMe", $repassa, false);
                    ?>
                </fieldset>
            </fieldset>

            <!--       caso Queira o combo select   <fieldset > -->
            <!--              <div id="listAlunos" style="width:800px;"> </div> -->
            <!--          </fieldset> -->


            <fieldset class='separator'>
                <legend>Observação</legend>
                <textarea id='observacao' rows="5" style="width: 100%;"></textarea>
                <table>
                    <tr>
                        <td nowrap="nowrap" class = 'bold field-size10'>
                            Emissor:
                            <select id='emissor' style="width:100%;">
                                <option value="">Selecione Emissor</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" class = 'bold field-size3'>Exibir Grade de Horário:
                            <select id='gradeAluno' style="width:30%;">
                                <option value="N">Não</option>
                                <option value="S">Sim</option>
                            </select></td>
                        <td nowrap="nowrap" >

                        </td>
                    </tr>
                </table>
            </fieldset>

            <button type="button" value="Imprimir" id="imprimir" name="imprimir"
                    onclick="js_imprimir()" disabled>
                <i class="far fa-file-pdf"></i>
                Processar
            </button>

<!--            <input type="button" disabled="disabled" id='imprimir' value='Imprimir' name='imprimir' onClick="js_imprimir()"/>-->
        </fieldset>
</div>
</body>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>
<script type="text/javascript">
    function submeter(myfield, e) {
        var keycode;
        if (window.event) keycode = window.event.keyCode;
        else if (e) keycode = e.which;
        else return true;

        if (keycode == 13) {
            js_pesquisar();
            return false;
        }
        else
            return true;
    }

    function js_pesquisar() {
        codigo = document.getElementById("pesquisaCodigo").value;
        nome = document.getElementById("pesquisaAluno").value;
        location.href = "edu2_declaracaoexalunos001.php?pesquisar&iAluno=" + codigo + "&sAluno=" + nome;
    }

    function js_redireciona(chave,turma,matricula) {
        location.href = "edu2_declaracaoexalunos001.php?iAluno=" + chave + "&disabled=" + 0 + "&iTurma="+turma+ "&iMatricula="+matricula;
    }

    function js_buscaEmissor() {
        if (<?=$iEscola?> == '') {
            return false;
        }
        var oParametros     = {};
        oParametros.exec    = 'buscaEmissor';
        oParametros.iEscola = <?=$iEscola?>;

        $('emissor').options.length = 0;
        $('emissor').add(new Option("Selecione Emissor", ""));

        js_divCarregando('Aguarde... carregando dados do emissor.', "msgBox");

        var oObjeto        = {};
        oObjeto.method     = 'post';
        oObjeto.parameters = 'json='+Object.toJSON(oParametros);
        oObjeto.onComplete = function(oAjax) {
            js_retornoEmissor(oAjax);
        };
        new Ajax.Request("edu_educacaobase.RPC.php", oObjeto);
    }

    function js_retornoEmissor(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = eval("(" + oAjax.responseText+ ")");

        if (oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode());
        }

        oRetorno.dados.each( function (oEmissor) {

            var sValue  = oEmissor.funcao.urlDecode()+'|'+oEmissor.nome.urlDecode()+'|'+oEmissor.descricao.urlDecode();
            var sString = oEmissor.funcao.urlDecode()+' - '+oEmissor.nome.urlDecode();

            if (!empty(oEmissor.descricao)) {
                sString += " ("+oEmissor.descricao.urlDecode()+") ";
            }
            $('emissor').add(new Option(sString, sValue));
        });
    }

    document.getElementById('imprimir').disabled = <?= $disable?>;

    var tabela = document.getElementById('TabDbLov');
    var trs = tabela.getElementsByTagName("tr");
    trs[trs.length-1].hidden=true;

    js_buscaEmissor();
</script>
</html>
