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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

$clempresto = new cl_empresto;
$clrecurso = new cl_orctiporec;
$oDaoEmpparam = new cl_empparametro();
$clrecurso->rotulo->label();
$oGet = db_utils::postMemory($_GET);

$sRelatorio = "emp2_relemprestoexecucao002.php";
$rsParam = $oDaoEmpparam->sql_record($oDaoEmpparam->sql_query_file(db_getsession("DB_anousu")));
if ($oDaoEmpparam->numrows > 0) {
    $oEmpParam = db_utils::fieldsMemory($rsParam, 0);
    if ($oEmpParam->e30_notaliquidacao != '') {
        $sRelatorio = "emp2_relemprestoexecucaonotas002.php";
    }
}

$oDaoEmpTipo = new cl_emptipo();
$sSqlBuscaTipos = $oDaoEmpTipo->sql_query_file();
$rsBuscaTipos = $oDaoEmpTipo->sql_record($sSqlBuscaTipos);
$aTiposEmpenhos = array();
for ($iRowTipo = 0; $iRowTipo < $oDaoEmpTipo->numrows; $iRowTipo++) {
    $oStdDadosTipo = db_utils::fieldsMemory($rsBuscaTipos, $iRowTipo);
    $aTiposEmpenhos[$oStdDadosTipo->e41_codtipo] = $oStdDadosTipo->e41_descr;
}
$aTiposEmpenhos[0] = "Todos";

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBHint.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
    <script type="text/javascript" src="scripts/EmissaoRelatorio.js"></script>
    <script type="text/javascript" src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css"/>
</head>
<body class="body-default">
<div class="container">
    <form name="form1" method="post" action="">
        <fieldset>
            <table>
                <tr>
                    <td colspan="2" id="lista-instituicao"></td>
                </tr>
            </table>
            <fieldset style="border-bottom: none; border-right: none; border-left: none; margin: 0;">
                <legend>Opções de Filtros</legend>
                <table>
                    <tr>
                        <td>
                            <strong>Data Inicial: </strong>
                        </td>
                        <td>
                            <?php
                            db_inputdata("dtini", "01", "01", date("Y", db_getsession("DB_datausu")), true, "text", 2);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Data Final: </strong>
                        </td>
                        <td>
                            <?php
                            db_inputdata(
                                "dtfim",
                                date("d", db_getsession("DB_datausu")),
                                date("m", db_getsession("DB_datausu")),
                                date("Y", db_getsession("DB_datausu")),
                                true,
                                "text",
                                2);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Agrupamento:</strong>
                        </td>
                        <td>
                            <?php
                            $acumu = array(
                                "or" => "Órgão",
                                "un" => "Unidade",
                                "fu" => "Função",
                                "su" => "Subfunção",
                                "pr" => "Programa",
                                "pa" => "Projeto/Atividade",
                                "el" => "Elemento",
                                "de" => "Desdobramento",
                                "re" => "Recurso",
                                "tr" => "Tipo de resto",
                                "cr" => "Credor",
                                "ex" => "Exercício"
                            );
                            db_select("tipo", $acumu, true, "text", 2);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Tipo Empenho:</b></td>
                        <td>
                            <?php
                            db_select("emptipo", $aTiposEmpenhos, true, 1);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Restos a Pagar:</strong>
                        </td>
                        <td>
                            <?php
                            $acumu = array(
                                "0" => "Geral-todos",
                                "1" => "Com Movimento até a Data",
                                "2" => "Com saldo a pagar",
                                "3" => "Com liquidação total/parcial",
                                "4" => "Anulados",
                                "5" => "Pagos",
                                "6" => "Com saldo a liquidar"
                            );

                            db_select("commov", $acumu, true, "text", 2);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Exercício:</strong>
                        </td>
                        <td>
                            <?php
                            $sql = $clempresto->sql_query_empenho(
                                db_getsession("DB_anousu"),
                                null,
                                ' distinct e60_anousu ',
                                'e60_anousu'
                            );
                            $result = $clempresto->sql_record($sql);
                            $opcao = array("0" => "Todos");

                            for ($ini = 0; $ini < $clempresto->numrows; $ini++) {
                                db_fieldsmemory($result, $ini);
                                $opcao[$e60_anousu] = $e60_anousu;
                            }

                            db_select("exercicio", $opcao, true, "text", 2);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="tipoInscricao" class="bold">Tipo de Inscrição:</label></td>
                        <td>
                            <?php
                            $dados = [
                                "1" => "Todos",
                                "2" => "Não Processados",
                                "3" => "Processados"
                            ];

                            db_select("tipoInscricao", $dados, true, "text", 2);
                            ?>
                        </td>
                    </tr>

                </table>
            </fieldset>
            <fieldset style="border-bottom: none; border-right: none; border-left: none; margin: 0;">
                <legend>Opções de Impressão</legend>
                <table>
                    <tr>
                        <td>
                            <strong>Quebrar Página:</strong>
                        </td>
                        <td>
                            <input type="checkbox" name="quebradepagina" id="quebradepagina"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Opção de Impressão:</strong>
                        </td>
                        <td>
                            <?php
                            $aImpressao = array(
                                "0" => "Analítico",
                                "1" => "Sintético"
                            );

                            db_select("impressao", $aImpressao, true, "text", 2);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Imprimir Filtros:</strong>
                        </td>
                        <td>
                            <?php
                            $aFiltros = array(
                                "nao" => "Não",
                                "sim" => "Sim"
                            );

                            db_select("imprimefiltros", $aFiltros, true, "text", 2);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Formato:</strong>
                        </td>
                        <td>
                            <?php
                            $aFormato = array(
                                "pdf" => "PDF",
                                "csv" => "CSV");

                            db_select("formato", $aFormato, true, "text", 2);
                            ?>
                        </td>
                    </tr>

                </table>
            </fieldset>
            <?php
            db_input('sRelatorio', 10, "", true, "hidden", 1);
            db_input('listacredor', 10, "", true, "hidden", 1);
            db_input('filtra_despesa', 10, "", true, "hidden", 1);
            db_input('vercredor', 10, "", true, "hidden", 1);
            ?>
        </fieldset>
        <input name="emitir" id="emitir" type="button" value="Emitir" onclick="js_emite();"/>
    </form>
</div>
</body>
</html>
<script type="text/javascript">

    oViewInstituicao = new DBViewInstituicao('oViewInstituicao', $('lista-instituicao'));
    oViewInstituicao.show();

    function js_emite() {
        vir = "";
        listacredor = "";
        for (x = 0; x < parent.iframe_g2.document.form1.credor.length; x++) {
            listacredor += vir + parent.iframe_g2.document.form1.credor.options[x].value;
            vir = ",";
        }

        document.form1.listacredor.value = listacredor;
        // pega dados da func_selorcdotacao_aba.php
        document.form1.filtra_despesa.value = parent.iframe_filtro.js_atualiza_variavel_retorno();
        document.form1.vercredor.value = parent.iframe_g2.document.form1.ver.value;

        var oInstituicoes = oViewInstituicao.getInstituicoesSelecionadas().map(function (oItem) {
            return oItem.codigo;
        });

        if (oInstituicoes.length == 0) {
            return alert('Selecione ao menos uma Instituição.');
        }

        var oRelatorio = new EmissaoRelatorio(sRelatorio.value, {
            db_selinstit: oInstituicoes.join('-'),
            listacredor: $F('listacredor'),
            vercredor: $F('vercredor'),
            filtra_despesa: $F('filtra_despesa'),
            vercredor: $F('vercredor'),
            dtini: $F('dtini'),
            dtfim: $F('dtfim'),
            tipo: $F('tipo'),
            commov: $F('commov'),
            exercicio: $F('exercicio'),
            impressao: $F('impressao'),
            formato: $F('formato'),
            emptipo: $F('emptipo'),
            emptipo_descricao: $("emptipo").options[$("emptipo").selectedIndex].text,
            imprimefiltros: $F('imprimefiltros'),
            dtini_dia: $F('dtini_dia'),
            dtini_mes: $F('dtini_mes'),
            dtini_ano: $F('dtini_ano'),
            dtfim_dia: $F('dtfim_dia'),
            dtfim_mes: $F('dtfim_mes'),
            dtfim_ano: $F('dtfim_ano'),
            desdobramentos: parent[2].desdobramentos,
            tipoInscricao: $F('tipoInscricao')
        });

        $("quebradepagina").checked && oRelatorio.addParameter("quebradepagina", 1);

        oRelatorio.setMethod('post');
        oRelatorio.open();
    }

    function downloadArquivo(caminhoArquivo, nomeArquivo) {
        var oDownload = new DBDownload();
        oDownload.addFile(caminhoArquivo, nomeArquivo);
        oDownload.show();
    }

    document.getElementById('tipo').style.width = "210px";
    document.getElementById('commov').style.width = "210px";
    document.getElementById('exercicio').style.width = "210px";
    document.getElementById('impressao').style.width = "210px";
    document.getElementById('formato').style.width = "210px";
    document.getElementById('emptipo').style.width = "210px";
    document.getElementById('tipoInscricao').style.width = "210px";
    document.getElementById('imprimefiltros').style.width = "210px";

    /**
     * Retorna as instituições que foram selecionadas
     * @return {Array}
     */
    function getInstituicoesSelecionadas() {

        return oViewInstituicao.getInstituicoesSelecionadas().map(function (oItem) {
            return oItem.codigo;
        });
    }
</script>
