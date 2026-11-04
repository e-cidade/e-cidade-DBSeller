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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_acervo_classe.php"));
require_once(modification("classes/db_autor_classe.php"));
require_once(modification("classes/db_tipoitem_classe.php"));
require_once(modification("classes/db_editora_classe.php"));
require_once(modification("classes/db_classiliteraria_classe.php"));
require_once(modification("classes/db_localizacao_classe.php"));
require_once(modification("classes/db_aquisicao_classe.php"));

$clacervo = new cl_acervo;
$clautor = new cl_autor;
$cltipo = new cl_tipoitem;
$cleditora = new cl_editora;
$clclassi = new cl_classiliteraria;
$cllocalizacao = new cl_localizacao;
$claquisicao = new cl_aquisicao;
$clacervo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("bi06_biblioteca");
$clrotulo->label("bi17_codigo");
$clrotulo->label("bi17_nome");

$clrotulo->label("bi29_sequencial");
$clrotulo->label("bi29_nome");
$clrotulo->label("bi29_abreviatura");

$depto = db_getsession("DB_coddepto");
$sql = "SELECT bi17_codigo,bi17_nome FROM biblioteca WHERE bi17_coddepto = $depto";
$result = db_query($sql);
;
$linhas = pg_num_rows($result);
if ($linhas != 0) {
    db_fieldsmemory($result, 0);
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
    ?>
</head>
<body>
<?php MsgAviso(db_getsession("DB_coddepto"), "biblioteca", "", " bi17_coddepto = " . db_getsession("DB_coddepto") . ""); ?>
<div class="container">
    <form name="form1" method="post" action="">
        <fieldset>
            <legend><b>Relatório de Acervos</b></legend>
            <table class="form-container" align="center">
                <tr>
                    <td align="left" nowrap title="Editora">
                        <strong>Editora:</strong>
                    </td>
                    <td align="left" nowrap title="Editora">
                        <?php
                        $result_editora = $cleditora->sql_record($cleditora->sql_query("", "*", "bi02_nome", ""));
                        db_selectrecord("editora", $result_editora, true, 2, "", "", "", 1, "", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Classificação Literária">
                        <strong>Class. Literária:</strong>
                    </td>
                    <td align="left" nowrap title="Classificação Literária">
                        <?php
                        $result_classi = $clclassi->sql_record($clclassi->sql_query("", "*", "bi03_classificacao", ""));
                        db_selectrecord("classi", $result_classi, true, 2, "", "", "", 1, "", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Tipo de Ítem">
                        <strong>Tipo:</strong>
                    </td>
                    <td align="left" nowrap title="Tipo de Ítem">
                        <?php
                        $result_tipo = $cltipo->sql_record($cltipo->sql_query("", "*", "bi05_nome", ""));
                        db_selectrecord("tipo", $result_tipo, true, 2, "", "", "", 1, "", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Localização">
                        <strong>Localização:</strong>
                    </td>
                    <td align="left" nowrap title="Localização">
                        <?php
                        $sqlLocalizacao = $cllocalizacao->sql_query("", "*", "bi09_nome", " bi09_biblioteca = $bi17_codigo");
                        $result_localizacao = $cllocalizacao->sql_record($sqlLocalizacao);
                        if ($result_localizacao) {
                            db_selectrecord("localizacao", $result_localizacao, true, 2, "", "", "", 1, "", 1);
                        } else {
                            ?>
                            <select name="localizacao" id="localizacao">
                                <option value=""></option>
                            </select>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Autor">
                        <strong>Autor:</strong>
                    </td>
                    <td align="left" nowrap title="Autor">
                        <?php
                        $result_autor = $clautor->sql_record($clautor->sql_query("", "*", "bi01_nome", ""));
                        db_selectrecord("autor", $result_autor, true, 2, "", "", "", 1, "", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Coleção">
                        <?php db_ancora("Coleção: ", "js_pesquisaColecao(true);", 1); ?>
                    </td>
                    <td align="left" nowrap title="Coleção">
                        <?php
                        db_input("bi29_sequencial", 10, $Ibi29_sequencial, true, "text", 1, "onchange='js_pesquisaColecao(false);'");
                        db_input("bi29_nome", 47, '', true, "text", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Ordenação">
                        <strong>Ordenar por:</strong>
                    </td>
                    <td align="left" nowrap title="Ordenação">
                        <?php
                        $ordem = array("bi06_titulo" => "Título", "bi06_seq" => "Código");
                        db_select("ordem", $ordem, true, 2)
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" nowrap title="Tipo de relatório">
                        <strong>Relatório:</strong>
                    </td>
                    <td align="left" nowrap title="Tipo de relatório">
                        <?php
                        $rel = array("resumido" => "Resumido", "completo" => "Completo");
                        db_select("rel", $rel, true, 2)
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tbi06_biblioteca ?>">
                        <?= @$Lbi06_biblioteca ?>
                    </td>
                    <td>
                        <?php
                        db_input('bi17_codigo', 10, $Ibi17_codigo, true, 'text', 3, "");
                        db_input('bi17_nome', 47, $Ibi17_nome, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Tipo de Saída:</strong></td>
                    <td>
                        <select name="formatoImpressao" id="formatoImpressao">
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();">
    </form>
</div>
<?php db_menu(); ?>
</body>
</html>
<script>

    const formatoImpressao = document.getElementById('formatoImpressao');
    const apiUrl = "edu4_biblioteca.RPC.php";

    function js_pesquisaColecao(lMostra) {
        var sUrl = 'func_colecaoacervo.php?';
        if (lMostra) {
            sUrl += 'funcao_js=parent.js_mostraColecao1|bi29_sequencial|bi29_nome';
            js_OpenJanelaIframe('', 'db_iframe_colecaoacervo', sUrl, 'Pesquisa Coleção', true);
        } else {
            if ($F('bi29_sequencial') != '') {
                sUrl += 'pesquisa_chave=' + $F('bi29_sequencial');
                sUrl += '&funcao_js=parent.js_mostraColecao';
                js_OpenJanelaIframe('', 'db_iframe_colecaoacervo', sUrl, 'Pesquisa Coleção', false);
            } else {
                $('bi29_sequencial').value = "";
            }
        }
    }

    function js_mostraColecao(sNome, lErro) {
        $('bi29_nome').value = sNome;
        if (lErro) {
            $('bi29_sequencial').value = '';
            $('bi29_nome').value = sNome;
            $('bi29_nome').focus();
        }
    }

    function js_mostraColecao1(iColecao, sNome) {
        $('bi29_sequencial').value = iColecao;
        $('bi29_nome').value = sNome;
        db_iframe_colecaoacervo.hide();
    }

    function js_emite() {
        if (formatoImpressao.value === 'pdf') {
            jan = window.open('bib2_racervos002.php?ordem=' + document.form1.ordem.value 
                + '&editora=' + document.form1.editora.value + '&classi=' + document.form1.classi.value 
                + '&tipo=' + document.form1.tipo.value + '&localizacao=' + document.form1.localizacao.value 
                + '&autor=' + document.form1.autor.value + '&rel=' + document.form1.rel.value 
                + '&biblioteca=' + document.form1.bi17_codigo.value + '&desc_biblioteca=' 
                + document.form1.bi17_nome.value + '&iColecao=' + document.form1.bi29_sequencial.value, 
                '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) 
                + ',scrollbars=1,location=0 '
            );
            jan.moveTo(0, 0);
            return false;
        }

        var oParametros = new Object();
        oParametros.exec = 'processarRelatorioAcervoCSV';
        oParametros.ordem = document.form1.ordem.value;
        oParametros.filtroEditora = document.form1.editora.value;
        oParametros.filtroClassificacao = document.form1.classi.value;
        oParametros.filtroTipo = document.form1.tipo.value;
        oParametros.filtroLocalizacao = document.form1.localizacao.value;
        oParametros.filtroAutor = document.form1.autor.value;
        oParametros.filtroRel = document.form1.rel.value;
        oParametros.filtroBiblioteca = document.form1.bi17_codigo.value;
        oParametros.filtroColecao = document.form1.bi29_sequencial.value;
        const body = {
            method:'post',
            parameters: 'json='+Object.toJSON(oParametros),
            onComplete: js_trataRetornoRelatorioCSV
        }
        //js_divCarregando('Aguarde... Imprimindo o relatório','msgbox');
        var oAjax = new Ajax.Request(apiUrl, body);
    }

    function js_trataRetornoRelatorioCSV(oAjax) 
    {
        const retorno = JSON.parse(oAjax.responseText);
        //js_removeObj("msgbox");
        if (retorno.iStatus != 0 && retorno.pathRelatorioCSV != "") {
            jan = window.open(retorno.pathRelatorioCSV,'');
        } else {
            alert(retorno.sMessage.urlDecode());
        } 
    }
</script>
