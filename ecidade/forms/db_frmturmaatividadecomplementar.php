
<?php
$escola = new Escola(db_getsession('DB_coddepto'));
if ($escola->ofereceAtividadeComplementar() == ESCOLA::NAO_OFERECE_ATIVIDADE_COMPLEMENTAR) {
?>
    <strong>Esta escola NÃO oferece atividades complementares! (Editável em Escola -> Cadastros -> Dados Da Escola -> Infra Estrutura)</strong>
<?php
}
?>

<fieldset style="width:95%">
    <legend><b>Alteração de Turma</b></legend>
    <legend>Horários das Atividades Complementares</legend>
    <table class="form-container">
        <tr>
            <td nowrap='nowrap' colspan="7">
                <center>
                    <input class='diassemana' type="checkbox" value="1" id="domingo" />
                    <label for="domingo"> Domingo </label>

                    <input class='diassemana' type="checkbox" value="2" id="segunda" />
                    <label for="segunda"> Segunda </label>

                    <input class='diassemana' type="checkbox" value="3" id="terca" />
                    <label for="terca"> Terça </label>

                    <input class='diassemana' type="checkbox" value="4" id="quarta" />
                    <label for="quarta"> Quarta </label>

                    <input class='diassemana' type="checkbox" value="5" id="quinta" />
                    <label for="quinta"> Quinta </label>

                    <input class='diassemana' type="checkbox" value="6" id="sexta" />
                    <label for="sexta"> Sexta </label>

                    <input class='diassemana' type="checkbox" value="7" id="sabado" />
                    <label for="sabado"> Sábado </label>
                </center>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap">
                Hora inicial:
            </td>

            <td nowrap='nowrap'>
                <input type="text" value="" name="horaInicial" id="horaInicial" maxlength="5" onblur="js_validaHora24Horas(this, event);" />

                Hora Final:
                <input type="text" value="" name="horaFinal" id="horaFinal" maxlength="5" onblur=" validaPeriodoHora(event);" />
            </td>
        </tr>

        <tr>
            <td nowrap title="<?=$Ted146_censoativcompl?>">
                <?php db_ancora($Led146_censoativcompl,"js_persquisaed146_censoativcompl(true);",1);?>
            </td>
            <td>
                <?php db_input('ed146_censoativcompl',10,@$Ied57_i_codigo,true,'text',3,"")?>
                <?php db_input('ed133_c_descr',50,@$Ied268_c_descr,true,'text',3,'')?>
            </td>
        </tr>

        <tr>
            <td nowrap='nowrap'>Turno:</td>
            <td nowrap='nowrap'>
                <select id="turnoatividade">
                    <option selected="selected" value="">Selecione</option>
                </select>
            </td>
        </tr>

        <tr>
            <td nowrap='nowrap'>Função:</td>
            <td nowrap='nowrap'>
                <select id="atividade">
                    <option selected="selected" value="">Selecione</option>
                </select>
            </td>
        </tr>

        <tr>
            <td nowrap='nowrap'>Profissional/Monitor:</td>
            <td nowrap='nowrap'>
                <input type="hidden" value="" id="iRecHumano" name="iRecHumano" />
                <input type="text" value="" id="sNomeRechumano" name="sNomeRechumano" size="53" />
            </td>
        </tr>
    </table>
</fieldset>


<input type="button" id="salvar" name="salvar" value="Salvar">

<div class="subcontainer">
    <fieldset style="min-width: 800px;">
        <legend>Profissionais/Monitores vinculados</legend>
        <div id="ctnGrid"></div>
    </fieldset>
</div>

<script type="text/javascript">

    new MaskedInput("#horaInicial", "00:00", { placeholder: "0" });
    new MaskedInput("#horaFinal", "00:00", { placeholder: "0" });

    const MSG_TURMAACHORARIO = 'educacao.escola.edu1_turmaachorarioprofissional001.';

    var oGet = js_urlToObject();
    var oGrid = new DBGrid('gridProfissionais');
    oGrid.nameInstance = 'oGrid';
    oGrid.setCellAlign(['left', 'center', 'left', 'left', 'center']);
    oGrid.setCellWidth(['10%', '18%', '30%', '37%', '5%']);
    oGrid.setHeader(['Dia', 'Horário', 'Atividade', 'Profissional/Monitor', 'Ação']);
    oGrid.setHeight(150);
    oGrid.show($('ctnGrid'));


    (function () {
        js_carregaAtivivadesComplementares();
        js_carregaAtividades();
        js_carregaTurnosDisponiveis();

    })();

    function js_persquisaed146_censoativcompl(mostra) {

        var sUrl = 'func_censoativcompl.php?';
        sUrl += 'iCalendario=' + oGet.iCalendario;

        if (mostra) {
            sUrl += '&funcao_js=parent.js_mostracensoativcompl1|ed133_i_codigo|ed133_c_descr';
            js_OpenJanelaIframe('', 'db_iframe_censoativcompl', sUrl, 'Pesquisa atividades complementares', true);
        } else {

            if ($F('ed267_i_censoativcompl') != '') {

                sUrl += '&pesquisa_chave=' + $F('ed267_i_censoativcompl') + '&funcao_js=parent.js_mostracensoativcompl';
                js_OpenJanelaIframe('', 'db_iframe_censoativcompl', sUrl, 'Pesquisa atividades complementares', false);
            } else {

                document.form1.ed133_c_descr.value = '';
                document.getElementById("outraativ").style.visibility = 'hidden';
                document.form1.ed274_c_nome.value = '';
                document.form1.ed274_i_codigo.value = '';
            }
        }
    }

    function js_mostracensoativcompl1(ed133_i_codigo, ed133_c_descr) {
        var form = document.getElementById('form1');
        form.ed146_censoativcompl.value = ed133_i_codigo;
        form.ed133_c_descr.value = ed133_c_descr;

        db_iframe_censoativcompl.hide();
    }

    /**
     * Busca as atividades complementares vinculadas a turma
     */
    function js_carregaAtivivadesComplementares() {

        var oParametros = {};
        oParametros.exec = 'getAtividadesComplementares';
        oParametros.turma = oGet.ed57_i_codigo;

        var oRequest = {};
        oRequest.asynchronous = false;
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametros);
        oRequest.onComplete = function (oAjax) {


            js_removeObj('msgBoxA');
            var oRetorno = JSON.parse(oAjax.responseText);

            oGrid.clearAll(true);
            if (oRetorno.atividades.length == 0) {
                return;
            }

            var diasSemana = {
                1: 'Domingo',
                2: 'Segunda',
                3: 'Terça',
                4: 'Quarta',
                5: 'Quinta',
                6: 'Sexta',
                7: 'Sábado',
            };
            oRetorno.atividades.each(function (atividade, i) {

                var oBtn = new Element('input', { 'type': 'button', 'value': 'E' });
                oBtn.setAttribute("onclick", 'desvincularAtividade(' + atividade.codigoVinculo + ')');

                var sHorario = atividade.horaInicial + ' até ' + atividade.horaFinal;
                var aLinha = [];
                aLinha.push(diasSemana[atividade.iDiaSemana]);
                aLinha.push(sHorario);
                aLinha.push(atividade.sAtividade);
                aLinha.push(atividade.profissional);
                aLinha.push(oBtn.outerHTML);

                oGrid.addRow(aLinha);

            });
            oGrid.renderRows();

        };

        js_divCarregando(_M(MSG_TURMAACHORARIO + "buscando_vinculados"), 'msgBoxA');
        new Ajax.Request('edu4_turmas.RPC.php', oRequest);
    }

    function js_carregaAtividades() {

        var oParametros = {};
        oParametros.exec = 'getAtividades';

        var oRequest = {};
        oRequest.asynchronous = false;
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametros);
        oRequest.onComplete = function (oAjax) {

            js_removeObj('msgBoxB');
            var oRetorno = JSON.parse(oAjax.responseText);

            if (oRetorno.aFuncoes.length == 0) {
                return;
            }
            $('atividade').options.length = 0;
            $('atividade').add(new Option("Selecione", ''));
            oRetorno.aFuncoes.each(function (oAtividade) {
                $('atividade').add(new Option(oAtividade.ed119_descricao.urlDecode(), oAtividade.ed119_sequencial));
            });

        };

        js_divCarregando(_M(MSG_TURMAACHORARIO + "busca_atividades"), 'msgBoxB');
        new Ajax.Request('edu4_turmas.RPC.php', oRequest);
    }

    function js_carregaTurnosDisponiveis() {
        var oParametros = {};
        oParametros.exec = 'getTurnosDisponiveis';
        oParametros.turma = oGet.ed57_i_codigo;

        var oRequest = {};
        oRequest.asynchronous = false;
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametros);
        oRequest.onComplete = function (oAjax) {

            var oRetorno = JSON.parse(oAjax.responseText);
            js_removeObj('msgBoxA');

            if (oRetorno.turnos.length == 0) {

                return;
            }

            $('turnoatividade').options.length = 0;
            $('turnoatividade').add(new Option("Selecione", ''));
            for(var i in oRetorno.turnos) {
                $('turnoatividade').add(new Option(oRetorno.turnos[i].urlDecode(), i));
            }
        };

        js_divCarregando('Buscando turnos', 'msgBoxA');
        new Ajax.Request('edu4_turmas.RPC.php', oRequest);
    }


    $('atividade').observe('change', function () {

        $('iRecHumano').value = '';
        $('sNomeRechumano').value = '';

        if ($F('atividade') != '') {

            /**
             * Declaração do auto complete para Profissional
             */
            $('sNomeRechumano').onkeydown = '';
            var sRpcAutoComplete = 'edu4_pesquisarechumano.RPC.php?lFiltraAtividade=true&iAtividade=' + $F('atividade');
            var oAutoComplete = new dbAutoComplete($('sNomeRechumano'), sRpcAutoComplete);
            oAutoComplete.setTxtFieldId($('iRecHumano'));
            oAutoComplete.show();
            oAutoComplete.setMinLength(2);
        }
    });



    $('sNomeRechumano').observe('keyup', function () {

        if ($F('sNomeRechumano') == '') {
            $('iRecHumano').value = '';
        }

        if ($F('atividade') == '') {

            $('sNomeRechumano').value = '';
            $('atividade').focus();
            alert(_M(MSG_TURMAACHORARIO + "selecione_atividade"));

        }

    });


    /**
     * Valida se a hora inicial é menor que a hora final
     * @param oEvent
     * @returns {boolean}
     */
    function validaPeriodoHora(oEvent) {

        if (!js_validaHora24Horas($('horaFinal'), oEvent)) {
            return
        }

        if (empty($F('horaInicial'))) {

            alert(_M(MSG_TURMAACHORARIO + "hora_inicial_nao_informado"));

            oEvent.preventDefault();
            oEvent.stopPropagation();
            setTimeout(function () {
                $('horaInicial').focus();
            }, 10);
            return false;
        }

        var iHoraInicial = $F('horaInicial').substr(0, 2);
        var iMinutosInicial = $F('horaInicial').substr(3, 2);
        var iHoraFinal = $F('horaFinal').substr(0, 2);
        var iMinutosFinal = $F('horaFinal').substr(3, 2);

        var oDataAtual = new Date();
        var oHoraInicial = new Date(oDataAtual.getFullYear(), oDataAtual.getMonth(), oDataAtual.getDate(), iHoraInicial, iMinutosInicial);
        var oHoraFinal = new Date(oDataAtual.getFullYear(), oDataAtual.getMonth(), oDataAtual.getDate(), iHoraFinal, iMinutosFinal);

        if ((oHoraInicial.getHours() > oHoraFinal.getHours())
            || (oHoraInicial.getHours() == oHoraFinal.getHours() && oHoraInicial.getMinutes() > oHoraFinal.getMinutes())) {

            alert(_M(MSG_TURMAACHORARIO + "conflito_entre_horas"));
            $('horaFinal').value = '00:00';
            oEvent.preventDefault();
            oEvent.stopPropagation();
            setTimeout(function () {
                $('horaFinal').focus();
            }, 10);
            return false;
        }

        return true;
    }

    function js_getDiasSemanaSelecionado() {

        var aDiasSemanaSelecionado = [];

        $$('.diassemana').each(function (oElement) {

            if (oElement.checked) {
                aDiasSemanaSelecionado.push(oElement.value);
            }
        });
        return aDiasSemanaSelecionado;
    }
    function js_validaCamposObrigatorios() {

        if (empty($F('horaInicial'))) {

            alert(_M(MSG_TURMAACHORARIO + "hora_inicial_nao_informado"));
            return false;
        }

        if (empty($F('horaFinal'))) {

            alert(_M(MSG_TURMAACHORARIO + "hora_final_nao_informado"));
            return false;
        }

        if (empty($F('iRecHumano'))) {

            alert(_M(MSG_TURMAACHORARIO + "profissional_nao_informado"));
            return false;
        }

        if (empty($F('ed146_censoativcompl'))) {
            alert('A atividade complementar deve ser informada.');
            return false;
        }

        if (empty($F('turnoatividade'))) {
            alert('O turno deve ser informado.');
            return false;
        }

        return true;
    }

    $('salvar').observe('click', function () {

        var aDiasSemana = js_getDiasSemanaSelecionado();
        if (empty(aDiasSemana)) {

            alert(_M(MSG_TURMAACHORARIO + "selecione_dia_semana"));
            return false;
        }

        if (!js_validaCamposObrigatorios()) {
            return false;
        }

        var oParametros = {};
        oParametros.exec = 'vincularAtividadeComplementar';
        oParametros.turma = oGet.ed57_i_codigo;
        oParametros.atividadeComplementar = $F('ed146_censoativcompl');
        oParametros.funcaoAtividade = $F('atividade');
        oParametros.recHumano = $F('iRecHumano');
        oParametros.diasSemana = aDiasSemana;
        oParametros.turnoReferente = $F('turnoatividade');
        oParametros.horaInicial = $F('horaInicial');
        oParametros.horaFinal = $F('horaFinal');

        var oRequest = {};
        oRequest.asynchronous = false;
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametros);
        oRequest.onComplete = function (oAjax) {

            js_removeObj('msgBoxB');
            var oRetorno = JSON.parse(oAjax.responseText);

            alert(oRetorno.message.urlDecode());
            if (oRetorno.erro) {
                return false;
            }

            document.getElementById('form1').reset();
            js_carregaAtivivadesComplementares();
        };

        js_divCarregando(_M(MSG_TURMAACHORARIO + "salvando"), 'msgBoxB');
        new Ajax.Request('edu4_turmas.RPC.php', oRequest);

    });

    function desvincularAtividade(iCodigoVinculo) {
        var oParametros = {};
        oParametros.exec = 'removeAtividadeComplementar';
        oParametros.iVinculo = iCodigoVinculo;

        var oRequest = {};
        oRequest.asynchronous = false;
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametros);
        oRequest.onComplete = function (oAjax) {

            js_removeObj('msgBoxB');
            var oRetorno = JSON.parse(oAjax.responseText);

            alert(oRetorno.message.urlDecode());
            if (oRetorno.erro) {
                return false;
            }
            js_carregaAtivivadesComplementares();
        };

        js_divCarregando(_M(MSG_TURMAACHORARIO + "removendo_vinculo"), 'msgBoxB');
        new Ajax.Request('edu4_turmas.RPC.php', oRequest);
    }

</script>


<?php
    if ($escola->ofereceAtividadeComplementar() == ESCOLA::NAO_OFERECE_ATIVIDADE_COMPLEMENTAR) {
        ?>

        <script>
            $('salvar').disabled = true;
        </script>

    <?php
    }
?>
