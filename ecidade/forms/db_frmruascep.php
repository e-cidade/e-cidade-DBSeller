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

//MODULO: cadastro
$clruas->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("j29_cep");
$clrotulo->label("j29_inicio");
$clrotulo->label("j29_final");

?>
<div class="container">
    <form name="form1">
        <fieldset>
            <legend>
                <b>Cadastro de CEP's</b>
            </legend>
            <table>
                <tr>
                    <td type="hidden"><input type="hidden" id="j29_codigo" name="j29_codigo" value="<?= $chavepesquisa ?>"></td>
                </tr>
                <tr>
                    <label for="CEP:">
                        <td nowrap title="<?=@$Tj29_cep?>">
                            <?= @$Lj29_cep ?>
                        </td>
                    </label>
                    <td>
                        <?php
                        db_input('j29_cep', 8, $Ij29_cep, true, 'text', $db_opcao, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="Lado da rua: 1-PAR, 2-IMPAR, 3-AMBOS">
                        <b>Lado:</b>
                    </td>
                    <td>
                        <?php
                        $x = array("1" => "PAR", "2" => "IMPAR", "3" => "AMBOS");
                        db_select('j29_lado', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tj29_inicio ?>">
                        <?= @$Lj29_inicio ?>
                    </td>
                    <td>
                        <?php
                        db_input('j29_inicio', 5, $Ij29_inicio, true, 'text', $db_opcao, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tj29_final ?>">
                        <?= @$Lj29_final ?>
                    </td>
                    <td>
                        <?php
                        db_input('j29_final', 5, $Ij29_final, true, 'text', $db_opcao, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input name="salvar"
                               type="button"
                               value="Salvar"
                        >
                        <input name="limpar"
                               type="button"
                               value="Cancelar"
                        >
                    </td>
                </tr>

            </table>

        </fieldset>
</div>
<div class="container">
    <fieldset style="width:640px; margin-top:5px;">
        <div id="gridContainer"></div>
    </fieldset>
</div>


</form>
<script>

    const sUrlRPC = 'ruascep.RPC.php';

    function isEmpty(value) {
        return [null, ''].includes(value.trim());
    }

    const dbGrid = new DBGrid('gridContainer');
    const form = document.querySelector("form[name='form1']");
    const formButtonSalvar = document.querySelector("input[name='salvar']");
    const formButtonLimpar = document.querySelector("input[name='limpar']");

    function limparForm() {

        form.j29_cep.focus();
        form.j29_inicio.disabled = false;
        form.j29_cep.value='';
        form.j29_lado.value = '1';
        form.j29_inicio.value = '';
        form.j29_final.value = '';
        formButtonSalvar.value = 'Salvar';

    }

    formButtonLimpar.addEventListener('click', limparForm);

    function grid() {

        js_divCarregando('Aguarde, pesquisando CEPs cadastrados', 'msgBox');
        //$('j29_codigo').value = '';
        var oParam = {};
        oParam.j29_codigo = $('j29_codigo').value
        oParam.sExec = 'getCep';
        new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {
                    js_removeObj('msgBox');
                    var oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.status === 1 && oRetorno.aDados !== null) {

                        dbGrid.clearAll(true);

                        oRetorno.aDados.each(function (oDado) {
                            var aRowCep = [];
                            aRowCep[0] = oDado.j29_codigo;
                            aRowCep[1] = oDado.j29_inicio;
                            aRowCep[2] = oDado.j29_final;
                            aRowCep[3] = oDado.j29_cep;
                            aRowCep[4] = oDado.j29_lado === '1'? 'PAR' : oDado.j29_lado === '2'? 'IMPAR' : 'AMBOS';
                            aRowCep[5] = `<input type="button" value="A" onclick="editar(${oDado.j29_codigo},${oDado.j29_inicio});">
                                       <input type="button" value="E" onclick="excluir(${oDado.j29_codigo},${oDado.j29_inicio});">`;
                            dbGrid.addRow(aRowCep);
                        });
                        dbGrid.renderRows();
                    }
                }
            }
        );
    }

    function editar(idCep,iIdInicio) {

        js_divCarregando('Aguarde, pesquisando CEP cadastrado', 'msgBox');
        limparForm();
        var oParam = {};
        oParam.sExec = 'buscaCepUnico';
        oParam.iIdCep = idCep;
        oParam.iIdInicio = iIdInicio;
        form.j29_inicio.disabled = true;
        new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {
                    js_removeObj('msgBox');
                    const oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.status === 1) {
                        formButtonSalvar.value = "Alterar";
                        form.j29_inicio.setValue(oRetorno.aDados.j29_inicio);
                        form.j29_final.setValue(oRetorno.aDados.j29_final);
                        form.j29_cep.setValue(oRetorno.aDados.j29_cep);
                        form.j29_lado.setValue(oRetorno.aDados.j29_lado);
                    }
                }
            });
    }


    function salvar() {
        if (isEmpty(form.j29_inicio.value)) {
            alert("O Campo Nº Início é obrigatório");
            return;
        }

        if (isEmpty(form.j29_final.value)) {
            alert("O Campo Nº Final é obrigatório");
            return;
        }

        if (isEmpty(form.j29_cep.value)) {
            alert("O Campo CEP é obrigatório");
            return;
        }

        js_divCarregando('Aguarde, cadastrando CEP', 'msgBox');
        const oParam = {}
        oParam.sExec = 'salvar';
        oParam.j29_codigo = form.j29_codigo.value;
        oParam.j29_inicio = form.j29_inicio.value;
        oParam.j29_final = form.j29_final.value;
        oParam.j29_cep = form.j29_cep.value;
        oParam.j29_lado = form.j29_lado.value;
        new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {
                    js_removeObj('msgBox');
                    var oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.status === 1) {

                        alert('CEP cadastrado com sucesso.');
                        limparForm();
                        grid();
                        return;
                    }
                    alert(oRetorno.message);
                    return false;

                }
            }
        );
    }


    function alterar() {


        if (isEmpty(form.j29_final.value)) {
            alert("O Campo Nº Final é obrigatório");
            return;
        }

        if (isEmpty(form.j29_cep.value)) {
            alert("O Campo CEP é obrigatório");
            return;
        }

        js_divCarregando('Aguarde, alterando CEP', 'msgBox');
        const oParam = {}
        oParam.sExec = 'alterar';
        oParam.j29_codigo = form.j29_codigo.value;
        oParam.j29_inicio = form.j29_inicio.value;
        oParam.j29_final = form.j29_final.value;
        oParam.j29_cep = form.j29_cep.value;
        oParam.j29_lado = form.j29_lado.value;
        new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {
                    js_removeObj('msgBox');
                    var oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.status === 1) {

                        alert('CEP alterado com sucesso.');
                        limparForm();
                        grid();
                        return;
                    }
                    alert(oRetorno.message);
                    return false;

                }
            }
        );
    }

    function excluir(iIdCep,iIdInicio) {

        if (confirm('Deseja realmente excluir ?')) {
            js_divCarregando('Aguarde, excluindo CEP', 'msgBox');
            const oParam = {};
            oParam.sExec = 'excluir';
            oParam.iIdCep = iIdCep;
            oParam.iIdInicio = iIdInicio;
            new Ajax.Request(sUrlRPC,
                {
                    method: 'post',
                    parameters: 'json=' + Object.toJSON(oParam),
                    onComplete: function (oAjax) {
                        js_removeObj('msgBox');
                        var oRetorno = JSON.parse(oAjax.responseText);
                        if (oRetorno.status === 1) {
                            alert('CEP excluído com sucesso.');
                            grid();
                            return;
                        }
                        alert(oRetorno.message);
                        return false;

                    }
                }
            );
        }
    }

    function main() {
        dbGrid.nameInstance = 'dbGrid';
        dbGrid.hasTotalizador = false;
        dbGrid.setHeight(150);
        dbGrid.allowSelectColumns(false);
        const aAligns = [];
        aAligns[0] = 'center';
        aAligns[1] = 'center';
        aAligns[2] = 'center';
        aAligns[3] = 'center';
        aAligns[4] = 'center';
        aAligns[5] = 'center';
        const aHeader = [];
        aHeader[0] = 'Cód.Logradouro';
        aHeader[1] = 'Nº Início';
        aHeader[2] = 'Nº Final';
        aHeader[3] = 'CEP';
        aHeader[4] = 'Lado';
        aHeader[5] = 'Ação';
        dbGrid.setCellAlign(aAligns);
        dbGrid.setCellWidth(new Array("20%","10%","10%",'20%', "20%", "20%"));
        dbGrid.setHeader(aHeader);
        dbGrid.show($('gridContainer'));
        limparForm();
        formButtonSalvar.addEventListener('click', function () {
            switch (formButtonSalvar.value.toLocaleLowerCase()) {
                case 'salvar':
                    salvar();
                    break;
                case "alterar" :
                    alterar();
                    break;
            }
        });
        grid();
    }

    main();
</script>

