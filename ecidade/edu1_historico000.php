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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));

db_postmemory($_POST);

$db_opcao = 1;
$db_botao = true;

$oDaoAluno = new cl_aluno();
$oDaoHistorico = new cl_historico();
$clrotulo = new rotulocampo;
$oDaoAluno->rotulo->label();
$clrotulo->label("ed61_i_aluno");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
    <script language="javascript" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <?php
    db_app::load('dbcomboBox.widget.js, dbtextField.widget.js');
    ?>
</head>
<body class="body-default">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" valign="top" bgcolor="#CCCCCC">
                <center>
                    <br></br>
                    <form name="form1" method="post" action="">
                        <table>
                            <tr>
                                <td nowrap title="<?= @$Ted47_i_codigo ?>">
                                    <?php
                                    db_ancora(@$Led61_i_aluno, "js_pesquisaed47_i_codigo(true);", $db_opcao);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    db_input('ed47_i_codigo', 10, @$Ied47_i_codigo, true, 'text', 3);
                                    db_input('ed47_v_nome', 40, @$Ied47_v_nome, true, 'text', 3);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="button" id='impHistorico' name='impHistorico' value='Emitir Histórico Escolar' onclick='js_emiteHistorico()' disabled="disabled" />
                                    <input type="button" id='impCertificado' name='impCertificado' value='Emitir Certificado de Conclusão' onclick='js_emiteCertificado()' disabled="disabled" />
                                </td>
                            </tr>
                        </table>
                    </form>
                    <fieldset style="width:99%;padding:0px;">
                        <legend><b>Histórico do Aluno</b></legend>
                        <table width="100%" border="0" cellspacing="2" cellpadding="2">
                            <tr>
                                <td valign="top" width="42%">
                                    <iframe src="edu1_historicoarvore.php?ed61_i_aluno=<?= @$ed47_i_codigo ?>&ed47_v_nome=<?= @$ed47_v_nome ?>" name="arvore" id="arvore" width="99%" height="230" frameborder="1" marginwidth="0" leftmargin="0" topmargin="0"></iframe>
                                    <br>
                                    <iframe src="" name="disciplina" id="disciplina" width="99%" height="230" frameborder="1" marginwidth="0" leftmargin="0" topmargin="0"></iframe>
                                </td>
                                <td align="right" valign="top">
                                    <iframe src="" name="dados" id="dados" width="99%" height="462" frameborder="1" marginwidth="0" leftmargin="0" topmargin="0"></iframe>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </center>
            </td>
        </tr>
    </table>
    <form name="hist" method="post" action="">
        <?php
        db_input('ed47_i_codigo', 10, @$ed47_i_codigo, true, 'hidden', $db_opcao);
        db_input('ed47_v_nome', 100, @$ed47_v_nome, true, 'hidden', $db_opcao);
        ?>
    </form>
    <div id="ctnLancamentoArea" style="display: none">
        <div class="container">
            <fieldset>
                <legend>
                    <b>Lançamento de Areas de Conhecimento da Etapa</b>
                </legend>
                <input type="hidden" id="iTipoHistorico">
                <input type="hidden" id="codigoHistoricoEtapa">
                <input type="hidden" id="codigoHistoricoArea">
                <table class="form-container">
                    <tr>
                        <td>
                            <label id="ancoraArea" for="codigoArea">Áreas de Conhecimento:</label>
                        </td>
                        <td>
                            <input id="codigoArea" type="text">
                            <input id="descricaoArea" type="text">
                        </td>
                    </tr>
                    <tr>
                        <td>Aproveitamento:</td>
                        <td>
                            <input id="resultadoObtidoArea" type="text">
                        </td>
                    </tr>
                    <tr>
                        <td>Resultado Final:</td>
                        <td>
                            <select name="resultadoFinalArea" id="resultadoFinalArea">
                                <option value="A">Aprovado</option>
                                <option value="R">Reprovado</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <button type="button" id="salvar"><i class="fa fa-save"></i> Salvar</button>
            <button type="button" id="limpar"><i class="fa fa-eraser"></i> Limpar</button>
            <br>
            <fieldset>
                <legend class="separator">Areas de Conhecimento</legend>
                <div id="gridAreas"></div>
            </fieldset>
        </div>
    </div>
</body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
<script>
    const ctnLancamentoArea = document.getElementById('ctnLancamentoArea');
    const ctnGridAreas = document.getElementById('gridAreas');
    const btnSalvarArea = document.getElementById('salvar');
    const inputTipoHistoricoEtapa = document.getElementById('iTipoHistorico');
    const inputCodigoHistoricoEtapa = document.getElementById('codigoHistoricoEtapa');
    const inputCodigoHistoricoArea = document.getElementById('codigoHistoricoArea');
    const ancoraArea = document.getElementById('ancoraArea');
    const codigoArea = document.getElementById('codigoArea');
    const descricaoArea = document.getElementById('descricaoArea');
    const resultadoObtidoArea = document.getElementById('resultadoObtidoArea');
    const resultadoFinalArea = document.getElementById('resultadoFinalArea');

    const MENSAGENS_MANUTENCAO_HISTORICO_000 = 'educacao.escola.edu_historico000.';
    var sUrlRPC = 'edu4_historicoaluno.RPC.php';

    new DBLookUp(ancoraArea, codigoArea, descricaoArea, {
        'sArquivo': 'func_areaconhecimento.php',
        "sObjetoLookUp": "db_iframe_areaconhecimento",
        'sLabel': 'Pesquisa de Areas de Conhecimento',
        'zIndex': '11000',
        'aCamposAdicionais': ['ed293_sequencial', 'ed293_descr'],
        'fCallBack': js_retornaPesquisaAreaDeConhecimento
    });

    var collectionAreasConhecimento = new Collection().setId('codigo');
    var gridAreasConhecimento = new DatagridCollection(collectionAreasConhecimento).configure({
        order: false,
        height: 250
    });

    gridAreasConhecimento.addColumn('codigo', {
        label: "Código",
        width: '50px',
        align: 'center'
    });
    gridAreasConhecimento.addColumn('descricao_areaconhecimento', {
        label: "Área de Conhecimento",
        width: '300px',
        align: 'center'
    });
    gridAreasConhecimento.addColumn('resultado_obtido', {
        label: "Aproveitamento",
        width: '60px',
        align: 'center'
    });
    gridAreasConhecimento.addColumn('resultado_final', {
        label: "Resultado Final",
        width: '60px',
        align: 'center'
    });
    gridAreasConhecimento.addAction('Editar', 'Editar', (event, linha) => {

        inputCodigoHistoricoArea.value = linha.codigo;
        codigoArea.value = linha.codigo_areaconhecimento;
        descricaoArea.value = linha.descricao_areaconhecimento;
        resultadoObtidoArea.value = linha.resultado_obtido;
        resultadoFinalArea.value = linha.resultado_final;

    }, true, 'fa-pen');
    gridAreasConhecimento.addAction('Excluir', 'Excluir', (event, linha) => {
        if (!confirm("Ao excluir uma Area de Conhecimento dessa Etapa do Histórico," +
                "serão removido todos os vinculos de Disciplinas." +
                "Confirma exclusão?")) {
            return;
        }

        var oParametro = {};
        oParametro.exec = 'excluirAreaHistorico';
        oParametro.iTipoHistorico = inputTipoHistoricoEtapa.value;
        oParametro.iCodigo = linha.ID;
        js_divCarregando('Excuindo...', 'loading_message', true);
        new Ajax.Request(sUrlRPC, {
            asynchronous: false,
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: (response) => {
                js_removeObj('loading_message');
                var oRetorno = JSON.parse(response.responseText);
                if (oRetorno.status === 2) {
                    alert(oRetorno.message.urlDecode());
                    return;
                }
                collectionAreasConhecimento.remove(linha.ID);
                gridAreasConhecimento.reload();
            }
        });
    }, true, 'fa-trash');


    function js_salvarHistoricoAreaEtapa() {

        var oParametro = {};
        oParametro.exec = 'salvarAreaHistorico';
        oParametro.iHistoricoMps = inputCodigoHistoricoEtapa.value;
        oParametro.iTipoHistorico = inputTipoHistoricoEtapa.value;
        oParametro.iCodigo = inputCodigoHistoricoArea.value;
        oParametro.iCodigoArea = codigoArea.value;
        oParametro.iResultadoObtido = resultadoObtidoArea.value;
        oParametro.iResultadoFinal = resultadoFinalArea.value;
        js_divCarregando('Salvando...', 'loading_message', true);
        new Ajax.Request(sUrlRPC, {
            asynchronous: false,
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: (response) => {
                js_removeObj('loading_message');

                var oRetorno = JSON.parse(response.responseText);
                if (oRetorno.status == 2) {
                    alert(oRetorno.message.urlDecode());
                    return;
                }
                js_limparCamposArea();

                collectionAreasConhecimento.add({
                    codigo: oRetorno.areahistorico.codigo,
                    codigo_areaconhecimento: oRetorno.areahistorico.codigo_areaconhecimento,
                    descricao_areaconhecimento: oRetorno.areahistorico.descricao_areaconhecimento.urlDecode(),
                    resultado_final: oRetorno.areahistorico.resultado_final,
                    resultado_obtido: oRetorno.areahistorico.resultado_obtido.urlDecode()
                });

                gridAreasConhecimento.reload();
            },
        });
    }
    btnSalvarArea.addEventListener('click', js_salvarHistoricoAreaEtapa);

    function js_retornaPesquisaAreaDeConhecimento(param1, param2, codigo, descricao) {
        if (!empty(codigo)) {
            codigoArea.value = codigo;
            descricaoArea.value = descricao;
        }
    }

    function js_pesquisaed47_i_codigo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_aluno', 'func_aluno.php?funcao_js=parent.js_mostraaluno1|ed47_i_codigo|ed47_v_nome', 'Pesquisa', true);
        }
    }

    function js_mostraaluno1(chave1, chave2) {

        document.form1.ed47_i_codigo.value = chave1;
        document.form1.ed47_v_nome.value = chave2;

        db_iframe_aluno.hide();
        document.form1.submit();
    }

    function js_lancarAreasConhecimento(iCodigoHistoricoAno, iTipoHistorico, iEnsino, iHistoricomps, iCurso, iAnoReferencia) {
        inputCodigoHistoricoEtapa.value = iHistoricomps;
        inputTipoHistoricoEtapa.value = iTipoHistorico;

        var windowLancarArea = new windowAux('windowArea', 'Lançar Areas de Conhecimento', 800, 600);
        windowLancarArea.setContent(ctnLancamentoArea);
        windowLancarArea.setShutDownFunction(() => {
            windowLancarArea.destroy();
            gridAreasConhecimento.clear();
        })
        windowLancarArea.show(0, 0, true);
        ctnLancamentoArea.style.display = 'block';
        gridAreasConhecimento.show(ctnGridAreas);

        var oParametro = {};
        oParametro.exec = 'buscarAreasHistoricoEtapa';
        oParametro.iHistoricoMps = iHistoricomps;
        oParametro.iTipoHistorico = iTipoHistorico;
        js_divCarregando('', 'loading_message', true);
        new Ajax.Request(sUrlRPC, {
            asynchronous: false,
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: (response) => {
                js_removeObj('loading_message');
                var oRetorno = JSON.parse(response.responseText);

                oRetorno.areas_conhecimento.map((area_conhecimento) => {
                    collectionAreasConhecimento.add({
                        codigo: area_conhecimento.codigo,
                        codigo_areaconhecimento: area_conhecimento.codigo_areaconhecimento,
                        descricao_areaconhecimento: area_conhecimento.descricao_areaconhecimento.urlDecode(),
                        resultado_final: area_conhecimento.resultado_final,
                        resultado_obtido: area_conhecimento.resultado_obtido
                    });
                });
                gridAreasConhecimento.reload();
            }
        });
    }

    function js_montaCboAreasConhecimento(oCboAreaConhecimento, iHistoricomps, iTipoHistorico) {
        var oParametro = {};
        oParametro.exec = 'buscarAreasHistoricoEtapa';
        oParametro.iHistoricoMps = iHistoricomps;
        oParametro.iTipoHistorico = iTipoHistorico;

        new Ajax.Request(sUrlRPC, {
            asynchronous: false,
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: (response) => {
                var oRetorno = JSON.parse(response.responseText);

                oRetorno.areas_conhecimento.map((area_conhecimento) => {
                    oCboAreaConhecimento.addItem(area_conhecimento.codigo, area_conhecimento.descricao_areaconhecimento.urlDecode());
                });
            }
        });
    }

    /*
     * Função para carregar a tela de lançamento de Disciplinas
     */
    function js_lancarDisciplina(iCodigoHistoricoAno, iTipoHistorico, iEnsino, iHistoricomps, iCurso, iAnoReferencia) {

        aDisciplinasLancadas = new Array();
        iCodigoHistorico = iCodigoHistoricoAno;
        iTipoHistoricoTurma = iTipoHistorico;
        iEnsinoTurma = iEnsino;
        iHistoricompsAluno = iHistoricomps;
        iCodigoCurso = iCurso;

        var iTamanhoJanela = document.body.getWidth() / 1.3;
        oWindowDisciplinas = new windowAux('wndDisciplina', 'Lançar Disciplinas', iTamanhoJanela);
        var sConteudo = "<div id='conteudo'>";
        sConteudo += "  <fieldset>";
        sConteudo += "    <legend>";
        sConteudo += "      <b>Lançamento de Disciplina</b>";
        sConteudo += "    </legend>";
        sConteudo += "    <table>";
        sConteudo += "      <tr style='display:none'>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Código:</b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnCodigo'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr>";
        sConteudo += "        <td>";
        sConteudo += "          <a href='#' onclick='js_pesquisarDisciplina(true);return false'><b>Disciplina:</b></a>";
        sConteudo += "        </td>";
        sConteudo += "        <td>";
        sConteudo += "          <span id='ctnCodigoDisciplina'>";
        sConteudo += "          </span>";
        sConteudo += "          <span  id='ctnDescricaoDisciplina'>";
        sConteudo += "          </span>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr style='display: none'>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Área de Conhecimento:<b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnCboAreaConhecimento'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Tipo de Base:<b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnCboBaseComun'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Situação:</b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnCboSituacao'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Carga Horária:</b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnTxtCargaHoraria'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr id='ctnLinhaResultado'>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Resultado:</b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnCboResultado'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Aproveitamento:</b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnTxtAproveitamento'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr>";
        sConteudo += "        <td>";
        sConteudo += "          <b>Termo Final:</b>";
        sConteudo += "        </td>";
        sConteudo += "        <td id='ctnTxtTermoFinal'>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "      <tr id='ctnLinhaJustificativa' style='visibility: hidden;'>";
        sConteudo += "        <td>";
        sConteudo += "          <a href='#' onclick='js_pesquisarJustificativa(true);return false'><b>Justificativas:</b></a>";
        sConteudo += "        </td>";
        sConteudo += "        <td>";
        sConteudo += "          <span id='ctnTxtCodigoJustificativa'>";
        sConteudo += "          </span>";
        sConteudo += "          <span  id='ctnTxtDescricaoJustificativa'>";
        sConteudo += "          </span>";
        sConteudo += "        </td>";
        sConteudo += "      </tr>";
        sConteudo += "    </table>";
        sConteudo += "  </fieldset>";
        sConteudo += "  <center>";
        sConteudo += "    <input id='btnSalvarDisciplina' type='button' value='Salvar'>";
        sConteudo += "    <input id='btnLimparDadosDisciplina' type='button' value='Limpar Dados'>";
        sConteudo += "    <input id='lExibeMensagemAproveitamentoMinimo' type='hidden' value=true>";
        sConteudo += "  </center>";
        sConteudo += "  <fieldset>";
        sConteudo += "    <div id='ctnGridDisciplinas'>";
        sConteudo += "    </div>";
        sConteudo += "  </fieldset>";
        sConteudo += "</div>";
        oWindowDisciplinas.setContent(sConteudo);
        oWindowDisciplinas.setShutDownFunction(function() {
            /**
             * validados os dados, caso a etapa esteja marcada como reprovada;
             * ao menos uma disciplina deve estar como Reprovada.
             */
            if (iTipoHistoricoTurma == 1) {
                console.log(dados['ed62_c_resultadofinal'].value);
                sResultadoFinal = dados['ed62_c_resultadofinal'].value;
            } else if (iTipoHistoricoTurma == 2) {
                console.log(dados['ed99_c_resultadofinal'].value)
                sResultadoFinal = dados['ed99_c_resultadofinal'].value;
            }

            var aDisciplinas = oDataGridDisciplinas.aRows;
            if (sResultadoFinal == 'R' && aDisciplinas.length > 0) {

                var lPossuiDisciplinaReprovada = false;

                aDisciplinas.each(function(oDisciplina, iContador) {
                    if (oDisciplina.aCells[7].getValue() == 'R') {
                        lPossuiDisciplinaReprovada = true;
                    }
                });

                if (!lPossuiDisciplinaReprovada) {

                    alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'resultado_final_etapa_reprovado'));
                    return false;
                }
            }

            oWindowDisciplinas.destroy();
            dados.location.href = dados.location.href + '&lFechou=true&iQtdDisciplinas=' + oDataGridDisciplinas.getRows().length;
            arvore.location.reload();
        });

        var oMessageBoard = new DBMessageBoard('messageboard1',
            _M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'message_board_titulo'),
            _M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'message_board_ajuda'),
            oWindowDisciplinas.getContentContainer()
        );
        oMessageBoard.show();
        oWindowDisciplinas.show();

        oTxtCodigo = new DBTextField('oTxtCodigo', 'oTxtCodigo', '', '10');
        oTxtCodigo.setReadOnly(true);
        oTxtCodigo.show($('ctnCodigo'));

        oTxtCodigoDisciplina = new DBTextField('oTxtCodigoDisciplina', 'oTxtCodigoDisciplina', '', '10');
        oTxtCodigoDisciplina.addEvent('onChange', ";js_pesquisarDisciplina(false)");
        oTxtCodigoDisciplina.show($('ctnCodigoDisciplina'));

        oTxtDescricaoDisciplina = new DBTextField('oTxtDescricaoDisciplina', 'oTxtDescricaoDisciplina', '', '30');
        oTxtDescricaoDisciplina.setReadOnly(true);
        oTxtDescricaoDisciplina.show($('ctnDescricaoDisciplina'));

        oCboSituacao = new DBComboBox('oCboSituacao', 'oCboSituacao');
        oCboSituacao.addItem('', '');
        oCboSituacao.addItem('CONCLUÍDO', 'CONCLUÍDO');
        oCboSituacao.addItem('AMPARADO', 'AMPARADO');
        oCboSituacao.addItem('NÃO OPTANTE', 'NÃO OPTANTE');
        oCboSituacao.addEvent('onChange', ";js_validarSituacao(false)");
        oCboSituacao.show($('ctnCboSituacao'));
        oCboBaseComum = new DBComboBox('oCboBaseComum', 'oCboBaseComum');
        oCboBaseComum.addItem('0', 'Selecione');
        PHPSession.loadData().then(async () => {
            await HttpClient.get(`${PHPSession.requestApi}/educacao/secretaria/tipo-base/buscarTodos`).then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                response.data.each(tipo => {
                    oCboBaseComum.addItem(tipo.ed182_id, tipo.ed182_descricao);
                });
            });
        });
        oCboBaseComum.show($('ctnCboBaseComun'));

        oCboAreaConhecimento = new DBComboBox('oCboAreaConhecimento', 'oCboAreaConhecimento', ['Selecione']);
        js_montaCboAreasConhecimento(oCboAreaConhecimento, iHistoricomps, iTipoHistorico);
        oCboAreaConhecimento.show($('ctnCboAreaConhecimento'));

        oCboResultado = new DBComboBox('oCboResultado', 'oCboResultado');
        oCboResultado.addItem('', '');
        oCboResultado.show($('ctnCboResultado'));

        oTxtCargaHoraria = new DBTextField('oTxtCargaHoraria', 'oTxtCargaHoraria', '', '10');
        oTxtCargaHoraria.addStyle('background-color', '#E6E4F1');
        oTxtCargaHoraria.addEvent('onInput', "js_ValidaCampos(this, 4, 'Carga Horária', true, false);");
        oTxtCargaHoraria.show($('ctnTxtCargaHoraria'));
        $('oTxtCargaHoraria').maxLength = 7;

        oTxtAproveitamento = new DBTextField('oTxtAproveitamento', 'oTxtAproveitamento', '', '10');
        oTxtAproveitamento.show($('ctnTxtAproveitamento'));

        oDataGridDisciplinas = new DBGrid('gridDisciplinas');
        oDataGridDisciplinas.sNameInstance = 'oDataGridDisciplinas';
        oDataGridDisciplinas.setCellWidth(new Array("5%", "5%", "20%", "20%", "19%", "7%", "4%", "5%", "10%", "5%"));
        oDataGridDisciplinas.setCellAlign(new Array("center", "center",
            "left",
            "left",
            "left",
            "center",
            "center",
            "center",
            "center"
        ));
        oDataGridDisciplinas.setHeader(new Array('Cod.', 'Cod. Disc.',
            'Disciplina',
            'Área de Conhecimento',
            'Tipo da Base',
            'Situação',
            'CH',
            'Resultado',
            'Aproveitamento',
            'Ação'));

        oDataGridDisciplinas.show($('ctnGridDisciplinas'));

        oTxtCodigoJustificativa = new DBTextField('oTxtCodigoJustificativa', 'oTxtCodigoJustificativa', '', '10');
        oTxtCodigoJustificativa.addEvent('onChange', ";js_pesquisarJustificativa(false)");
        oTxtCodigoJustificativa.show($('ctnTxtCodigoJustificativa'));

        oTxtDescricaoJustificativa = new DBTextField('oTxtDescricaoJustificativa', 'oTxtDescricaoJustificativa', '', '30');
        oTxtDescricaoJustificativa.setReadOnly(true);
        oTxtDescricaoJustificativa.show($('ctnTxtDescricaoJustificativa'));

        oTxtTermoFinal = new DBTextField('oTxtTermoFinal', 'oTxtTermoFinal', '', 10);
        oTxtTermoFinal.setMaxLength(4);
        oTxtTermoFinal.show($('ctnTxtTermoFinal'));

        $('btnSalvarDisciplina').observe('click', js_validarDadosDisciplina);
        $('btnLimparDadosDisciplina').observe('click', js_limparDadosDisciplina);

        js_buscarDisciplina();
        js_pesquisaTermos(iCodigoHistoricoAno, iEnsino, iAnoReferencia);
        js_limparDadosDisciplina();
    }

    /**
     * Pesquisamos os termos para apresentacao no comboBox Resultado
     */
    function js_pesquisaTermos(iCodigoHistoricoAno, iEnsino, iAnoReferencia) {

        var oParametro = new Object();
        oParametro.exec = 'pesquisaTermos';
        oParametro.iCodigoHistoricoAno = iCodigoHistoricoAno;
        oParametro.iEnsino = iEnsino;
        oParametro.iAnoReferencia = iAnoReferencia
        new Ajax.Request(sUrlRPC, {
            asynchronous: false,
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: js_retornaPesquisaTermos
        });
    }

    function js_retornaPesquisaTermos(oResponse) {

        var oRetorno = JSON.parse(oResponse.responseText);
        if (oRetorno.status == 1 && oRetorno.aTermos.length > 0) {

            oRetorno.aTermos.each(function(oLinha, iContador) {

                oCboResultado.addItem(oLinha.sReferencia.urlDecode(), oLinha.sDescricao.urlDecode());
            });
        } else {

            oCboResultado.addItem('A', 'APROVADO');
            oCboResultado.addItem('R', 'REPROVADO');
            oCboResultado.addItem('P', 'APROVADO PARCIALMENTE');
        }
    }

    function js_pesquisarDisciplina(lMostrar) {
        if (lMostrar) {
            js_OpenJanelaIframe('',
                'db_iframe_disciplina',
                'func_disciplina.php?ensino=' + iEnsinoTurma +
                '&funcao_js=parent.js_mostrarDisciplinas|ed12_i_codigo|ed232_c_descr',
                'Pesquisar Disciplinas',
                true
            );
            $('Jandb_iframe_disciplina').style.zIndex = 10000;
        } else {

            if (oTxtCodigoDisciplina.getValue() != "") {

                js_OpenJanelaIframe('',
                    'db_iframe_disciplina',
                    'func_disciplina.php?pesquisa_chave=' + oTxtCodigoDisciplina.getValue() +
                    '&ensino=' + iEnsinoTurma +
                    '&funcao_js=parent.js_mostrarDisciplinasErro',
                    'Pesquisar Disciplinas',
                    false
                );

            } else {
                oTxtDescricaoDisciplina.setValue('');
            }
        }
    }

    function js_mostrarDisciplinas(iCodigo, sDescricao) {

        oTxtCodigoDisciplina.setValue(iCodigo);
        oTxtDescricaoDisciplina.setValue(sDescricao);
        db_iframe_disciplina.hide();
    }

    function js_mostrarDisciplinasErro(sDescricao, lErro) {

        oTxtDescricaoDisciplina.setValue(sDescricao);
        if (lErro) {
            oTxtCodigoDisciplina.setValue('');
        }
    }

    function js_limparCamposArea() {
        inputCodigoHistoricoArea.value = '';
        codigoArea.value = '';
        descricaoArea.value = '';
        resultadoObtidoArea.value = '';
    }

    const btnLimpar = document.getElementById('limpar').addEventListener('click', () => {
        js_limparCamposArea();
    })

    /*
     * Função para mostrar/ocultar campos de acordo com a situação
     */
    function js_validarSituacao() {

        if (oCboSituacao.getValue() == 'AMPARADO') {

            oCboResultado.setDisable();
            oCboResultado.setValue('');
            oTxtAproveitamento.setReadOnly(true);
            oTxtAproveitamento.setValue('');
            oTxtCodigoJustificativa.setValue('');
            oTxtDescricaoJustificativa.setValue('');
            oTxtTermoFinal.setValue('');
            oTxtTermoFinal.setReadOnly(true);
            $('ctnLinhaJustificativa').style.visibility = 'visible';
        } else if (oCboSituacao.getValue() == 'NÃO OPTANTE') {

            oTxtCargaHoraria.setReadOnly(true);
            oCboResultado.setDisable(true);
            oTxtTermoFinal.setReadOnly(false);
            $('ctnLinhaJustificativa').style.visibility = 'hidden';
        } else {

            oCboResultado.setEnable();
            oTxtAproveitamento.setReadOnly(false);
            oTxtTermoFinal.setReadOnly(false);
            $('ctnLinhaJustificativa').style.visibility = 'hidden';
        }
    }

    /*
     * Função para validação dos dados da disciplina
     */
    function js_validarDadosDisciplina() {

        if (iTipoHistoricoTurma == 1) {

            sNotaMinima = dados.$F('ed62_c_minimo');
            sResultadoFinal = dados.$F('ed62_c_resultadofinal');
        }

        if (iTipoHistoricoTurma == 2) {

            sNotaMinima = dados.$F('ed99_c_minimo');
            sResultadoFinal = dados.$F('ed99_c_resultadofinal');
        }

        var lDadosValidados = true;

        if (oTxtCodigoDisciplina.getValue() == '') {

            alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'informe_disciplina'));
            lDadosValidados = false;
            return false;
        }

        if (oCboSituacao.getValue() == '') {

            alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'informe_situacao_disciplina'));
            lDadosValidados = false;
            return false;
        }

        if (oCboSituacao.getValue() == 'CONCLUÍDO') {

            if (oCboResultado.getValue() == '') {

                alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'informe_resultado_disciplina'));
                lDadosValidados = false;
                return false;
            }

            if (oTxtAproveitamento.getValue() == '') {

                alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'informe_aproveitamento_disciplina'));
                lDadosValidados = false;
                return false;
            }
        }

        if (oCboSituacao.getValue() == 'AMPARADO') {

            if (oTxtCodigoJustificativa.getValue() == '') {

                alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'informe_justificativa_amparo'));
                lDadosValidados = false;
                return false;
            }
        }

        if (oCboResultado.getValue() == 'R') {
            if (oTxtAproveitamento.getValue() > parseFloat(sNotaMinima)) {

                var oMensagem = {};
                oMensagem.sNotaMinima = sNotaMinima;
                alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'situacao_reprovado', oMensagem));
                lDadosValidados = false;
                return false;
            }
        }

        if (!empty($F('oTxtCargaHoraria'))) {

            var aValorCargaHoraria = $F('oTxtCargaHoraria').split('.');

            if (aValorCargaHoraria[0] == '' ||
                (aValorCargaHoraria[0] != '' && aValorCargaHoraria[0].length > 4)
            ) {

                alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'valor_invalido_carga_horaria'));
                $('oTxtCargaHoraria').focus();
                lDadosValidados = false;
                return false;
            }

            if (aValorCargaHoraria.length > 1) {

                if (aValorCargaHoraria[1] == '' ||
                    (aValorCargaHoraria[1] != '' && (aValorCargaHoraria[1].length == 0 || aValorCargaHoraria[1].length > 2))
                ) {

                    alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'valor_invalido_carga_horaria'));
                    $('oTxtCargaHoraria').focus();
                    lDadosValidados = false;
                    return false;
                }
            }
        }

        if (sResultadoFinal == 'A' && oCboSituacao.getValue() != 'AMPARADO' && oCboSituacao.getValue() != 'NÃO OPTANTE') {

            if (oCboResultado.getValue() != 'A') {

                alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'resultado_final_aprovado'));
                lDadosValidados = false;
                return false;
            }

            if (oTxtAproveitamento.getValue() < parseFloat(sNotaMinima) && $F('lExibeMensagemAproveitamentoMinimo') == 'true') {

                var oMensagem = {};
                oMensagem.sNotaMinima = sNotaMinima;

                if (!confirm(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'situacao_aprovado', oMensagem))) {

                    $('lExibeMensagemAproveitamentoMinimo').value = true;
                    lDadosValidados = false;
                    return false;
                } else {
                    $('lExibeMensagemAproveitamentoMinimo').value = false;
                }
            }
        }

        if (lDadosValidados == true) {
            js_incluirDisciplina();
        }
    }

    function js_pesquisarJustificativa(lMostra) {

        if (lMostra) {

            js_OpenJanelaIframe(
                '',
                'db_iframe_justificativa',
                'func_justificativa.php?funcao_js=parent.js_mostrarJustificativa|ed06_i_codigo|ed06_c_descr',
                'Pesquisar Justificativa',
                true
            );
            $('Jandb_iframe_justificativa').style.zIndex = 10000;
        } else {

            if (oTxtCodigoJustificativa.getValue() != "") {

                js_OpenJanelaIframe(
                    '',
                    'db_iframe_justificativa',
                    'func_justificativa.php?pesquisa_chave=' + oTxtCodigoJustificativa.getValue() +
                    '&funcao_js=parent.js_mostrarJustificativaErro',
                    'Pesquisar Justificativa',
                    false
                );
            }
        }
    }

    function js_mostrarJustificativa(iCodigo, sDescricao) {

        oTxtCodigoJustificativa.setValue(iCodigo);
        oTxtDescricaoJustificativa.setValue(sDescricao);
        db_iframe_justificativa.hide();
    }

    function js_mostrarJustificativaErro(sDescricao, lErro) {

        oTxtDescricaoJustificativa.setValue(sDescricao);
        if (lErro) {
            oTxtCodigoJustificativa.setValue('');
        }
    }

    /*
     * Função para buscar as disciplinas vinculadas ao aluno
     */
    function js_buscarDisciplina() {

        var oParametro = new Object();
        oParametro.exec = 'getDisciplinasHistorico';
        oParametro.iCodigoHistoricoAno = iCodigoHistorico;
        oParametro.iTipoHistorico = iTipoHistoricoTurma;

        new Ajax.Request(
            sUrlRPC, {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_preencherDisciplinas
            }
        );
    }


    function js_atualizaAreaConhecimento(codigoDisciplinaEtapa, codigoAreaEtapa) {
        var oParametro = {};
        oParametro.exec = 'vincularAreaDisciplinaHistorico';
        oParametro.iDisciplinaEtapa = codigoDisciplinaEtapa;
        oParametro.iCodigoAreaEtapa = codigoAreaEtapa;
        oParametro.iTipoHistorico = iTipoHistoricoTurma;
        js_divCarregando('Salvando...', 'loading_message', true);
        new Ajax.Request(sUrlRPC, {
            asynchronous: false,
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: (response) => {
                js_removeObj('loading_message');
                var oRetorno = JSON.parse(response.responseText);

                if (oRetorno.status == 2) {
                    alert(oRetorno.message);
                }
            }
        });
    }
    /*
     * Função para preencher o DBGrid com as disciplinas
     */
    function js_preencherDisciplinas(oResponse) {

        var oRetorno = JSON.parse(oResponse.responseText);
        oDataGridDisciplinas.clearAll(true);
        aDisciplinasLancadas = oRetorno.disciplinas;
        oRetorno.disciplinas.each(function(oLinha, iContador) {
            const cboAreaConhecimento = oCboAreaConhecimento.getElement();
            cboAreaConhecimento.classList.add('classAreaConhecimento');
            cboAreaConhecimento.setAttribute('onchange', `js_atualizaAreaConhecimento(${oLinha.codigo}, this.value)`)
            cboAreaConhecimento.setAttribute('data-codigoarea', 0);
            if (!empty(oLinha.codigo_area)) {
                cboAreaConhecimento.setAttribute('data-codigoarea', oLinha.codigo_area);
            }
            var aLinha = new Array();
            var aCargaHoraria = oLinha.carga_horaria.split('.');

            aLinha[0] = oLinha.codigo;
            aLinha[1] = oLinha.codigo_disciplina;
            aLinha[2] = oLinha.descricao_disciplina.urlDecode();
            aLinha[3] = cboAreaConhecimento.outerHTML;
            aLinha[4] = oLinha.tipoBase.urlDecode();
            aLinha[5] = oLinha.situacao.urlDecode();
            aLinha[6] = aCargaHoraria[0];
            aLinha[7] = oLinha.resultado_final.urlDecode();
            aLinha[8] = oLinha.resultado_obtido.urlDecode();
            aLinha[9] = '<input type="button" value="A" onclick="js_carregaDadosDisciplina(' + oLinha.codigo + ')" />';
            aLinha[9] += '<input type="button" value="E" onclick="js_excluirDisciplina(' + oLinha.codigo + ')" />';
            oDataGridDisciplinas.addRow(aLinha);
        });

        oDataGridDisciplinas.renderRows();

        const selectsAreasConhecimento = document.querySelectorAll('.classAreaConhecimento');
        selectsAreasConhecimento.forEach((area) => {
            area.value = area.getAttribute('data-codigoarea');
        })
    }

    /*
     * Função para incluir uma disciplina
     */
    function js_incluirDisciplina() {

        var iCodigoDisciplina = oTxtCodigoDisciplina.getValue();
        var iCodigoAreaEtapa = oCboAreaConhecimento.getValue();
        var iJustificativa = '';
        var iCargaHoraria = oTxtCargaHoraria.getValue();
        var iResultado = oCboResultado.getValue();
        var iAproveitamento = oTxtAproveitamento.getValue();
        var iSituacao = oCboSituacao.getValue();
        var sTipoResultado = 'N';
        var iOrdenacao = 0;
        var iCodigoLancamento = oTxtCodigo.getValue();
        var sTermoFinal = oTxtTermoFinal.getValue();
        var lTipoBase = oCboBaseComum.getValue();

        if (iSituacao == 'AMPARADO') {

            sTipoResultado = 'A';
            iJustificativa = oTxtCodigoJustificativa.getValue();
        }

        js_divCarregando(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'salvando_disciplina'), "msgBox");
        var oParametro = new Object();
        oParametro.exec = 'incluirDisciplinaHistorico';
        oParametro.iCodigoAluno = $F('ed47_i_codigo');
        oParametro.iCodigoHistoricoAno = iCodigoHistorico;
        oParametro.iCodigoAreaEtapa = iCodigoAreaEtapa;
        oParametro.iTipoHistorico = iTipoHistoricoTurma;
        oParametro.iHistoricomps = iHistoricompsAluno;
        oParametro.iCodigoDisciplina = iCodigoDisciplina;
        oParametro.iJustificativa = iJustificativa;
        oParametro.iCargaHoraria = iCargaHoraria;
        oParametro.iResultado = iResultado;
        oParametro.iAproveitamento = btoa(iAproveitamento);
        oParametro.iSituacao = encodeURIComponent(tagString(iSituacao));
        oParametro.sTipoResultado = sTipoResultado;
        oParametro.iOrdenacao = iOrdenacao;
        oParametro.iCodigoLancamento = iCodigoLancamento;
        oParametro.iCodigoCurso = iCodigoCurso;
        oParametro.sTermoFinal = sTermoFinal;
        oParametro.lTipoBase = lTipoBase;

        var oAjax = new Ajax.Request(
            sUrlRPC, {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_salvarDisciplina
            }
        );
    }

    /*
     * Função que salva a Disciplina
     */
    function js_salvarDisciplina(oResponse) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oResponse.responseText);
        alert(oRetorno.message.urlDecode());
        if (oRetorno.status == 1) {

            js_limparDadosDisciplina();
            js_buscarDisciplina();
        }
    }

    function js_excluirDisciplina(iCodigoDisciplina) {

        if (confirm('Confirma a exclusão da disciplina?')) {

            js_divCarregando(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'excluindo_disciplina'), "msgBox");
            var oParametro = new Object();
            oParametro.exec = 'excluirDisciplinaHistorico';
            oParametro.iCodigoHistoricoAno = iCodigoHistorico;
            oParametro.iTipoHistorico = iTipoHistoricoTurma;
            oParametro.iHistoricomps = iHistoricompsAluno;
            oParametro.iDisciplina = iCodigoDisciplina;
            oParametro.iCodigoAluno = $F('ed47_i_codigo');
            oParametro.iCodigoCurso = iCodigoCurso;
            new Ajax.Request(
                sUrlRPC, {
                    method: 'post',
                    parameters: 'json=' + Object.toJSON(oParametro),
                    onComplete: function(oResponse) {

                        js_removeObj('msgBox');
                        var oRetorno = JSON.parse(oResponse.responseText);
                        alert(oRetorno.message.urlDecode());
                        if (oRetorno.status == 1) {

                            js_limparDadosDisciplina();
                            js_buscarDisciplina();
                        }
                    }
                }
            );
        }
    }

    function js_carregaDadosDisciplina(iCodigoLancamento) {
        var iCodigoDisciplina = oTxtCodigoDisciplina.getValue();

        var oParametro = new Object();
        oParametro.exec = 'carregaDadosDisciplina';
        oParametro.iCodigo = iCodigoLancamento;
        oParametro.iCodigoHistoricoAno = iCodigoHistorico;
        oParametro.iTipoHistorico = iTipoHistoricoTurma;
        oParametro.iHistoricomps = iHistoricompsAluno;
        oParametro.iDisciplina = iCodigoDisciplina;
        oParametro.iCodigoAluno = $F('ed47_i_codigo');
        oParametro.iCodigoCurso = iCodigoCurso;
        new Ajax.Request(
            sUrlRPC, {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_mostraDados
            }
        );

        oDataGridDisciplinas.aRows.map(linha => {
            if (iCodigoLancamento == linha.aCells[0].content) {
                let opcoes = oCboAreaConhecimento.getElement().options;
                let iOpcoes = oCboAreaConhecimento.getElement().options.length;
                let lAchou = false;
                for (var i = 0; i < iOpcoes; i++) {
                    if (opcoes[i].innerText == linha.aCells[3].content) {
                        oCboAreaConhecimento.setValue(opcoes[i].value);
                        lAchou = true;
                    }
                }
                if (!lAchou) {
                    oCboAreaConhecimento.setValue(0);
                }
            }
        });
    }

    function js_mostraDados(oResponse) {

        var oRetorno = JSON.parse(oResponse.responseText);
        if (oRetorno.status == 1) {

            oTxtCodigoDisciplina.setValue(oRetorno.oDisciplina.iCodigoDisciplina);
            oTxtDescricaoDisciplina.setValue(oRetorno.oDisciplina.sDescricaoDisciplina.urlDecode());
            oTxtCargaHoraria.setValue(oRetorno.oDisciplina.iCargaHoraria);
            oCboResultado.setValue(oRetorno.oDisciplina.sResultado);
            oTxtCodigo.setValue(oRetorno.oDisciplina.iCodigoLancamento);
            oCboBaseComum.setValue(oRetorno.oDisciplina.lTipoBase);
            oCboSituacao.setValue(oRetorno.oDisciplina.sSituacao.urlDecode());
            oTxtAproveitamento.setValue(oRetorno.oDisciplina.nAproveitamento.urlDecode());
            oTxtTermoFinal.setValue(oRetorno.oDisciplina.sTermoFinal);
            js_validarSituacao();
            oTxtCodigoJustificativa.setValue(oRetorno.oDisciplina.iJustificativa);
            if (oRetorno.oDisciplina.iJustificativa != "") {
                js_pesquisarJustificativa(false)
            }
        }
    }

    function js_limparDadosDisciplina() {

        oTxtCodigo.setValue('');
        oTxtCodigoDisciplina.setValue('');
        oTxtDescricaoDisciplina.setValue('');
        oCboSituacao.setValue('');
        oTxtCargaHoraria.setValue('');
        oCboResultado.setValue('');
        oTxtAproveitamento.setValue('');
        oTxtCodigoJustificativa.setValue('');
        oTxtDescricaoJustificativa.setValue('');
        oTxtTermoFinal.setValue('');
        oCboBaseComum.setValue('');

        js_preencherDadosPadrao();
    }

    function js_emiteHistorico() {
        let janela = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0];
        let rota = "/web/educacao/escola/alunos/historico-escolar/";
        let aluno = document.querySelector('#ed47_i_codigo').value;
        js_OpenJanelaIframe('',
            'db_iframe_justificativa',
            `${janela}${rota}${aluno}`,
            'Emitir Histórico',
            true
        );
    }


    function js_validaEmissaoCertificado() {

        var oParam = new Object();
        oParam.exec = "validaEmissaoCertificado";
        oParam.iAluno = $F('ed47_i_codigo')
        var sUrl = 'edu4_historicoaluno.RPC.php';

        new Ajax.Request(sUrl, {
            method: 'post',
            asynchronous: false,
            parameters: 'json=' + Object.toJSON(oParam),
            onComplete: function(oAjax) {

                var oRetorno = JSON.parse(oAjax.responseText);

                if (oRetorno.status == 2) {

                    alert(oRetorno.message.urlDecode());
                    return;
                }

                $('impCertificado').disabled = false;
                if (!oRetorno.lPermiteImpressao) {
                    $('impCertificado').disabled = true;
                }
            }
        });
    }

    function js_emiteCertificado() {

        var sUrl = "edu2_certconclusao001.php";
        sUrl += "?ed47_i_codigo=" + $F('ed47_i_codigo');
        sUrl += "&ed47_v_nome=" + $F('ed47_v_nome');

        js_OpenJanelaIframe('',
            'db_iframe_justificativa',
            sUrl,
            'Emitir Certificado',
            true
        );
    }

    var lAlunoTemHistorico = false;

    function js_validaAluno() {

        if ($F('ed47_i_codigo') == '') {

            alert(_M(MENSAGENS_MANUTENCAO_HISTORICO_000 + 'selecione_aluno'));
            return false;
        }

        var oParam = new Object();
        oParam.exec = "alunoTemHistorico";
        oParam.iAluno = $F('ed47_i_codigo')
        var sUrl = 'edu4_historicoaluno.RPC.php';

        new Ajax.Request(sUrl, {
            method: 'post',
            asynchronous: false,
            parameters: 'json=' + Object.toJSON(oParam),
            onComplete: function(oAjax) {

                var oRetorno = JSON.parse(oAjax.responseText);

                $('impHistorico').disabled = false;
                if (!oRetorno.lTemHistorico) {
                    $('impHistorico').disabled = true;
                }
                lAlunoTemHistorico = oRetorno.lTemHistorico;
            }
        });
    }

    function js_preencherDadosPadrao() {

        var oParam = new Object();
        oParam.exec = "getDadosEtapa";
        oParam.sTipoEtapa = this.dados.$F("sTipoRede");

        if (oParam.sTipoEtapa == 1) {
            oParam.iCodigoEtapa = this.dados.$F('ed62_i_codigo');
        } else {
            oParam.iCodigoEtapa = this.dados.$F('ed99_i_codigo');
        }

        var sUrl = 'edu4_historicoaluno.RPC.php';
        new Ajax.Request(sUrl, {
            method: 'post',
            asynchronous: false,
            parameters: 'json=' + Object.toJSON(oParam),
            onComplete: function(oAjax) {

                var oRetorno = JSON.parse(oAjax.responseText);


                oCboSituacao.getElement().value = oRetorno.oEtapa.sSituacao.urlDecode();

                if (oRetorno.oEtapa.sSituacao.urlDecode() == 'RECLASSIFICADO') {
                    oCboSituacao.getElement().value = 'CONCLUÍDO';
                }
                oCboResultado.getElement().value = oRetorno.oEtapa.sResultado.urlDecode();
            }
        });

        return true;
    }

    var lPrimeiroAcessoView = true;

    if ($F('ed47_i_codigo') != '') {

        js_validaAluno();
        if (lAlunoTemHistorico) {
            js_validaEmissaoCertificado();
        }
    }

    var oDadosManutencaoHistorico;

    (function() {

        if ($F('ed47_i_codigo') != '') {
            oDadosManutencaoHistorico = verificaSituacaoManutencaoHistorico($F('ed47_i_codigo'));
        }
    })();

    /**
     * Verifica o status de manutenção do histórico
     *
     * @param {int} iCodigoAluno Código do aluno
     */
    function verificaSituacaoManutencaoHistorico(iCodigoAluno) {

        var oParams = {},
            oDados = null;

        oParams.exec = 'buscaStatusManutencaoHistorico';
        oParams.iCodigoAluno = iCodigoAluno;


        new Ajax.Request(sUrlRPC, {
            method: 'post',
            asynchronous: false,
            parameters: 'json=' + Object.toJSON(oParams),
            onComplete: function(oAjax) {

                var oRetorno = JSON.parse(oAjax.responseText);

                if (oRetorno.status == 2) {

                    alert(oRetorno.message.urlDecode());
                    return false;
                }

                oDados = oRetorno;
            }
        });

        return oDados;
    }
</script>
