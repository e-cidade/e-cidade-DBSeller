<?php
/**
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

//MODULO: empenho

$uf = getEstadoInstituicao();
$isPB = $uf === "PB" ;

$clempautoriza->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("e44_tipo");
$clrotulo->label("z01_nome");
$clrotulo->label("nome");
$clrotulo->label("pc50_descr");
$clrotulo->label("e57_codhist");
$clrotulo->label("c58_descr");
$clrotulo->label("e150_numeroprocesso");
if ($db_opcao == 1) {
    $ac = "emp1_empautoriza004.php";
} elseif ($db_opcao == 2 || $db_opcao == 22) {
    $ac = "emp1_empautoriza005.php";
} elseif ($db_opcao == 3 || $db_opcao == 33) {
    $ac = "emp1_empautoriza006.php";
}
?>

<style>

    #e57_codhistdescr,
    #e54_codtipodescr,
    #e54_codcomdescr {
        width: 400px;
    }

    #e57_codhist,
    #e54_codtipo,
    #e54_tipol,
    #e54_codcom {
        width: 50px;
    }

    #e44_tipo {
        width: 453px;
    }

    #e54_tipoldescr {
        width: 200px;
    }
</style>

<form name="form1" id="formEmpAutoriza" method="post" action="<?= $ac ?>">
    <fieldset>
        <legend><strong>Autorização de Empenho </strong></legend>
        <table border="0">
            <tr>
                <td nowrap title="<?= @$Te54_autori ?>">
                    <?= @$Le54_autori ?>
                </td>
                <td>
                    <?php
                    db_input('e54_autori', 10, $Ie54_autori, true, 'text', 3);
                    db_input('o58_codele', 10, $Ie54_autori, true, 'hidden', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Te54_numcgm ?>">
                    <?php
                    db_ancora(@$Lz01_nome, "js_pesquisae54_numcgm(true);", isset($emprocesso) && $emprocesso == true ? "3" : $db_opcao, "", "ancora_e54_numcgm");
                    ?>
                </td>
                <td nowrap="nowrap">
                    <?php
                    db_input('e54_numcgm', 10, $Ie54_numcgm, true, 'text', isset($emprocesso) && $emprocesso == true ? "3" : $db_opcao, " onchange='js_pesquisae54_numcgm(false);'");
                    db_input('z01_nome', 48, $Iz01_nome, true, 'text', 3, '');
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Te54_codcom ?>">
                    <strong>Tipo de Compra:</strong>
                </td>
                <td>
                    <?php

                    if (isset($e54_codcom) && $e54_codcom == '') {
                        $pc50_descr = '';
                    }
                    if (empty($e54_codcom)) {
                        $somadata = $clpcparam->sql_record($clpcparam->sql_query_file(db_getsession("DB_instit"), "pc30_tipcom as e54_codcom"));
                        if ($clpcparam->numrows > 0) {
                            db_fieldsmemory($somadata, 0);
                        } else {
                            $e54_codcom = 5;
                        }
                    }

                    /*
                     * alterado para liberar o campo tipo de compra para alteracao
                     */
                    $campos = "pc50_codcom as e54_codcom, pc50_descr, l44_obrigalicitacao";
                    $sql = $clpctipocompra->sql_query(null, $campos, "pc50_descr", "pc50_ativo is true");
                    $result = $clpctipocompra->sql_record($sql);
                    $tiposCompra = db_utils::getCollectionByRecord($result);
                    db_selectrecord("e54_codcom", $result, true, isset($emprocesso) && $emprocesso == true ? "1" : $db_opcao, "", "", "", "", "js_reload(this.value)");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Te54_tipol ?>">
                    <strong>Tipo de Licitação:</strong>
                </td>
                <td id="conteudoDeLicitacao">
                    <?php
                    if (isset($tipocompra) || isset($e54_codcom)) {
                        if (isset($e54_codcom) && empty($tipocompra)) {
                            $tipocompra = $e54_codcom;
                        }

                        $liberaLicictacao = !empty(array_filter($tiposCompra, function ($dado) use ($tipocompra) {
                            return ($tipocompra == $dado->e54_codcom && $dado->l44_obrigalicitacao == 't');
                        }));

                        $result = $clcflicita->sql_record($clcflicita->sql_query_file(null, "l03_tipo,l03_descr", '', "l03_codcom=$tipocompra and l03_instit = " . db_getsession('DB_instit') . " and l03_modalidadeativa = 't'"));
                        if ($clcflicita->numrows > 0) {
                            /*
                             * alterado para liberar o campo tipo licitacao para alteracao
                             */
                            db_selectrecord("e54_tipol", $result, true, isset($emprocesso) && $emprocesso == true ? "1" : "1", "", "", "");
                            $dop = $db_opcao;
                        } else {

                            $e54_tipol = '';
                            $dop = '3';
                            db_input('e54_tipol', 8, $Ie54_tipol, true, 'text', 3);
                        }
                    } else {
                        $dop = '3';
                        $e54_tipol = '';
                        db_input('e54_tipol', 8, $Ie54_tipol, true, 'text', 3);
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <?php if(isParaiba()) { ?>
                <td><a href="#" id="ancoraLicitacao">Licitação: </a></td>
                <?php }else { ?>
                <td><strong>Número da Licitação:</strong></td>
                <?php } ?>
                <td><?php
                    $bloqueia = '';
                    if (!$liberaLicictacao || isParaiba()) {
                        $bloqueia = "class='readonly' readonly";
                    }
                    ?>

                    <input type="text" id="numeroLicitacao" name="numeroLicitacao" lang="l20_numero" <?php echo $bloqueia ?> style="width: 150px"
                           oninput="js_ValidaCampos(this, 1, 'Número da Licitação', 'f', 'f', event)" maxlength="20">
                    &nbsp;&nbsp;/&nbsp;&nbsp;
                    <input type="text" id="anoLicitacao" name="anoLicitacao" lang="l20_anousu" <?php echo $bloqueia ?> style="width: 50px"
                           oninput="js_ValidaCampos(this, 1, 'Ano da Licitação', 'f', 'f', event)" maxlength="4">
                </td>
            </tr>

            <tr>
                <td nowrap title="<?= @$Te54_codtipo ?>">
                    <strong>Tipo de Empenho:</strong>
                </td>
                <td>
                    <?php

                    /*
                     * alterado para liberar o campo tipo de empenho para alteracao
                     */
                    $result = $clemptipo->sql_record($clemptipo->sql_query_file(null, "e41_codtipo,e41_descr"));
                    db_selectrecord("e54_codtipo", $result, true, isset($emprocesso) && $emprocesso == true ? "1" : $db_opcao);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Te57_codhist ?>">
                    <?= $Le57_codhist ?>
                </td>
                <td>
                    <?php
                    // caso empparametro.e30_autimportahist=='t' busca o historico da ultima autorização
                    $par = $clempparametro->sql_record($clempparametro->sql_query_file(db_getsession("DB_anousu")));
                    if ($clempparametro->numrows > 0 && $db_opcao == 1) {

                        db_fieldsmemory($par, 0);
                        if ($e30_autimportahist == 't') {

                            $hist = $clempauthist->sql_record("select e57_codhist
    	                                             from empauthist
    					                               inner join empautoriza on e54_autori=e57_autori
    					                                    where e54_login=" . db_getsession("DB_id_usuario") . "
    					                                 order by e57_autori desc limit 1");
                            if ($clempauthist->numrows > 0) {
                                db_fieldsmemory($hist, 0);
                            }
                        }
                    }
                    $sql = $clemphist->sql_query_file(null, "e40_codhist, e40_descr, 2 as ordem");

                    if ($isPB) {
                        $sql = "
                            select null as e40_codhist,
                                  'Selecione...' as e40_descr,
                                  1 as ordem
                               union all
                               $sql
                        ";
                    }
                     $sql .= " order by 1";
                     $result = $clemphist->sql_record($sql);
                    db_selectrecord("e57_codhist", $result, true, isset($emprocesso) && $emprocesso == true ? "3" : "1", "", "", "");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Te44_tipo ?>">
                    <?= $Le44_tipo ?>
                </td>
                <td>
                    <?php
                    $result = $clempprestatip->sql_record($clempprestatip->sql_query_file(null, "e44_tipo as tipo,e44_descr,e44_obriga", "e44_obriga "));
                    $numrows = $clempprestatip->numrows;
                    $arr = array();

                    for ($i = 0; $i < $numrows; $i++) {

                        db_fieldsmemory($result, $i);
                        if ($e44_obriga == 0 && empty($e44_tipo)) {
                            $e44_tipo = $tipo;
                        }
                        $arr[$tipo] = $e44_descr;
                    }
                    db_select("e44_tipo", $arr, true, 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Te54_destin ?>">
                    <?= @$Le54_destin ?>
                </td>
                <td>
                    <?php
                    /*
                     * alterado para liberar o campo destino para alteracao
                     */
                    db_input('e54_destin', 61, $Ie54_destin, true, 'text', isset($emprocesso) && $emprocesso == true ? "1" : $db_opcao, "")
                    ?>
                </td>
            </tr>

            <tr title="Número do processo administrativo (PA). Máximo 15 caractéres.">
                <td nowrap="nowrap">
                    <strong>Processo Administrativo (PA):</strong>
                </td>
                <td colspan="3">
                    <?php
                    db_input('e150_numeroprocesso', 61, $Ie150_numeroprocesso, true, 'text', $db_opcao);
                    ?>
                </td>
            </tr>
            <?php
            $anousu = db_getsession("DB_anousu");
            if ($anousu > 2007) {

                if (empty($e54_concarpeculiar)) {
                    $e54_concarpeculiar = "000";
                    $c58_descr = "NÃO SE APLICA";
                    if ($isPB) {
                        $e54_concarpeculiar = "0";
                    }
                }
                ?>
                <tr id="trConcarpeculiar">
                    <td nowrap title="<?= @$Te54_concarpeculiar ?>" id = 'caracPeculiarF'>
                        <?php
                        db_ancora(@$Le54_concarpeculiar, "js_pesquisae54_concarpeculiar(true);", isset($emprocesso) && $emprocesso == true ? "3" : $db_opcao);
                        ?>
                    </td>
                    <td nowrap="nowrap" id = 'caracPeculiarI'>
                        <?php

                        db_input("e54_concarpeculiar", 10, $Ie54_concarpeculiar, true, "text", isset($emprocesso) && $emprocesso == true ? "3" : $db_opcao, "onChange='js_pesquisae54_concarpeculiar(false);'");
                        db_input("c58_descr", 47, 0, true, "text", 3);
                        ?>
                    </td>
                </tr>
                <?php
            } else {
                $e54_concarpeculiar = "0";
                db_input("e54_concarpeculiar", 10, 0, true, "hidden", 3, "");
            }
            ?>
            <tr>
                <td nowrap title="<?= @$Te54_resumo ?>" colspan="2">
                    <fieldset>
                        <legend>
                            <strong><?= @$Le54_resumo ?></strong>
                        </legend>
                        <?php db_textarea('e54_resumo', 3, 84, $Ie54_resumo, true, 'text', $db_opcao, "") ?>
                    </fieldset>
                </td>
            </tr>
        </table>
    </fieldset>

    <div style="margin-top: 10px;">
        <?php
        $name = $db_opcao == 1 || $db_opcao == 2 ? 'salvar' : 'excluir';
        $value = ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir"));
        $disabled = $db_botao == false ? 'disabled' : '';
        ?>
        <input id="btnSalvar"
               name="<?= $name; ?>"
               type="button" id="db_opcao"
               value="<?= $value; ?>"
            <?= $disabled ?>
               onclick="<?php ($db_opcao == 1) ? 'return js_salvaCache();' : ''; ?>"/>

        <?php if ($db_opcao != 1) { ?>
            <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
        <?php } ?>

        <?php if ($db_opcao == 2) { ?>
            <input name="novo" type="button" id="novo" value="Nova autorização" onclick="js_nova();">

            <?php
            $permissao_lancar = db_permissaomenu(db_getsession("DB_anousu"), 398, 3489);
            if ($permissao_lancar == "true") {
                ?>
                <input name="lancemp" type="button" id="lancemp" value="Lançar Empenho" onclick="js_lanc_empenho();">
                <?php
            }
        }

        if ($db_opcao == 1) { ?>
            <input name="importar" type="button" id="importar" value="Importar autorização" onclick="js_importar()">
        <?php } ?>

    </div>

    <?php if (isset($emprocesso) && $emprocesso == true) { ?>
        <br><font color="red"><b>Autorização gerada por solicitação de compras.</b></font>
    <?php } ?>


</form>

<script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
<script>

    var db_opcao = <?php echo $db_opcao; ?>;
    var isPB = '<?php echo $isPB ?>';
    // Inputs
    const campoAutorizacao = document.querySelector("#e54_autori");
    let instituicaoLicitacao = null;
    if (isPB) {
        const licitacaoLookup = new DBLookUp(
            document.getElementById('ancoraLicitacao'),
            document.getElementById('numeroLicitacao'),
            document.getElementById('anoLicitacao'), {
                'sArquivo': 'func_liclicita_gera_autorizacao.php',
                'sLabel': 'Pesquisar itens',
                'sObjetoLookUp': "db_iframe_liclicita_gera_autorizacao",
                'aCamposAdicionais': ['l20_instit']
            }
        );

        licitacaoLookup.setCallBack('onClick',(resposta) => {
            instituicaoLicitacao = resposta[2];
        });

        let liberaLicictacao = '<?php echo $liberaLicictacao ?>';
        if(!liberaLicictacao) {
            licitacaoLookup.desabilitar();
        }
    }

    // Buttons
    const btnSalvar = document.querySelector("#btnSalvar");
    const btnImportar = document.querySelector("#importar");

    // Form
    const formEmpAutoriza = document.querySelector("#formEmpAutoriza");

    if(isPB) {
        hiddenPeculiar();
    }

    btnSalvar.addEventListener('click', () => {

        let numeroLicitacao = document.getElementById('numeroLicitacao');
        let anoLicitacao = document.getElementById('anoLicitacao');

        if (isPB && document.getElementById('e57_codhist').value == "") {
            alert('Deve ser selecionado o Tipo de Meta da licitação.');
            document.getElementById('e57_codhist').focus();
            return;
        }

        if ((!numeroLicitacao.hasAttribute('readonly') && numeroLicitacao.value == '') ||
            (!anoLicitacao.hasAttribute('readonly') && anoLicitacao.value == '')) {
            alert('Você deve informar o número e ano da licitação.');
            return;
        }

        if (!anoLicitacao.hasAttribute('readonly') && anoLicitacao.value.length < 4) {
            alert('O ano deve possuir 4 digitos.');
            return;
        }

        salvar(new FormData(formEmpAutoriza));
    });

    function hiddenPeculiar (){

        let inputCaracPI = document.getElementById('caracPeculiarI');
        let filedCaracPF = document.getElementById('caracPeculiarF');
        inputCaracPI.hide();
        filedCaracPF.hide();

    }

    function setTipoLicitacao(e54_codcom, e54_tipol) {
        const formImportar = new FormData();

        formImportar.append('e54_codcom', e54_codcom);
        formImportar.append('acao', 'buscaLicitacoesPorTipoCodigo');

        HttpClient.post('emp1_empautoriza.RPC.php', {body: formImportar}).then(response => {
            if (response.erro) {
                return alert(response.mensagem);
            }
            setCamposTipoLicitacao(response, e54_tipol);
        });
    }

    /**
     * @todo revisar isso tudo
     */
    function setCamposTipoLicitacao(response, codigoTipoLicitacao) {
        const tdConteudoDeLicitacao = document.querySelector('#conteudoDeLicitacao');
        const codigoTipoLicitacaoSelect = document.querySelector('#e54_tipol');
        const codigoTipoLicitacaoInput = document.querySelector('#tipoLicitacaoInput');
        const descricaoTipoLicitacao = document.querySelector('#e54_tipoldescr');
        const numeroLicitacao = document.querySelector('#numeroLicitacao');
        const anoLicitacao = document.querySelector('#anoLicitacao');

        if (typeof (response.camposLicitacao) == 'undefined' || response.camposLicitacao == false) {
            codigoTipoLicitacaoSelect.style.display = 'none';

            if (!document.querySelector('#tipoLicitacaoInput')) {
                const codigoTipoLicitacaoInput = document.createElement('input');
                codigoTipoLicitacaoInput.setAttribute('readonly', '');
                codigoTipoLicitacaoInput.setAttribute('id', 'tipoLicitacaoInput');
                codigoTipoLicitacaoInput.setAttribute('maxlength', '1');
                codigoTipoLicitacaoInput.setAttribute('style', 'background-color:#DEB887;width:50px');
                tdConteudoDeLicitacao.insertBefore(codigoTipoLicitacaoInput, tdConteudoDeLicitacao.firstChild);
            }

            numeroLicitacao.setAttribute('readonly', '');
            numeroLicitacao.setAttribute('style', 'background-color:#DEB887');
            anoLicitacao.setAttribute('readonly', '');
            anoLicitacao.setAttribute('style', 'background-color:#DEB887');
            descricaoTipoLicitacao.style.display = 'none';
        } else {
            if (codigoTipoLicitacaoInput) {
                codigoTipoLicitacaoInput.setAttribute('style', 'display:none');
                codigoTipoLicitacaoSelect.setAttribute('style', 'display:inline-block');
                descricaoTipoLicitacao.setAttribute('style', 'display:inline-block');
                numeroLicitacao.removeAttribute('readonly');
                numeroLicitacao.setAttribute('style', 'background-color:#E6E4F1');
                anoLicitacao.removeAttribute('readonly');
                anoLicitacao.setAttribute('style', 'background-color:#E6E4F1');
            }

            codigoTipoLicitacaoSelect.innerHTML = '';
            descricaoTipoLicitacao.innerHTML = '';

            let tipo;
            let tipoDescricao;
            let selected = '';
            for (let i = 0; i < Object.keys(response.camposLicitacao).length; i++) {
                tipo = response.camposLicitacao[i].l03_tipo;
                tipoDescricao = response.camposLicitacao[i].l03_descr;

                if (codigoTipoLicitacao === tipo) {
                    selected = 'selected';
                } else {
                    selected = '';
                }

                codigoTipoLicitacaoSelect.innerHTML += `<option value="${tipo}" ${selected}>${tipo}</option>`;
                descricaoTipoLicitacao.innerHTML += `<option value="${tipo}" ${selected}>${tipoDescricao}</option>`;
            }
        }
    }

    function setForm(response) {
        document.querySelector('#e54_codcom').value = response.autorizacao.e54_codcom;
        document.querySelector('#e54_codcomdescr').value = response.autorizacao.e54_codcom;

        setTipoLicitacao(response.autorizacao.e54_codcom, response.autorizacao.e54_tipol);

        var codigoHistorico = 0;

        if (response.autorizacao.historico !== null) {
            codigoHistorico = response.autorizacao.historico.e40_codhist;
        }

        document.querySelector('#e57_codhist').value = codigoHistorico;

        document.querySelector('#e57_codhistdescr').value = codigoHistorico;

        var codigoTipoPrestacao = 1;

        if (response.autorizacao.tipoPrestacao !== null) {
            codigoTipoPrestacao = response.autorizacao.tipoPrestacao.e44_tipo
        }

        document.querySelector('#e44_tipo').value = codigoTipoPrestacao;

        document.querySelector('#e150_numeroprocesso').value = response.autorizacao.processoAdministrativo.e150_numeroprocesso || "";

        document.querySelector('#e54_codtipo').value = response.autorizacao.e54_codtipo;
        document.querySelector('#e54_codtipodescr').value = response.autorizacao.e54_codtipo;

        document.querySelector('#e54_concarpeculiar').value = response.autorizacao.e54_concarpeculiar;
        document.querySelector('#e54_concarpeculiar').onchange();

        document.querySelector('#e54_destin').value = response.autorizacao.e54_destin;
        document.querySelector('#e54_resumo').value = response.autorizacao.e54_resumo;
    }

    function validaDadosAutorizacao(fornecedor, isInsersao) {
        var statusFornecedor;

        if (fornecedor == '') {
            alert("É necessário informar o Fornecedor.");
            return '';
        }

        const form = new FormData();
        form.append('fornecedor', fornecedor);
        form.append('isInsersao', isInsersao);
        form.append('acao', 'validaDadosAutorizacao');
        return HttpClient.post('emp1_empautoriza.RPC.php', {body: form});
    }

    function salvar(form) {
        const codigoFornecedor = form.get("e54_numcgm");

        validaDadosAutorizacao(codigoFornecedor, form.get("e54_autori") == "").then(response => {

            if (typeof (response.statusFornecedor) != 'undefined') {
                if (response.statusFornecedor['mensagem'] != '') {
                    alert(response.statusFornecedor['mensagem']);
                }
            }
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            statusFornecedor = response.statusFornecedor['statusFornecedor'];
            if (statusFornecedor != 3 && statusFornecedor != '') {
                form.append('acao', 'salvar');
                form.append('instituicaoLicitacao',instituicaoLicitacao)
                const isAlterar = (document.querySelector('#e54_autori').value) ? true : false;
                var codigoAutorizacao;

                HttpClient.post('emp1_empautoriza.RPC.php', {body: form}).then(responseSalvar => {

                    if (responseSalvar.erro) {
                        return alert(responseSalvar.mensagem);
                    }

                    codigoAutorizacao = responseSalvar.autorizacao.e54_autori;

                    if (!isAlterar) {
                        parent.mo_camada('empautitem');
                        window.location.href = `emp1_empautoriza005.php?chavepesquisa=${codigoAutorizacao}`;
                    } else {
                        alert('Autorização alterada com sucesso!');
                    }
                });
            }
        })
    }

    function js_importar() {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautoriza', 'db_iframe_empautoriza', 'func_empautoriza.php?funcao_js=parent.js_importar02|e54_autori', 'Pesquisa', true, '0', '1');
    }

    function js_importar02(chave) {
        db_iframe_empautoriza.hide();
        if (confirm("Deseja realmente importar a autorização " + chave + "?")) {

            const parametros = new FormData();
            parametros.append('e54_autori', chave);
            parametros.append('acao', 'buscarPorCodigoAutorizacao');

            HttpClient.post('emp1_empautoriza.RPC.php', {body: parametros}).then(response => {

                if (response.erro) {
                    return alert(response.mensagem);
                }

                const codigoFornecedor = response.autorizacao.e54_numcgm.z01_numcgm;

                validaDadosAutorizacao(codigoFornecedor, true).then(responseValida => {

                    if (responseValida.erro) {
                        return alert(responseValida.mensagem);
                    }

                    if (typeof (responseValida.statusFornecedor) == 'undefined') {
                        alert(responseValida.mensagem);
                        return;
                    }
                    const statusFornecedor = responseValida.statusFornecedor['statusFornecedor'];

                    if (statusFornecedor == 3) {
                        if (confirm('Fornecedor com débito.\n\nDeseja reaproveitar os dados da Autorização e alterar o Fornecedor?\n')) {
                            setForm(response);
                            document.querySelector('#ancora_e54_numcgm').click();
                        }
                    } else {
                        const form = new FormData();

                        form.append('e44_tipo', response.autorizacao.tipoPrestacao !== null ? response.autorizacao.tipoPrestacao.e44_tipo : '1');
                        form.append('e54_numcgm', codigoFornecedor);
                        form.append('e54_codcom', response.autorizacao.e54_codcom);
                        form.append('e54_tipol', response.autorizacao.e54_tipol);
                        form.append('e54_codtipo', response.autorizacao.e54_codtipo);
                        form.append('e54_destin', response.autorizacao.e54_destin);
                        form.append('e54_concarpeculiar', response.autorizacao.e54_concarpeculiar);
                        form.append('e54_resumo', response.autorizacao.e54_resumo);
                        form.append('e54_autori_importada', response.autorizacao.e54_autori);
                        form.append('e55_itens', JSON.stringify(response.autorizacao.itens));
                        form.append('e57_codhist', response.autorizacao.historico !== null ? response.autorizacao.historico.e40_codhist : '0');
                        form.append('o58_coddot', response.autorizacao.dotacao !== null ? response.autorizacao.dotacao.o58_coddot : '');
                        form.append('o58_anousu', response.autorizacao.dotacao !== null ? response.autorizacao.dotacao.o58_anousu : '');
                        form.append('e150_numeroprocesso', response.autorizacao.processoAdministrativo !== null ? response.autorizacao.processoAdministrativo.e150_numeroprocesso : '');
                        form.append('e54_valor', response.autorizacao.e54_valor);

                        /**
                         * @todo revisar
                         */

                        form.append('numeroLicitacao', response.autorizacao.numeroLicitacao);
                        form.append('anoLicitacao', response.autorizacao.anoLicitacao);

                        salvar(form);
                    }
                });
            });
        }
    }

    function js_pesquisae54_concarpeculiar(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautoriza', 'db_iframe_concarpeculiar',
                'func_concarpeculiar.php?funcao_js=parent.js_mostraconcarpeculiar1|' +
                'c58_sequencial|c58_descr', 'Pesquisa', true, '0', '1');
        } else {
            if (document.form1.e54_concarpeculiar.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautoriza',
                    'db_iframe_concarpeculiar',
                    'func_concarpeculiar.php?pesquisa_chave=' + document.form1.e54_concarpeculiar.value +
                    '&funcao_js=parent.js_mostraconcarpeculiar', 'Pesquisa', false);
            } else {
                document.form1.c58_descr.value = '';
            }
        }
    }

    function js_mostraconcarpeculiar(chave, erro) {
        document.form1.c58_descr.value = chave;
        if (erro == true) {
            document.form1.e54_concarpeculiar.focus();
            document.form1.e54_concarpeculiar.value = '';
        }
    }

    function js_mostraconcarpeculiar1(chave1, chave2) {
        document.form1.e54_concarpeculiar.value = chave1;
        document.form1.c58_descr.value = chave2;
        db_iframe_concarpeculiar.hide();
    }

    function js_nova() {
        destin = document.form1.e54_destin.value;
        resumo = document.form1.e54_resumo.value;
        numcgm = document.form1.e54_numcgm.value;
        nome = document.form1.z01_nome.value;
        parent.location.href = "emp1_empautoriza001.php?z01_nome=" + nome + "&e54_numcgm=" + numcgm + "&e54_destin=" + destin + "&e54_resumo=" + resumo;
    }

    // lançar empenho
    function js_lanc_empenho() {

        autori = document.form1.e54_autori.value;
        var iElemento = $F("o58_codele");

        parent.location.href = "<?=$sUrlEmpenho?>?iElemento=" + iElemento + "&chavepesquisa=" + autori + "&lanc_emp=true";
    }

    function completaElemento(iElemento) {

        //alert(iElemento);
        $("o58_codele").value = iElemento;
    }

    function js_reload(valor) {
        obj = document.createElement('input');
        obj.setAttribute('name', 'tipocompra');
        obj.setAttribute('type', 'hidden');
        obj.setAttribute('value', valor);
        document.form1.appendChild(obj);
        document.form1.submit();
    }

    function js_pesquisae54_numcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautoriza', 'db_iframe_cgm', 'func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true, '0', '1');
        } else {
            if (document.form1.e54_numcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautoriza', 'db_iframe_cgm', 'func_nome.php?pesquisa_chave=' + document.form1.e54_numcgm.value + '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_mostracgm(erro, chave) {

        document.form1.z01_nome.value = chave;
        if (erro == true) {
            document.form1.e54_numcgm.focus();
            document.form1.e54_numcgm.value = '';
        } else {
            js_debitosemaberto();
        }
    }

    function js_mostracgm1(chave1, chave2) {

        document.form1.e54_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_cgm.hide();

        js_debitosemaberto();
    }

    function js_pesquisae54_login(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_db_usuarios', 'func_db_usuarios.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome', 'Pesquisa', true, '0', '1');
        } else {
            if (document.form1.e54_login.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_db_usuarios', 'func_db_usuarios.php?pesquisa_chave=' + document.form1.e54_login.value + '&funcao_js=parent.js_mostradb_usuarios', 'Pesquisa', false);
            } else {
                document.form1.nome.value = '';
            }
        }
    }

    function js_mostradb_usuarios(chave, erro) {
        document.form1.nome.value = chave;
        if (erro == true) {
            document.form1.e54_login.focus();
            document.form1.e54_login.value = '';
        }
    }

    function js_mostradb_usuarios1(chave1, chave2) {
        document.form1.e54_login.value = chave1;
        document.form1.nome.value = chave2;
        db_iframe_db_usuarios.hide();
    }

    function js_pesquisa() {
        <?php
        if ($db_opcao == 2 || $db_opcao == 22) {
            $iframe = "selempautoriza";
        } else {
            $iframe = "selempautoriza";
        }
        ?>
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_empautoriza', 'db_iframe_<?=$iframe?>', 'func_<?=$iframe?>.php?funcao_js=parent.js_preenchepesquisa|e54_autori', 'Pesquisa', true, '0', '1');
    }

    function js_preenchepesquisa(chave) {
        db_iframe_<?=$iframe?>.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    /**
     * Procura se o fornecedor possui débitos em aberto
     */
    function js_debitosemaberto() {

        var sUrlRPC = 'com4_notificafornecedor.RPC.php';
        var iCgm = $('e54_numcgm').value;

        if ($('pesquisar')) {
            $('pesquisar').disabled = true;
        }

        if ($('novo')) {
            $('novo').disabled = true;
        }

        if ($('lancemp')) {
            $('lancemp').disabled = true;
        }

        if ($('importar')) {
            $('importar').disabled = true;
        }

        $('db_opcao').disabled = true;

        js_divCarregando('Aguarde, verificando débitos em aberto...', "msgBoxDebitosEmAberto");

        var oParam = new Object();
        oParam.sExecucao = 'debitosEmAberto';
        oParam.iNumCgm = iCgm;
        oParam.sLiberacao = "A";

        var oAjax = new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: js_retornodebitosemaberto
            });
    }

    /**
     * Retorno com os débitos em aberto e informações de configuração
     */
    function js_retornodebitosemaberto(oAjax) {

        js_removeObj("msgBoxDebitosEmAberto");

        var oRetorno = JSON.parse(oAjax.responseText);
        var iNumCgm = new Number(oRetorno.iNumCgm);
        var iParamFornecDeb = new Number(oRetorno.iParamFornecDeb);
        var iDebitosEmAberto = new Number(oRetorno.iDebitosEmAberto);
        var lParamGerarNotifDebitos = oRetorno.lParamGerarNotifDebitos;

        if (iParamFornecDeb == 1) {

            if ($('pesquisar')) {
                $('pesquisar').disabled = false;
            }

            if ($('novo')) {
                $('novo').disabled = false;
            }

            if ($('lancemp')) {
                $('lancemp').disabled = false;
            }

            if ($('importar')) {
                $('importar').disabled = false;
            }

            $('db_opcao').disabled = false;
        } else if (iParamFornecDeb == 2) {

            if (iDebitosEmAberto > 0) {

                var sMensagem = 'O fornecedor ' + iNumCgm + ' possui débitos em aberto.';
                sMensagem += '\n Deseja Notifica-lo?';
                if (confirm(sMensagem)) {
                    js_NotificacaoDebitos(iNumCgm, iParamFornecDeb, oRetorno.aFormaNotificacao, lParamGerarNotifDebitos, true);
                } else {
                    js_NotificacaoDebitos(iNumCgm, iParamFornecDeb, oRetorno.aFormaNotificacao, lParamGerarNotifDebitos, false);
                }
            } else {

                if ($('pesquisar')) {
                    $('pesquisar').disabled = false;
                }

                if ($('novo')) {
                    $('novo').disabled = false;
                }

                if ($('lancemp')) {
                    $('lancemp').disabled = false;
                }

                if ($('importar')) {
                    $('importar').disabled = false;
                }

                $('db_opcao').disabled = false;
            }
        } else if (iParamFornecDeb == 3) {

            if (iDebitosEmAberto > 0) {

                alert('O fornecedor ' + iNumCgm + ' possui débitos em aberto.');

                js_NotificacaoDebitos(iNumCgm, iParamFornecDeb, oRetorno.aFormaNotificacao, lParamGerarNotifDebitos, true);

            } else {
                if ($('pesquisar')) {
                    $('pesquisar').disabled = false;
                }

                if ($('novo')) {
                    $('novo').disabled = false;
                }

                if ($('lancemp')) {
                    $('lancemp').disabled = false;
                }

                if ($('importar')) {
                    $('importar').disabled = false;
                }

                $('db_opcao').disabled = false;
            }
        }
    }

    /**
     * Executa a notificação de débitos ao fornecedor
     */
    function js_NotificacaoDebitos(iNumCgm, iParamFornecDeb, aFormaNotificacao, lGerarNotificacaoDebito, lMostrarJanela) {

        var iOrigem = 3;
        var iCodigoOrigem = $('e54_autori').value;

        oNotificarDebitos = new dbViewNotificaFornecedor(iNumCgm, iOrigem);
        oNotificarDebitos.setCodigoOrigem(iCodigoOrigem);
        oNotificarDebitos.setGerarNotificacaoDebito(lGerarNotificacaoDebito);
        if (lMostrarJanela) {

            oNotificarDebitos.setFormaNotificacao(aFormaNotificacao, true);
            if (aFormaNotificacao.length > 0) {
                oNotificarDebitos.show();
            } else {
                oNotificarDebitos.setFormaNotificacao(aFormaNotificacao, false);
            }
        } else {

            oNotificarDebitos.setGerarNotificacaoDebito(false);
            oNotificarDebitos.setFormaNotificacao(0, false);
        }

        /**
         * Retorno do processo de notificação de debitos
         */
        oNotificarDebitos.setCallBack(function (oRetorno) {

            if (oRetorno.lFormaNotifEmail) {
                alert(oRetorno.sMessage.urlDecode());
            }

            if (oRetorno.lFormaNotifCarta) {
                js_emitircartanotificacao(oRetorno.iCodigoNotificaBloqueioFornecedor);
            }

            if ($('pesquisar')) {
                $('pesquisar').disabled = false;
            }

            if ($('novo')) {
                $('novo').disabled = false;
            }

            if ($('lancemp')) {
                $('lancemp').disabled = false;
            }

            if ($('importar')) {
                $('importar').disabled = false;
            }

            $('db_opcao').disabled = false;
            if (iParamFornecDeb == 3) {
                $('e54_numcgm').value = '';
                $('z01_nome').value = '';
            }
        });
    }

    function js_emitircartanotificacao(iCodigoNotificaBloqueioFornecedor) {

        var jan = window.open('com2_emitircartanotificacao002.php?iCodigoNotificaBloqueioFornecedor=' + iCodigoNotificaBloqueioFornecedor,
            '',
            'width=' + (screen.availWidth - 5) +
            ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
        jan.moveTo(0, 0);
    }
</script>
