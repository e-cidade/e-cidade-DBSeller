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

//MODULO: orcamento
use ECidade\Financeiro\Orcamento\Recurso\Complemento;
use ECidade\Financeiro\Orcamento\Recurso\Especificacao;
use ECidade\Financeiro\Orcamento\Recurso\Grupo;
use ECidade\Financeiro\Orcamento\Recurso\IdentificadorUso;
use ECidade\Financeiro\Orcamento\Recurso\TipoDetalhamento;

$clorctiporec = new cl_orctiporec;
$clorctiporec->rotulo->label();

$display = "display:none";
if (InstituicaoRepository::usaFonteRecursoUniao()) {
    $display = "";
}
?>
<div class="container">
    <form id="frmRecurso" name="form1" method="post" action="">
        <fieldset>
            <legend>Recurso</legend>
            <!-- Dados da LOA -->
            <fieldset class="separator">
                <legend class="bold">Fonte de Recurso</legend>
                <table class="form-container" style="max-width: 550px;">
                    <tr style="<?php echo $display; ?>">
                        <td class="bold"><label for="identificadorUso">Identificador de Uso:</label></td>
                        <td>
                            <select id="identificadorUso">
                                <option value="">Selecione</option>
                                <?php
                                foreach (IdentificadorUso::getAll() as $valor => $descricao) {
                                    echo "<option value='{$valor}'>{$descricao}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr style="<?php echo $display; ?>">
                        <td>
                            <label for="tipoDetalhamento">
                                <strong>Tipo de Detalhamento:</strong>
                            </label>
                        </td>
                        <td>
                            <select id="tipoDetalhamento">
                                <option value="">Selecione</option>
                                <?php
                                foreach (TipoDetalhamento::getAll() as $valor => $descricao) {
                                    echo "<option value='{$valor}'>{$descricao}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr style="<?php echo $display; ?>">
                        <td>
                            <label for="grupoFonteRecurso">
                                <strong>Grupo:</strong>
                            </label>
                        </td>
                        <td>
                            <select id="grupoFonteRecurso">
                                <option value="">Selecione</option>
                                <?php
                                foreach (Grupo::getAll() as $valor => $descricao) {
                                    echo "<option value='{$valor}'>{$descricao}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td id="tdespecificacaoFonte">
                            <label for="especificacaoFonte">
                                <strong>Especificação:</strong>
                            </label>
                        </td>
                        <td  id="tdespecificacaoFonteCombo">
                            <select id="especificacaoFonte">
                                <option value="">Selecione</option>
                                <?php
                                foreach (Especificacao::getAll() as $valor => $descricao) {
                                    echo "<option value='{$valor}'>{$descricao}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="complementoRecurso">
                                <strong>Complemento:</strong>
                            </label>
                        </td>
                        <td>
                            <select id="complementoRecurso">
                                <option value="">Selecione</option>
                                <?php
                                foreach (Complemento::getAll() as $valor => $descricao) {
                                    echo "<option value='{$valor}'>{$descricao}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="separator">
                <legend>Outras Informações</legend>
                <table>

                    <tr id="boxMascara" style="display: none;">
                        <td>
                            <b>Máscara:</b>
                        </td>
                        <td>
                            <?php db_input('sMascara', 40, null, true, 'text', 3); ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?php echo $To15_codtri; ?>">
                            <?php echo $Lo15_codtri; ?>
                        </td>
                        <td>
                            <?php db_input('o15_codtri', 40, $Io15_codtri, true, 'text', $db_opcao); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="o15_codigosiconfi"><?php echo $Lo15_codigosiconfi; ?></label>
                        </td>
                        <td>
                            <?php db_input('o15_codigosiconfi', 10, $Io15_codigosiconfi, true, 'text', $db_opcao, null, null, null, null, 8); ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?php echo $To15_descr; ?>">
                            <b>Descrição:</b>
                        </td>
                        <td>
                            <?php db_input('o15_descr', 40, $Io15_descr, true, 'text', $db_opcao); ?>
                        </td>
                    </tr>
                    <tr id="boxTipo" style="display: none;">
                        <td nowrap title="Tipo">
                            <b>Tipo:</b>
                        </td>
                        <td>
                            <?php
                            $aTipo = array(
                                '1' => 'Sintético',
                                '2' => 'Analítico'
                            );
                            db_select('iTipo', $aTipo, true, $db_opcao);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?php echo $To15_tipo; ?>">
                            <?php echo $Lo15_tipo; ?>
                        </td>
                        <td>
                            <?php
                            $aTipoRecurso = array(
                                '1' => 'Recurso Livre',
                                '2' => 'Recurso Vinculado'
                            );
                            db_select('o15_tipo', $aTipoRecurso, true, $db_opcao);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?php echo $To15_datalimite; ?>">
                            <b>Data Limite:</b>
                        </td>
                        <td>
                            <?php db_inputdata('o15_datalimite', @$o15_datalimite_dia, @$o15_datalimite_mes, @$o15_datalimite_ano, true, 'text', $db_opcao); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" nowrap title="<?php echo $To15_finali; ?>">
                            <fieldset class="separator">
                                <legend>Finalidade</legend>
                                <?php db_textarea('o15_finali', 2, 60, $Io15_finali, true, 'text', $db_opcao); ?>
                            </fieldset>
                        </td>
                    </tr>
                </table>

            </fieldset>
        </fieldset>

        <input
            name="<?php echo($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")); ?>"
            type="button"
            id="db_opcao"
            value="<?php echo($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")); ?>"
            <?php echo($db_botao == false ? "disabled" : ""); ?>>
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
        <?php
            db_input('o15_codigo', 10, $Io15_codigo, true, 'hidden', 3);
        ?>
    </form>
</div>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
<script>
    var sUrlRPC = 'orc4_manutencaoRecurso.RPC.php';

    const rota = '/financeiro/orcamento/cadastro/recurso/';
    const formulario = document.getElementById('frmRecurso');

    let cadastrarEspecificacao = false;
    let codigoEstrutural;

    function js_pesquisa() {
        var sUrl = 'func_orctiporec.php?funcao_js=parent.js_preenchepesquisa|o15_codigo&ativo=1';
        js_OpenJanelaIframe('', 'db_iframe_orctiporec', sUrl, 'Pesquisa', true);
    }

    $('especificacaoFonte').addEventListener('change', atualizaCodTri, false);
    $('grupoFonteRecurso').addEventListener('change', atualizaCodTri, false);

    function atualizaCodTri() {

        var especificacaoDaFonte = $('especificacaoFonte').value;
        var grupoDeFonteRecurso = $('grupoFonteRecurso').value;
        if (especificacaoDaFonte == '') {
            especificacaoDaFonte = '00';
        }
        if (grupoDeFonteRecurso == '') {
            grupoDeFonteRecurso = '00';
        }
    }


    function js_preenchepesquisa(chave) {
        db_iframe_orctiporec.hide();
        js_buscaDadosRecurso(chave);
    }

    function js_buscaDadosRecurso(iCodigoRecurso) {
        if ($('db_opcao').value != 'Incluir') {
            if (iCodigoRecurso == '') {
                alert("Informe o código do recurso!");
                return false;
            }
            js_divCarregando('Aguarde buscando recurso...', 'msgBoxGetDadosRecurso');

            var oParam = new Object();
            oParam.exec = "getDadosRecurso";
            oParam.codigorecurso = iCodigoRecurso;

            var oAjax = new Ajax.Request(
                sUrlRPC,
                {
                    method: 'post',
                    parameters: 'json=' + js_objectToJson(oParam),
                    onComplete: js_retornoDadosRecurso
                }
            );
        }
    }

    function js_retornoDadosRecurso(oAjax) {
        js_removeObj("msgBoxGetDadosRecurso");
        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode());
            return false;
        } else {
            $('o15_codigo').value = oRetorno.codigorecurso;
            $('o15_codigosiconfi').value = oRetorno.codigosiconfi;
            $('o15_descr').value = oRetorno.descricaorecurso.urlDecode();
            $('o15_codtri').value = oRetorno.codigotribunalrecurso;
            $('o15_finali').value = oRetorno.finalidaderecurso.urlDecode();

            $('identificadorUso').value = oRetorno.classificacao.identificador;
            $('tipoDetalhamento').value = oRetorno.classificacao.tipo;
            $('grupoFonteRecurso').value = oRetorno.classificacao.grupo;
            $('especificacaoFonte').value = oRetorno.classificacao.especificacao;
            $('complementoRecurso').value = oRetorno.classificacao.complemento;

            if ($('db_opcao').value == 'Alterar') {

                $('iTipo').value = oRetorno.tipo;
                $('o15_codigo').disabled = true;
                $('o15_codigo').style.backgroundColor = "#DEB887";
                $('o15_tipo').value = oRetorno.tiporecurso;
                $('o15_datalimite').value = js_formatar(oRetorno.datalimiterecurso, 'd');
            } else {

                $('iTipo_select_descr').value = (oRetorno.tipo == 1 ? 'Sintético' : 'Analítico');
                $('o15_tipo_select_descr').value = (oRetorno.tiporecurso == 1 ? 'Recurso Livre' : 'Recurso Vinculado');
                $('o15_datalimite').value = js_formatar(oRetorno.datalimiterecurso, 'd');

                $('identificadorUso').disabled = true;
                $('tipoDetalhamento').disabled = true;
                $('grupoFonteRecurso').disabled = true;
                $('especificacaoFonte').disabled = true;

                $('identificadorUso').style.backgroundColor = "#DEB887";
                $('tipoDetalhamento').style.backgroundColor = "#DEB887";
                $('grupoFonteRecurso').style.backgroundColor = "#DEB887";
                $('especificacaoFonte').style.backgroundColor = "#DEB887";

                $('identificadorUso').style.color = "#000000";
                $('tipoDetalhamento').style.color = "#000000";
                $('grupoFonteRecurso').style.color = "#000000";
                $('especificacaoFonte').style.color = "#000000";
            }
            $('db_opcao').disabled = false;

            js_preencherCodigoRecurso();
            return true;
        }
    }

    function js_buscaDadosMascara() {
        js_divCarregando('Aguarde buscando mascara...', 'msgBoxGetDadosMascara');
        var oParam = new Object();
        oParam.exec = "getDadosMascara";

        var oAjax = new Ajax.Request(
            sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + js_objectToJson(oParam),
                onComplete: js_retornoDadosMascara
            }
        );
    }


    function js_retornoDadosMascara(oAjax) {
        js_removeObj("msgBoxGetDadosMascara");
        var oRetorno = JSON.parse(oAjax.responseText);

        $('boxMascara').hide();
        $('boxTipo').hide();
        //$('o15_codtri').focus();
        if (oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode());
            $('sMascara').value = "";
            $('iTipo').value = '1';
            return false;
        } else {
            codigoEstrutural = oRetorno.codigo;
            $('sMascara').value = oRetorno.mascara.urlDecode();
            $('o15_codtri').value = oRetorno.mascara.urlDecode();
            $('o15_codtri').maxLength = oRetorno.mascara.urlDecode().length;
            new MaskedInput("#o15_codtri", oRetorno.mascara.urlDecode().replace(/0/g, "*"), {placeholder: "0"});

            if (oRetorno.niveis > 1) {
                $('boxMascara').show();
                $('boxTipo').show();
                $('iTipo').value = '2';
            }
            return true;
        }
    }

    function js_salvar() {
        if (empty($F('o15_codtri')) || $F('o15_codtri') == '0000') {
            $('o15_codtri').focus();
            alert("Você precisa especificar um código para o campo Código Tribunal. Informe o código neste campo e tente novamente.");
            return false;
        }

        if (empty($F('o15_finali'))) {
            alert("Informe a finalidade do recurso!");
            return false;
        }

        if ($F('especificacaoFonte') == '') {
            alert("Você precisa selecionar uma especifiçacão.");
            return false;
        }

        if ($F('complementoRecurso') == '') {
            alert("Informe o complemento do recurso!");
            return false;
        }

        let data = $F('o15_datalimite');
        if (!empty(data)) {
            data = js_formatar(data, 'd');
        }

        const formData = new FormData();
        formData.append("codigo", $F('o15_codigo'));
        formData.append("descricao", $F('o15_descr'));
        formData.append("codigoTribunal", $F('o15_codtri'));
        formData.append("finalidade", $F('o15_finali'));
        formData.append("dataLimite", data);
        formData.append("codigoSiconf", $F('o15_codigosiconfi'));
        formData.append("loaIdentificacao", getValue($F('identificadorUso')));
        formData.append("loaTipo", getValue($F('tipoDetalhamento')));
        formData.append("loaGrupo", getValue($F('grupoFonteRecurso')));
        formData.append("loaEspecificacao", getValue($F('especificacaoFonte')));
        formData.append("complemento", $F('complementoRecurso'));
        formData.append("tipoRecurso", $F('o15_tipo'));
        formData.append("codigoRecurso", null);
        formData.append("codigoEstrutural", codigoEstrutural);

        HttpClient.post(PHPSession.requestApi + rota + 'salvar', {body: formData}).then(response => {

            alert(response.message);
            if (response.error) {
                return;
            }

            formulario.reset();
            if ($('db_opcao').value == 'Alterar') {
                js_pesquisa();
            }
        });
    }

    function js_remover() {
        const formData = new FormData();
        formData.append("codigo", $F('o15_codigo'));

        HttpClient.post(PHPSession.requestApi + rota + 'excluir', {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }

            formulario.reset();
            $('db_opcao').disabled = true;
            js_pesquisa();
        });
    }

    $('db_opcao').observe("click", function () {
        if ($('db_opcao').value == 'Excluir') {
            js_remover();
        } else {
            js_salvar();
        }
    });

    js_buscaDadosMascara();

    $('especificacaoFonte').addEventListener('change', (event) => {

        let elemento = event.target;
        let str = elemento.options[elemento.selectedIndex].innerHTML
        $('o15_descr').value = str.substr(str.indexOf('-') + 1).trim();
    });

    const getValue = (valor) => {
        return valor === null ? '' : valor
    };

</script>
