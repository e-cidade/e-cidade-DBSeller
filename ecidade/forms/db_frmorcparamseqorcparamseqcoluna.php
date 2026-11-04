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

require_once modification('dbforms/db_classesgenericas.php');

$clorcparamseqorcparamseqcoluna->rotulo->label();
$clrotulo = new rotulocampo();
$clrotulo->label('o69_codparamrel');
$clrotulo->label('o115_descricao');

?>
<style>
    #fieldset-colunas-vinculadas {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }

    @media (min-width: 576px) {
        #fieldset-colunas-vinculadas {
            max-width: 540px;
        }
    }

    @media (min-width: 768px) {
        #fieldset-colunas-vinculadas {
            max-width: 720px;
        }
    }

    @media (min-width: 992px) {
        #fieldset-colunas-vinculadas {
            max-width: 960px;
        }
    }

    @media (min-width: 1200px) {
        #fieldset-colunas-vinculadas {
            max-width: 1140px;
        }
    }
</style>
<div class="container">
    <form name="form1" id="frmColunas" method="post">
        <fieldset>
            <legend>Colunas da Linha</legend>
            <table class="form-container">
                <tr>
                    <?php
                    db_input('o116_sequencial', 10, $Io116_sequencial, true, 'hidden', 3, "");
                    db_input('o69_origem', 10, $Io116_sequencial, true, 'hidden', 3, "");
                    ?>
                </tr>
                <tr>
                    <td nowrap title="<?= @$To116_codseq ?>">
                        <?php
                        db_ancora('Linha: ', "js_pesquisao116_codseq(true);", 3);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'o116_codseq',
                            10,
                            $Io116_codseq,
                            true,
                            'text',
                            3,
                            " onchange='js_pesquisao116_codseq(false);'"
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$To116_codparamrel ?>">
                        <?php
                        db_ancora('Relatório:', "js_pesquisao116_codparamrel(true);", 3);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'o116_codparamrel',
                            10,
                            $Io116_codparamrel,
                            true,
                            'text',
                            3,
                            " onchange='js_pesquisao116_codparamrel(false);'"
                        );
                        db_input('o42_descrrel', 50, $Io69_codparamrel, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset class="separator">
                            <legend>Lançador de Colunas</legend>
                            <table class="form-container">
                                <tr>
                                    <td>
                                        <span id="labelColuna" style="display: none">Coluna:</span>
                                        <a id="ancoraColuna" href="#">Coluna:</a>
                                    </td>
                                    <td>
                                        <input type="text" id="codigoColuna" name="codigoColuna" lang="o115_sequencial"
                                               class="field-size2"/>
                                        <input type="text" id="descricaoColuna" name="descricaoColuna"
                                               lang="o115_descricao" style="width: 455px;" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ordem:</td>
                                    <td>
                                        <input name="ordem" id="ordem" class="field-size2" maxlength="2">
                                        <input type="button" name="btnAdicionar" id="btnAdicionar" value="Adicionar">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <br>
                                        <div id="cntColunasLancadas"></div>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
                <tr id="linhaFormula" style="display: none">
                    <td>
                        <label for="o116_formula">Fórmula de Cálculo:</label>
                    </td>
                    <td>
                        <?php
                        db_textarea('o116_formula', 1, 50, $Io116_formula, true, 'text', 1, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="cbxPeriodos">Selecionar Períodos:</label>
                    </td>
                    <td>
                        <select id="cbxPeriodos" style="width: 75px">
                            <option value="f">Não</option>
                            <option value="t">Sim</option>
                        </select>
                    </td>
                </tr>
                <tr id="linhaPeriodo" style="display: none">
                    <td>
                        <label for="o116_periodo">
                            <strong>Períodos:</strong>
                        </label>
                    </td>
                    <td>
                        <select name="o116_periodo[]" id="o116_periodo" multiple>
                            <?php
                            $aPeriodos = $oRelatorio->getPeriodos();
                            $aListaPeriodos = array();
                            $aListaPeriodos[0] = "Selecione";
                            foreach ($aPeriodos as $oPeriodo) {
                                $sSelected = '';
                                if ($oPeriodo->o114_sequencial == @$o116_periodo) {
                                    $sSelected = "selected";
                                }
                                echo "<option value='{$oPeriodo->o114_sequencial}' {$sSelected}>{$oPeriodo->o114_descricao}</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="btnSalvar" type="button" id="btnSalvar" value="Salvar"/>
        <input name="btnCancelar" type="button" id="btnCancelar" value="Cancelar" onclick="recarregar();" disabled>
    </form>
</div>
<fieldset id="fieldset-colunas-vinculadas">
    <legend>Colunas Vinculadas</legend>
    <div id="cntColunasVinculadas"></div>
</fieldset>
<script src="scripts/widgets/Input/DBInput.widget.js"></script>
<script src="scripts/widgets/Input/DBInputInteger.widget.js"></script>
<script>
    new DBInputInteger($('ordem'));
    const urlParams = new URLSearchParams(window.location.search);
    const abaLinhas = parent[0].document;
    const codigoLinha = abaLinhas.querySelector('input[name="o69_codseq"]').value;
    const codigoRelatorio = abaLinhas.querySelector('input[name="o69_codparamrel"]').value;
    const descricaoRelatorio = abaLinhas.querySelector('input[name="o42_descrrel"]').value;

    const inputCodigoRelatorio = document.querySelector('#o116_codparamrel');
    const inputDescricaoRelatorio = document.querySelector('#o42_descrrel');
    const inputCodigoLinha = document.querySelector('#o116_codseq');
    const inputFormula = document.querySelector('#o116_formula');

    inputCodigoRelatorio.value = codigoRelatorio;
    inputDescricaoRelatorio.value = descricaoRelatorio;
    inputCodigoLinha.value = codigoLinha;

    var origem = urlParams.has('o69_origem') ? urlParams.get('o69_origem') : null;
    var ordensColunasLancadas = [];
    var ordensColunasVinculadas = [];

    $('o116_formula').style.minHeight = '1px';

    if (origem != 5) {
        $('linhaFormula').style.display = '';
    }

    const dbLookUp = new DBLookUp($('ancoraColuna'), $('codigoColuna'), $('descricaoColuna'), {
        'sArquivo': 'func_orcparamseqcoluna.php',
        'sObjetoLookUp': 'db_iframe_orcparamseqcoluna',
        'sLabel': 'Pesquisar colunas'
    });
    dbLookUp.setCallBack('onChange', error => {
        if (!error) {
            sugerirOrdemColuna();
        }
    });
    dbLookUp.setCallBack('onClick', () => {
        sugerirOrdemColuna();
    });

    // Primeira grid - Lançador de colunas
    var colunasLancadasCollection = new Collection().setId('codigoColuna');
    var gridColunasLancadas = DatagridCollection.create(colunasLancadasCollection).
        configure({'height': '120px;', 'order': false, 'delete': true});
    gridColunasLancadas.addColumn('codigoColuna', {label: 'Código', 'width': '15%'}).setOption('align', 'center');
    gridColunasLancadas.addColumn('ordem', {label: 'Ordem', 'width': '15%'}).setOption('align', 'center');
    gridColunasLancadas.addColumn('descricao', {label: 'Descrição', 'width': '60%'}).setOption('align', 'left');
    gridColunasLancadas.show($('cntColunasLancadas'));

    gridColunasLancadas.addAction('E', null, function(event, element) {

        var index = ordensColunasLancadas.indexOf(parseInt(element.ordem));
        if (index > -1) {
            ordensColunasLancadas.splice(index, 1);
        }

        gridColunasLancadas.collection.remove(element.codigoColuna);
        gridColunasLancadas.reload();
        sugerirOrdemColuna();
    });

    // Segunda grid - Colunas vinculadas
    var colunasVinculadasCollection = new Collection().setId('codigoColuna');
    var gridColunasVinculadas = DatagridCollection.create(colunasVinculadasCollection).configure({
        height: '120px;',
        order: false,
        delete: true,
        update: true
    });
    gridColunasVinculadas.addColumn('codigoColuna', {label: 'Código', 'width': '10%'}).setOption('align', 'center');
    gridColunasVinculadas.addColumn('ordem', {label: 'Ordem', 'width': '10%'}).setOption('align', 'center');
    gridColunasVinculadas.addColumn('descricao', {label: 'Descrição', 'width': '30%'}).setOption('align', 'left');
    gridColunasVinculadas.addColumn('campo', {label: 'Campo', 'width': '10%'}).setOption('align', 'left');
    gridColunasVinculadas.addColumn('periodosDescricao', {label: 'Períodos', 'width': '30%'}).
        setOption('align', 'left');
    gridColunasVinculadas.show($('cntColunasVinculadas'));

    gridColunasVinculadas.addAction('A', null, function(event, element) {
        limparTelaFormulario();

        $('codigoColuna').value = element.codigoColuna;
        $('descricaoColuna').value = element.descricao;
        $('ordem').value = element.ordem;
        $('btnAdicionar').value = 'Alterar';
        $('labelColuna').style.display = '';
        $('ancoraColuna').style.display = 'none';
        $('codigoColuna').setAttribute('disabled', 'disabled');

        inputFormula.value = element.formula;

        colunasLancadasCollection.add({
            'codigoColuna': element.codigoColuna,
            'ordem': element.ordem,
            'descricao': element.descricao
        });

        var totalPeriodos = 0;
        Object.keys(element.periodos).forEach(codigoPeriodo => {
            var option = document.querySelector('#o116_periodo option[value=\'' + codigoPeriodo + '\']');
            option.selected = true;
            totalPeriodos++;
        });

        if (totalPeriodos != $('o116_periodo').length) {
            $('cbxPeriodos').value = 't';
        }
        $('cbxPeriodos').dispatchEvent(new Event('change'));

        gridColunasLancadas.reload();

        $(`action_e_${element.codigoColuna}`).setAttribute('disabled', 'disabled');
        $('btnCancelar').removeAttribute('disabled');
    });

    gridColunasVinculadas.addAction('E', null, function(event, element) {
        if (confirm('Deseja excluir a coluna ' + element.descricao + '?')) {

            const formData = new FormData();
            formData.append('acao', 'excluirLinhaColunas');
            formData.append('relatorio', $F('o116_codparamrel'));
            formData.append('linha', $F('o116_codseq'));
            formData.append('coluna', element.codigoColuna);

            HttpClient.post('con1_relatorio_legal.RPC.php', {
                body: formData
            }).then(resposta => {

                alert(resposta.mensagem);
                if (resposta.erro) {
                    return;
                }

                recarregar();
            });
        }
    });

    //Adiciona colunas na grid Lançador de colunas
    $('btnAdicionar').onclick = function() {

        if (empty($F('codigoColuna'))) {
            alert('Campo coluna não preenchido.');
            return false;
        }

        if (empty($F('ordem'))) {
            alert('Campo ordem não preenchido.');
            return false;
        }

        if (ordensColunasLancadas.indexOf(parseInt($F('ordem'))) > -1 ||
            ordensColunasVinculadas.indexOf(parseInt($F('ordem'))) > -1) {
            alert('Ordem informada já é utilizada por outra coluna.');
            return false;
        }

        ordensColunasLancadas.push(parseInt($F('ordem')));

        colunasLancadasCollection.add({
            'codigoColuna': $F('codigoColuna'),
            'ordem': $F('ordem'),
            'descricao': $F('descricaoColuna')
        });
        gridColunasLancadas.reload();

        $('ordem').value = '';

        if ($('btnAdicionar').value == 'Adicionar') {
            $('codigoColuna').value = '';
            $('descricaoColuna').value = '';
        }
    };

    // Valida se apresenta ou não os períodos para seleção
    $('cbxPeriodos').onchange = function() {
        $('linhaPeriodo').style.display = 'none';
        if ($F('cbxPeriodos') == 't') {
            $('linhaPeriodo').style.display = '';
        }
    };

    function montarPeriodos() {
        var periodos = [];

        if ($F('cbxPeriodos') == 'f') {
            for (var option of $('o116_periodo').options) {
                periodos.push(option.value);
            }
            return periodos;
        }

        return $F('o116_periodo');
    }

    function montarColunas() {
        var colunas = [];
        var colunasLancadas = gridColunasLancadas.getCollection();

        for (colunaLancada of colunasLancadas.itens) {
            var coluna = {
                'codigo': colunaLancada.codigoColuna,
                'ordem': colunaLancada.ordem,
                'descricao': colunaLancada.descricao
            };
            colunas.push(coluna);
        }

        return colunas;
    }

    $('btnSalvar').onclick = function() {

        var colunasLancadas = gridColunasLancadas.getCollection();
        if (colunasLancadas.itens.length == 0) {
            alert('Ao menos uma coluna deve ser adicionada.');
            return false;
        }

        if ($F('cbxPeriodos') == 't' && $F('o116_periodo').length == 0) {
            alert('Ao menos um período deve ser selecionado.');
            return false;
        }

        var mensagemAviso = "Atenção - Caso haja Valor Manual lançado para a linha/coluna, será excluída.\n\nDeseja continuar?";
        if (!confirm(mensagemAviso)) {
            return false;
        }

        const formData = new FormData();
        var colunas = montarColunas();

        formData.append('acao', 'salvarLinhaColuna');
        formData.append('relatorio', $F('o116_codparamrel'));
        formData.append('linha', $F('o116_codseq'));
        formData.append('colunas', JSON.stringify(colunas));
        formData.append('periodos', JSON.stringify(montarPeriodos()));
        formData.append('formula', $F('o116_formula'));

        HttpClient.post('con1_relatorio_legal.RPC.php', {
            body: formData
        }).then(resposta => {
            alert(resposta.mensagem);

            if (!resposta.erro) {
                recarregar();
            }
        });
    };

    function buscarColunasVinculadas() {
        const formData = new FormData();
        formData.append('acao', 'buscarLinhaColunas');
        formData.append('relatorio', $F('o116_codparamrel'));
        formData.append('linha', $F('o116_codseq'));

        HttpClient.post('con1_relatorio_legal.RPC.php', {
            body: formData
        }).then(resposta => {
            if (resposta.erro) {
                return alert(resposta);
            }

            ordensColunasVinculadas = [];

            Object.keys(resposta.linhaColunas).forEach(index => {
                ordensColunasVinculadas.push(resposta.linhaColunas[index].ordem);
                colunasVinculadasCollection.add(resposta.linhaColunas[index]);
            });

            colunasVinculadasCollection.sort('asc', ['ordem'], null);
            gridColunasVinculadas.reload();
        });
    }

    function limparTelaFormulario() {
        gridColunasLancadas.clear();
        $('codigoColuna').value = '';
        $('ordem').value = '';
        $('cbxPeriodos').value = 'f';
        for (var option of $('o116_periodo').options) {
            option.selected = false;
        }
    }

    function recarregar() {
        const iframe = (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_orcparamseqorcparamseqcoluna;
        iframe.location.reload();
    }

    function sugerirOrdemColuna() {
        const colunas = ordensColunasVinculadas.concat(ordensColunasLancadas);
        const ordem = $('ordem');

        if (colunas.length === 0) {
            ordem.value = 1;
            return;
        }

        const max = colunas.reduce(function(a, b) {
            return Math.max(a, b);
        });

        if (max === 99) {
            ordem.value = '';
            return;
        }

        ordem.value = max + 1;
    }

    buscarColunasVinculadas();
    var oContextComplete = new DBContextComplete('teste');
    oContextComplete.setElementForContext($('o116_formula'));

    oContextComplete.setPrependString('#');
    oContextComplete.init();
    oParam = {exec:'getVariaveis', iOrigemDados: $F('o69_origem'),
        iCodigoRelatorio:$F('o116_codparamrel'),
        iCodigoLinha:$F('o116_codseq') ,
    };
    var oAjax = new Ajax.Request('con4_relatorioslegais.RPC.php',
        {
            method:'post',
            parameters:'json='+Object.toJSON(oParam),
            asynchronous:false,
            onComplete: function(oResponse) {

                var oRetorno = JSON.parse(oResponse.responseText);

                oContextComplete.addGroup('colunas', '@');
                oRetorno.oListaVariaveis.campos_relatorios.each(function(sVariavel) {
                    oContextComplete.addOption(sVariavel, sVariavel);
                });

                oRetorno.oListaVariaveis.colunas_linha.each(function(sVariavel) {
                    oContextComplete.addOption(sVariavel, sVariavel,'colunas');
                });
            }
        }
    );
</script>
