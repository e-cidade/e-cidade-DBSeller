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

$clorcparamrel->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("o112_descricao");
if ($db_opcao == 1) {
    $db_action = "orc1_orcparamrel004.php";
} else {
    if ($db_opcao == 2 || $db_opcao == 22) {
        $db_action = "orc1_orcparamrel005.php";
    } else {
        if ($db_opcao == 3 || $db_opcao == 33) {
            $db_action = "orc1_orcparamrel006.php";
        }
    }
}
?>
<style>
    .modal-open .modal {
        overflow-x: hidden;
        overflow-y: auto;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        display: none;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }

    .modal-dialog {
        position: relative;
        width: auto;
        margin: .5rem;
        pointer-events: none;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translate(0, -50px);
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    .modal-dialog-scrollable .modal-content {
        max-height: calc(100vh - 1rem);
        overflow: hidden;
    }

    .modal-dialog-scrollable .modal-header,
    .modal-dialog-scrollable .modal-footer {
        flex-shrink: 0;
    }

    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
    }

    .modal-dialog-centered.modal-dialog-scrollable .modal-content {
        max-height: none;
    }

    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #e1dede;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, .2);
        border-radius: .3rem;
        outline: 0;
    }

    .modal-header {
        background-color: #326094;
        display: flex;
        justify-content: space-between;
        padding: 0 1rem;
        border-top-left-radius: .3rem;
        border-top-right-radius: .3rem;
        align-items: center;
        border: 1px solid #326094;
    }

    .modal-header label {
        margin-top: 7px;
        margin-bottom: 7px;
        color: white;
    }

    .modal-header .close {
        padding: 1rem;
        margin: -1rem -1rem -1rem auto;
    }

    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
    }

    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: .5rem 1rem;
        border-top: 1px solid #9c9c9c;
        border-bottom-right-radius: .3rem;
        border-bottom-left-radius: .3rem;
    }

    .modal-footer > :not(:first-child) {
        margin-left: .25rem;
    }

    .modal-footer > :not(:last-child) {
        margin-right: .25rem;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 3.5rem);
        }
    }

    button.close {
        cursor: pointer;
        padding: 0;
        background-color: transparent;
        border: 0;
        appearance: none;
    }

    .form-check {
        position: relative;
        display: block;
        padding-left: 1.25rem;
    }

    .form-check-input {
        cursor: pointer;
        box-sizing: border-box;
        padding: 0;
        position: absolute;
        margin-top: .3rem;
        margin-left: -1.25rem;
    }

    .form-check-label {
        cursor: pointer;
        margin-bottom: 5px;
        display: inline-block;
        padding-top: 7px;
        padding-left: 5px;
    }
</style>
<div class="container">
    <form name="form1" method="post" action="<?= $db_action ?>" id="formRelatorio">
        <fieldset style="width:630px">
            <table class="form-container">
                <tr style="display: none;">
                    <td nowrap title="<?= $To42_codparrel ?>">
                        <?= $Lo42_codparrel ?>
                    </td>
                    <td>
                        <?php
                        db_input('o42_codparrel', 8, $Io42_codparrel, true, 'text', 3, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $To42_orcparamrelgrupo ?>">
                        <?php
                        db_ancora('Grupo do Relatório:', "js_pesquisao42_orcparamrelgrupo(true);", $db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'o42_orcparamrelgrupo',
                            10,
                            $Io42_orcparamrelgrupo,
                            true,
                            'text',
                            $db_opcao,
                            " onchange='js_pesquisao42_orcparamrelgrupo(false);'"
                        );
                        db_input('o112_descricao', 50, $Io112_descricao, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="o42_descrrel">Descrição:</label>
                    </td>
                    <td>
                        <?php
                        db_input('o42_descrrel', 64, $Io42_descrrel, true, 'text', $db_opcao)
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Selecione o arquivo de template:</label>
                    </td>
                    <td>
                        <div id="ctnImportacao"></div>
                        <input type="hidden" name="templatePath" id="templatePath" />
                    </td>

                </tr>
                <tr>
                    <td>
                        <label for="o42_notapadrao">Nota Explicativa Padrão:</label>
                    </td>
                    <td>
                        <?php
                        db_textarea('o42_notapadrao', 10, 64, $Io42_descrrel, true, 'text', $db_opcao);
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
               type="submit" id="db_opcao"
               value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> >
        <?php
        if ($db_opcao !== 1) {
            ?>
            <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
            <?php
        }
        if ($db_opcao === 2) {
            ?>
            <input type="button" id="exportar" value="Exportar"/>
            <?php
        }
        if (in_array($db_opcao, array(1, 2, 22))) {
            ?>
            <input type="button" id="importarRelatorio" value="Importar" onClick="js_importar()"/>
            <?php
        }
        ?>
    </form>
</div>

<div class="modal" tabindex="-1" role="dialog" id="janelaExportacao">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label>
                    <strong>Exportar</strong>
                </label>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
            </div>
            <div class="modal-body">
                <form name="exportacaoForm" id="exportacaoForm">
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <label for="formatoExportacao">
                                    <strong>Formato:</strong>
                                </label>
                            </td>
                            <td>
                                <select id="formatoExportacao" name="formatoExportacao" class="form-control">
                                    <option value="json">JSON</option>
                                    <option value="sql">SQL</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="form-check">
                                    <input type="checkbox" value="relatorio" name="exportarRelatorio"
                                           id="exportarRelatorio" class="form-check-input" checked>
                                    <label for="exportarRelatorio"
                                           title="Toda configuração do relatório, períodos e linhas vínculados assim como a configuração padrão."
                                           class="form-check-label">Relatório</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" value="periodos" name="exportarPeriodos"
                                           id="exportarPeriodos" class="form-check-input">
                                    <label for="exportarPeriodos" title="Caso seja cadastrado um novo tipo de período."
                                           class="form-check-label">Períodos</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" value="colunas" name="exportarColunas" id="exportarColunas"
                                           class="form-check-input">
                                    <label for="exportarColunas" title="Caso seja cadastrado uma coluna nova."
                                           class="form-check-label">Colunas</label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"  id="exportarArquivo"
                        name="exportarArquivo">
                    <i class="fas fa-download"></i>
                    Exportar
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="fecharExportacao"
                        name="fecharExportacao" >
                    <i class="far fa-window-close"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>
<script src="scripts/prototype.js"></script>
<script src="scripts/strings.js"></script>
<script src="scripts/classes/http/http.js"></script>
<script src="scripts/widgets/DBDownload.widget.js"></script>
<script src="scripts/widgets/DBFileUpload.widget.js"></script>
<script>
    (() => {
        const inputBotao = document.getElementById('db_opcao');

        document.getElementById('db_opcao').addEventListener('click', e => {
            if (inputBotao.value === 'Excluir') {
                if (confirm('Confirmar exclusão do relatório?')) {
                    return true;
                }

                e.preventDefault();

                return false;
            }
        });
    })();
</script>
<script>
    const sRPC = 'con4_relatorioslegais.RPC.php';
    const get = js_urlToObject();
    const oFileUpload = new DBFileUpload({callBack: js_retornoEnvioArquivo});
    const selectInput = document.getElementById('formatoExportacao');

    const fileTemplate = new DBFileUpload({callBack: retornoEnvioTemplate, labelButton: 'Arquivo'});
    fileTemplate.show($('ctnImportacao'));

    if ($('exportar')) {
        $('exportar').addEventListener('click', function() {
            $('janelaExportacao').style.display = 'block';
        });
    }

    document.querySelector('.close').addEventListener('click', () => $('janelaExportacao').style.display = 'none');

    $('fecharExportacao').addEventListener('click', () => {
        $('janelaExportacao').style.display = 'none';
    });

    $('exportarArquivo').addEventListener('click', () => {
        const formData = new FormData($('exportacaoForm'));
        formData.append('acao', 'exportarRelatorio');
        formData.append('codigoRelarotio', $F('o42_codparrel'));

        const nodes = Array.from(document.querySelectorAll('input[type="checkbox"]'));
        nodes.forEach(elemento => {
            formData.append(elemento.name, elemento.checked);
        });

        HttpClient.post('con4_gerarrealtoriolegal.RPC.php', {
            body: formData
        }).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }

            $('janelaExportacao').style.display = 'none';
            const download = new DBDownload();
            download.addFile(response.filePath, `relatorio.${selectInput.value}`);
            download.show();
        });
    });

    <?php if ($db_opcao != 1) : ?>
    if ($('importarRelatorio')) {
        $('importarRelatorio').disabled = true;
    }

    if ($('importarRelatorio') && !empty($('o42_codparrel').value)) {
        $('importarRelatorio').disabled = false;
    }

    <?php endif; ?>

    function js_exportar() {
        js_divCarregando('Aguarde, exportando arquivo...', 'msgBox');

        const oParametros = {
            exec: 'exportarRelatorio',
            iCodigoRelatorio: $('o42_codparrel').value
        };

        new Ajax.Request(sRPC, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametros),
            onComplete: function(oAjax) {
                js_removeObj('msgBox');

                const oRetorno = JSON.parse(oAjax.responseText);
                const sMensagem = oRetorno.message.urlDecode();

                alert(sMensagem);

                if (oRetorno.status > 1) {
                    return false;
                }

                const oDownload = new DBDownload();
                oDownload.addFile(oRetorno.sCaminho.urlDecode(), oRetorno.sCaminho.urlDecode());
                oDownload.show();
            }
        });
    }

    function js_importarArquivo() {
        js_divCarregando('Aguarde, importando arquivo...', 'msgBox');

        const oParametros = {
            exec: 'importarRelatorio',
            iCodigoRelatorio: $('o42_codparrel').value,
            sCaminhoArquivo: oFileUpload.filePath
        };

        new Ajax.Request(sRPC, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametros),
            onComplete: function(oAjax) {

                js_removeObj('msgBox');
                const oRetorno = JSON.parse(oAjax.responseText);
                const sMensagem = oRetorno.message.urlDecode();

                alert(sMensagem);

                if (oRetorno.status > 1) {
                    return false;
                }

                document.location.href = 'orc1_orcparamrel005.php?chavepesquisa=' + oRetorno.iCodigoRelatorio;
            }
        });
    }

    function js_retornoEnvioArquivo(oRetorno) {
        if (oRetorno.error) {
            alert(oRetorno.error);
            $('importarArquivo').disabled = true;
            return false;
        }

        $('importarArquivo').disabled = false;
    }

    function js_importar() {
        if (typeof windowExerc !== 'undefined') {
            windowExerc.destroy();
        }

        var sConteudoWindowAux = '<div id="importarRelatorio">';
        sConteudoWindowAux += '  <div id="msgImportarRelatorio" style="overflow:hidden;"></div>';
        sConteudoWindowAux += '  <fieldset>';
        sConteudoWindowAux += '    <legend>Importar Arquivo </legend>';
        sConteudoWindowAux += '    <div id="containerArquivo"></div>';
        sConteudoWindowAux += '  </fieldset>';
        sConteudoWindowAux += '  <center>';
        sConteudoWindowAux += '    <br /><input type="button" disabled id="importarArquivo" value="Importar" onClick="js_importarArquivo();" />';
        sConteudoWindowAux += '  </center>';
        sConteudoWindowAux += '</div>';

        windowExerc = new windowAux('importarRelatorio', 'Importação dos dados do relatório legal', 590, 450);
        windowExerc.setContent(sConteudoWindowAux);
        windowExerc.show(0, 0, false, 0, 0);

        const sTitulo = 'Importação dos dados do relatório legal';
        oMessageBoard = new DBMessageBoard('messageBoardRelatorio', sTitulo, '', $('msgImportarRelatorio'));
        oMessageBoard.show();

        if (!empty(oFileUpload.filePath)) {
            $('importarArquivo').disabled = false;
        }

        oFileUpload.show($('containerArquivo'));
    }

    function js_pesquisao42_orcparamrelgrupo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo.iframe_orcparamrel',
                'db_iframe_orcparamrelgrupo',
                'func_orcparamrelgrupo.php?funcao_js=parent.js_mostraorcparamrelgrupo1|o112_sequencial|o112_descricao',
                'Pesquisa',
                true,
                '0',
                '1'
            );
        } else {
            if (document.form1.o42_orcparamrelgrupo.value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo.iframe_orcparamrel',
                    'db_iframe_orcparamrelgrupo',
                    'func_orcparamrelgrupo.php?pesquisa_chave=' + document.form1.o42_orcparamrelgrupo.value +
                    '&funcao_js=parent.js_mostraorcparamrelgrupo',
                    'Pesquisa',
                    false,
                    '0',
                    '1'
                );
            } else {
                document.form1.o112_descricao.value = '';
            }
        }
    }

    function js_mostraorcparamrelgrupo(chave, erro) {
        document.form1.o112_descricao.value = chave;
        if (erro == true) {
            document.form1.o42_orcparamrelgrupo.focus();
            document.form1.o42_orcparamrelgrupo.value = '';
        }
    }

    function js_mostraorcparamrelgrupo1(chave1, chave2) {
        document.form1.o42_orcparamrelgrupo.value = chave1;
        document.form1.o112_descricao.value = chave2;
        db_iframe_orcparamrelgrupo.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo.iframe_orcparamrel',
            'db_iframe_orcparamrel',
            'func_orcparamrel.php?funcao_js=parent.js_preenchepesquisa|o42_codparrel',
            'Pesquisa',
            true,
            '0',
            '1'
        );
    }

    function js_preenchepesquisa(chave) {
        db_iframe_orcparamrel.hide();
        <?php
        if ($db_opcao != 1) {
            ?>
        location.href = `<?php echo basename($_SERVER['PHP_SELF']); ?>?chavepesquisa=${chave}`;
            <?php
        }
        ?>
    }

    function retornoEnvioTemplate(retorno) {

        $('templatePath').value = "";

        if (retorno.error) {
            alert(retorno.error);
            return false;
        }

        if (retorno.extension.toLowerCase() != 'xls' && retorno.extension.toLowerCase() != 'xlsx') {
            alert('Arquivo inválido, extensão do arquivo deve ser xls ou xlsx.');
            return false;
        }

        $('templatePath').value = retorno.filePath;

    }
</script>
