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

$oSaudeConfiguracao = new SaudeConfiguracao();
$lCNSObrigatorio = $oSaudeConfiguracao->obrigarCns();
?>
<fieldset class="separator">
    <legend>Controles CGS</legend>

    <table class="form-container">
        <tr>
            <td>
                <label for="cadastroInativo">Cadastro Inativo:</label>
            </td>
            <td>
                <input id="cadastroInativo" type="checkbox"/>
                <label id="mensagemBloqueioInativo" style="display:none">
                    Campo bloqueado: Para desmarcá-lo excluir o registro de óbito
                </label>
            </td>
            <td>
                
            </td>
        </tr>

        <tr>
            <td>
                <label for="cgsMunicipio">CGS do Município:</label>
            </td>
            <td>
                <select id="cgsMunicipio" style="width: 66%;">
                    <option value="t">SIM</option>
                    <option value="f">NÃO</option>
                </select>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="separator">
    <legend>Dados do Usuário</legend>
    <table class="form-container">
        <tr>
            <td>
                <label for="cns">CNS:</label>
            </td>
            <td>
                <input id="cns" type="text" value="" class="field-size3" maxlength="15"/>
                <input id="btn-consulta-cns" type="button" value="Consultar"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="cpf">CPF:</label>
            </td>
            <td>
                <input id="cpf" type="text" value="" class="field-size3" maxlength="14"
                       onkeypress="formatar(this,'000.000.000-00')"/>
                <input id="btn-consulta-cpf" type="button" value="Consultar"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="rg">RG:</label>
            </td>

            <td>
                <input id="rg" type="text" class="field-size3" maxlength="11"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="nome">Nome: <span style="color: red">*</span></label>
            </td>
            <td>
                <input id="nome" type="text" value="" class="field-size7" maxlength="255"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="nome">Nome Social:</label>
            </td>
            <td>
                <input id="nomeSocial" type="text" value="" class="field-size7" maxlength="255"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="nomeMae">Nome da Mãe: <span style="color: red">*</span></label>
            </td>
            <td>
                <input id="nomeMae" type="text" value="" class="field-size7 mae-usuario" maxlength="255"/>
            </td>
            <td>
                <input id="desconheceMae" type="checkbox" class="mae-usuario"/>
            </td>
            <td>
                <label for="desconheceMae">Sem informação</label>
            </td>
        </tr>

        <tr>
            <td>
                <label for="nomePai">Nome do Pai: <span style="color: red">*</span></label>
            </td>
            <td>
                <input id="nomePai" type="text" value="" class="field-size7 pai-usuario" maxlength="40"/>
            </td>
            <td>
                <input id="desconhecePai" type="checkbox" class="pai-usuario"/>
            </td>
            <td>
                <label for="desconhecePai">Sem informação</label>
            </td>
        </tr>

        <tr>
            <td>
                <label for="sexo">Sexo: <span style="color: red">*</span></label>
            </td>
            <td class="field-size6">
                <select id="sexo">
                    <option value=''>Selecione...</option>
                    <option value="M">MASCULINO</option>
                    <option value="F">FEMININO</option>
                    <option value="N">NÃO INFORMADO</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <label for="racaCor">Raça/Cor: <span style="color: red">*</span></label>
            </td>
            <td>
                <select id="racaCor">
                    <option value="">Selecione...</option>
                    <option value="BRANCA">BRANCA</option>
                    <option value="PRETA">PRETA</option>
                    <option value="PARDA">PARDA</option>
                    <option value="AMARELA">AMARELA</option>
                    <option value="INDÍGENA">INDÍGENA</option>
                </select>
            </td>
        </tr>

        <tr id="linhaEtnia" style="display: none;">
            <td id="colunaEtnia" class="field-size2"></td>
            <td>
                <input id="codigoEtnia" type="hidden" value=""/>
                <input id="descricaoEtnia" type="text" value="" class="readonly field-size7" readonly="readonly"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="fatorRH">Fator RH:</label>
            </td>
            <td>
                <select id="fatorRH">
                    <option value="0"></option>
                    <option value="1">POSITIVO</option>
                    <option value="2">NEGATIVO</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <label for="tipoSangue">Tipo de Sangue:</label>
            </td>
            <td>
                <select id="tipoSangue">
                    <option value="0"></option>
                    <option value="1">A</option>
                    <option value="2">B</option>
                    <option value="3">O</option>
                    <option value="4">AB</option>
                </select>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="separator">
    <legend>Dados de Nascimento</legend>
    <table class="form-container">
        <tr>
            <td class="field-size2">
                <label for="dataNascimento">Data de Nascimento: <span style="color: red">*</span></label>
            </td>
            <td style="display: flex">
                <input id="dataNascimento" type="text" value="" class="field-size2"/>
                <div style="padding-left: 25px; display: flex; align-items: center; height: 18px;"
                     id="linhaDaIdadeCompleta"></div>
            </td>
        </tr>

        <tr>
            <td class="field-size2">
                <label for="nacionalidade">Nacionalidade:</label>
            </td>
            <td>
                <select id="nacionalidade" style="width: 66%;">
                    <option value="0">BRASILEIRO</option>
                    <option value="1">NATURALIZADO</option>
                    <option value="2">ESTRANGEIRO</option>
                </select>
            </td>
        </tr>

        <tr>
            <td class="field-size2">
                <label for="paisOrigem">País de Origem:</label>
            </td>
            <td>
                <select id="paisOrigem" style="width: 66%;" disabled="disabled"></select>
            </td>
        </tr>

        <tr>
            <td id="colunaMunicipioNascimento" class="field-size2"></td>
            <td>
                <input id="municipioNascimento" type="text" value="" class="readonly field-size7" readonly/>
            </td>
        </tr>

        <tr>
            <td class="field-size2">
                <label for="ufNascimento">UF de Nascimento:</label>
            </td>
            <td>
                <input id="ufNascimento" type="text" value="" class="readonly field-size2" readonly/>
            </td>
        </tr>

        <tr>
            <td class="field-size2">
                <label for="codigoIbge">IBGE:</label>
            </td>
            <td>
                <input id="codigoIbge" type="text" value="" class="readonly field-size2" readonly/>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="separator">
    <legend>Info. do Sistema</legend>
    <table class="form-container">
        <tr>
            <td>
                <label for="usuarioCadastro">Cadastrado Pelo Usuário:</label>
            </td>
            <td>
                <input id="usuarioCadastro" type="text" value="" class="readonly field-size7" readonly/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="dataCadastro">Data de Cadastro:</label>
            </td>
            <td id='tdCad'>
                <input id="dataCadastro" type="text" value="" class="readonly field-size2"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="dataAlteracao">Data da Última Atualização:</label>
            </td>
            <td id='tdAlt'>
                <input id="dataAlteracao" type="text" value="" class="readonly field-size2"/>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="separator">

    <legend>
        Correio eletrônico
    </legend>
    <table class="form-container">
        <tr>
            <td>
                <label for="contato_email">E-mail:</label>
            </td>
            <td>
                <input id="contato_email" name="contato_email" class="field-size-max"/>
            </td>
        </tr>
    </table>

</fieldset>

<fieldset class="separator">

    <legend>
        Telefones
    </legend>
    <table class="form-container">
        <tr>
            <td>
                <label for="contato_telefone_fixo">Telefone Fixo: <span id="telefone_fixo"
                                                                        style="color: red">*</span></label>
            </td>
            <td>
                <input id="contato_telefone_fixo" name="contato_telefone_fixo" onkeypress="mascara(this);"
                       onpaste="mascaraPaste(this);" maxlength="14" placeholder="(XX) XXXX-XXXX"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="contato_telefone_celular">Telefone Celular: <span id="telefone_celular"
                                                                              style="color: red">*</span></label>
            </td>
            <td>
                <input id="contato_telefone_celular" name="contato_telefone_celular" onkeypress="mascara(this, true);"
                       onpaste="mascaraPaste(this, true);" maxlength="15" placeholder="(XX) XXXXX-XXXX"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="contato_fax">Fax:</label>
            </td>
            <td>
                <input id="contato_fax" name="contato_fax" onkeypress="mascara(this);" onpaste="mascaraPaste(this);"
                       maxlength="14" placeholder="(XX) XXXX-XXXX"/>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="separator">

    <legend>Endereço</legend>
    <table class="form-container">
        <tr>
            <td>
                <label>Endereço Principal: <span style="color: red">*</span></label>
            </td>
            <td>
                <input name="contato_endereco_principal" id="contato_endereco_principal"/>
            </td>
        </tr>

        <tr>
            <td>
                <label>Unidade origem:</label>
            </td>
            <td>
                <select name="sd114_unidadesorigem" id="sd114_unidadesorigem" style="width: 66%;">
                    <option value=""></option>
                    <?php
                        $daoUnidadesOrigem = new cl_unidadesorigem;
                        $whereUnidadesOrigem = "sd113_ativo = true";
                        $sqlUnidadesOrigem = $daoUnidadesOrigem->sql_query(
                            null,
                            '*',
                            "sd113_descricao ASC",
                            $whereUnidadesOrigem
                        );                                            
                        $result = db_query($sqlUnidadesOrigem);
                        for($i=0;$i<pg_num_rows($result);$i++){
                            $unidadeOrigem = db_utils::fieldsMemory($result,$i);                                                                                             
                        ?>
                            <option 
                                value="<?=$unidadeOrigem->sd113_sequencial?>" 
                                <?= (@$sd114_unidadesorigem == $unidadeOrigem->sd113_sequencial) ? "selected" : "" ?>>
                               <?=$unidadeOrigem->sd113_descricao?>
                            </option>
                        <?php
                        }
                    ?>                                                                           
                </select>                                           
            </td>
        </tr>        
        <tr>
            <td class="field-size2">
                <label for="distritoSanitario">Distrito Sanitário:</label>
            </td>
            <td>
                <select id="distritoSanitario" style="width: 66%;"></select>
            </td>
        </tr>
    </table>
</fieldset>


<fieldset class="separator">
    <legend>Óbito</legend>
    <table class="form-container">
        <tr>
            <td>
                <label for="informacaoObito">Informar óbito:</label>
            </td>
            <td>
                <select id="informacaoObito" style="width: 98px">
                    <option value="nao">Não</option>
                    <option value="sim">Sim</option>
                </select>
            </td>
        </tr>
        <tr id="trDataObito">
            <td>
                <label for="dataObito">Data de Óbito:</label>
            </td>
            <td>
                <input id="dataObito" type="text" value="" class="field-size2"/>
            </td>
        </tr>
    </table>
</fieldset>

<input id="cnsObrigatorio" type="hidden" value="<?= $lCNSObrigatorio ? 1 : 0 ?>"/>

<script>
    var cpfSemCaractere = "";

    var inputCpf = new DBInputCpf(document.getElementById('cpf'));
    const inputSexo = document.getElementById('sexo');
    const inputRacaCor = document.getElementById('racaCor');

    window.onload = function () {
        const trDataObito = document.getElementById('trDataObito');
        const informacaoObito = document.getElementById('informacaoObito');
        const colunaMunicipioNascimento = document.getElementById('colunaMunicipioNascimento');
        const inputMunicipioNascimento = document.getElementById('municipioNascimento');
        const labelMunicipioNascimento = document.getElementById('municipioNascimento');
        const contato_telefone_fixo = document.getElementById('contato_telefone_fixo');
        const contato_telefone_celular = document.getElementById('contato_telefone_celular');
        const telefone_fixo = document.getElementById('telefone_fixo');
        const telefone_celular = document.getElementById('telefone_celular');
        const defaultInputPhone = "(  )     -     ";

        trDataObito.setAttribute('style', 'display:none');

        const inputsReadonly = document.querySelectorAll('input.readonly');
        for (var i = 0; i < inputsReadonly.length; i++) {
            inputsReadonly[i].tabIndex = -1;
        }
    };

    function formatar(src, mask) {
        if (event.charCode >= 48 && event.charCode <= 57) {
            var i = src.value.length;
            var saida = mask.substring(0, 1);
            var texto = mask.substring(i)
            if (texto.substring(0, 1) != saida) {
                src.value += texto.substring(0, 1);
            }
        } else {
            event.preventDefault();
        }
    }

    contato_telefone_fixo.onkeyup = function (event) {
        if ((contato_telefone_fixo.value != defaultInputPhone && contato_telefone_fixo.value != "") &&
            (contato_telefone_celular.value == defaultInputPhone || contato_telefone_celular.value == "")) {
            telefone_celular.removeAttribute('style');
            telefone_celular.setAttribute('style', 'display:none');
        } else if ((contato_telefone_fixo.value != defaultInputPhone && contato_telefone_fixo.value != "") &&
            (contato_telefone_celular.value != defaultInputPhone || contato_telefone_celular.value != "")) {
            telefone_celular.removeAttribute('style');
            telefone_celular.setAttribute('style', 'color:red');
            telefone_fixo.removeAttribute('style');
            telefone_fixo.setAttribute('style', 'color:red');
        } else if (event.keyCode == 8 &&
            (contato_telefone_fixo.value == defaultInputPhone || contato_telefone_fixo.value == "")) {
            telefone_celular.removeAttribute('style');
            telefone_celular.setAttribute('style', 'color:red');
        }
    };

    contato_telefone_celular.onkeyup = function (event) {
        if ((contato_telefone_celular.value != defaultInputPhone && contato_telefone_celular.value != "") &&
            (contato_telefone_fixo.value == defaultInputPhone || contato_telefone_fixo.value == "")) {
            telefone_fixo.removeAttribute('style');
            telefone_fixo.setAttribute('style', 'display:none');
        } else if ((contato_telefone_celular.value != defaultInputPhone && contato_telefone_celular.value != "") &&
            (contato_telefone_fixo.value != defaultInputPhone || contato_telefone_fixo.value != "")) {
            telefone_celular.removeAttribute('style');
            telefone_celular.setAttribute('style', 'color:red');
            telefone_fixo.removeAttribute('style');
            telefone_fixo.setAttribute('style', 'color:red');
        } else if (event.keyCode == 8 &&
            (contato_telefone_fixo.value == defaultInputPhone || contato_telefone_fixo.value == "")) {
            telefone_fixo.removeAttribute('style');
            telefone_fixo.setAttribute('style', 'color:red');
        }
    };

    var oInputDataNascimento = new DBInputDate($('dataNascimento'));
    var oInputDataObito = new DBInputDate($('dataObito'));

    var oInputDataAlteracao = new DBInputDate($('dataAlteracao'));
    $('tdAlt').childElements()[0].disabled = true;
    $('tdAlt').childElements()[1].style.visibility = 'hidden';

    var oInputDataCadastro = new DBInputDate($('dataCadastro'));
    $('tdCad').childElements()[0].disabled = true;
    $('tdCad').childElements()[1].style.visibility = 'hidden';


    var oAncoraMunicipio = new DBAncora('Município de Nascimento: ', '#', true);
    oAncoraMunicipio.onClick(buscaMunicipioNascimento);
    oAncoraMunicipio.show($('colunaMunicipioNascimento'));

    var span = document.createElement('span');
    span.innerHTML = '*';
    span.style.color = 'red';
    $('colunaMunicipioNascimento').appendChild(span);

    var oAncoraEtnia = new DBAncora('Etnia:', '#', true);
    oAncoraEtnia.onClick(buscaEtnias);
    oAncoraEtnia.show($('colunaEtnia'));

    var dadosPessoais = {
        "cns": $("cns"),
        "codigo_cartao_sus": null,
        "nome": $("nome"),
        "nomeSocial": $("nomeSocial"),
        "nomeMae": $("nomeMae"),
        "nomePai": $("nomePai"),
        "sexo": $("sexo"),
        "racaCor": $("racaCor"),
        "codigo_etnia": $("codigoEtnia"),
        "label_etnia": $("descricaoEtnia"),
        "fatorRH": $("fatorRH"),
        "tipoSangue": $("tipoSangue"),
        "dataNascimento": oInputDataNascimento,
        "nacionalidade": $("nacionalidade"),
        "paisOrigem": $("paisOrigem"),
        "municipioNascimento": $("municipioNascimento"),
        "ufNascimento": $("ufNascimento"),
        "codigoIbge": $("codigoIbge"),
        'informacaoObito': informacaoObito,
        "dataObito": oInputDataObito,
        "cadastroInativo": $('cadastroInativo'),
        "cgsMunicipio": $('cgsMunicipio'),
        "dataAlteracao": oInputDataAlteracao,
        "dataCadastro": oInputDataCadastro,
        "cpf": $("cpf"),
        "rg": $("rg"),
        "distritoSanitario": $("distritoSanitario"),
        "unidadeOrigem":$("sd114_unidadesorigem")
    };

    var contato = {
        'telefone_fixo': $('contato_telefone_fixo'),
        'telefone_celular': $('contato_telefone_celular'),
        'fax': $('contato_fax'),
        'email': new DBInputEmail($('contato_email')),
        'endereco_principal': new DBInputEndereco($('contato_endereco_principal'), true)
    };

    /**
     * Bloqueia o campo nome da mãe ou do pai de acorco com a ação do checkbox referente a cada um
     * @param oElemento
     */
    function bloqueiaNome(oElemento) {

        var oInputElement = $$('input[type=text].' + oElemento.className)[0];
        oInputElement.value = "";
        oInputElement.readOnly = false;
        oInputElement.removeClassName('readonly');

        if (oElemento.checked === true) {

            oInputElement.value = "SEM INFORMAÇÃO";
            oInputElement.readOnly = true;
            oInputElement.addClassName('readonly');
        }
    }

    /**
     * Busca o município de nascimento
     */
    function buscaMunicipioNascimento() {

        if ($F('nacionalidade') != 0) {

            alert(_M(MENSAGENS_MANUTENCAO_CGS + 'nacionalidade_invalida'));
            return;
        }

        var sUrl = "func_cadendermunicipiosistema.php";
        sUrl += "?iTipoSistema=4";
        sUrl += "&funcao_js=parent.retornoBuscaMunicipioNascimento|db72_descricao|db71_sigla|db125_codigosistema";

        js_OpenJanelaIframe('', 'db_iframe_cadendermunicipiosistema', sUrl, 'Pesquisa Município', true);
    }

    /**
     * Preenche os dados do município de nascimento
     */
    function retornoBuscaMunicipioNascimento() {

        db_iframe_cadendermunicipiosistema.hide();

        $('municipioNascimento').value = arguments[0];
        $('ufNascimento').value = arguments[1];
        $('codigoIbge').value = arguments[2];

        colunaMunicipioNascimento.childNodes[0].focus();

    }

    /**
     * Busca as etnias quando selecionada raça INDÍGENA
     */
    function buscaEtnias() {

        var sUrl = 'func_etnia.php?';
        sUrl += 'funcao_js=parent.retornoBuscaEtnias|s200_codigo|s200_descricao';

        js_OpenJanelaIframe('', 'db_iframe_etnia', sUrl, 'Pesquisa Etnia', true);
    }

    /**
     * Retorna a etnia selecionada e preenche o código e descrição
     */
    function retornoBuscaEtnias() {

        $('codigoEtnia').value = arguments[0];
        $('descricaoEtnia').value = arguments[1];

        db_iframe_etnia.hide();
    }

    /**
     * Quando nacionalidade não for Brasileiro, limpa os campos de município, UF e IBGE
     */
    function validaNacionalidade() {

        $('paisOrigem').setAttribute('disabled', 'disabled');

        if ($F('nacionalidade') == 0) {
            $('paisOrigem').value = 10;
        }

        if ($F('nacionalidade') != 0) {

            $('paisOrigem').removeAttribute('disabled');

            $('municipioNascimento').value = '';
            $('ufNascimento').value = '';
            $('codigoIbge').value = '';
        }
    }

    /**
     * Controla se a linha da etnia deve ser apresentada, caso tenha sido selecionada raça INDÍGENA
     */
    function validaRaca() {

        if ($F('racaCor') == 'INDÍGENA') {

            $('linhaEtnia').setStyle({'display': ''});
            return;
        }

        $('linhaEtnia').setStyle({'display': 'none'});
        $('codigoEtnia').value = '';
        $('descricaoEtnia').value = '';
    }

    /**
     * Valida os dados pessoais obrigatórios
     * @returns {boolean}
     */
    function validaDadosPessoais() {

        if (document.getElementById('permite-alteracao').value == 1 || iCgs != "") {

            if ($F('cnsObrigatorio') == 1 && empty($F('cns'))) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'cns_nao_informado'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);
                $('cns').focus();

                return false;
            }

            if ($F('cns').trim() != '' && !$F('cns').validaCNS()) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'cns_invalido'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);
                $('cns').focus();

                return false;
            }

            if (($('cpf').value != '' && $('cpf').value.length < 14) || !inputCpf.valid) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'cpf_invalido'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);
                $('cpf').focus();

                return false;
            }

            if (empty($F('nome'))) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'nome_nao_informado'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);

                $('nome').focus();

                return false;
            }

            if ($('desconheceMae').checked === false && empty($F('nomeMae'))) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'nome_mae_nao_informado'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);
                $('nomeMae').focus();

                return false;
            }

            if (inputSexo.value == '') {
                alert('Selecione o sexo do paciente!');
                inputSexo.focus();

                return false;
            }

            if (inputRacaCor.value == '') {
                alert('Selecione a Raça/Cor.');
                inputRacaCor.focus();

                return false;
            }

            if (oInputDataNascimento.getValue() == null) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'data_nascimento_nao_informada'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);
                $('dataNascimento').focus();

                return false;
            }

            if ($F('nacionalidade') == 0
                && (empty($F('municipioNascimento')) || empty($F('ufNascimento')) || empty($F('codigoIbge')))
            ) {

                alert(_M(MENSAGENS_MANUTENCAO_CGS + 'local_nascimento_nao_informado'));
                oDBAba.mostraFilho(oDBAba.aAbas[0]);
                return false;
            }

            if (document.getElementById('nomePai').value == '' && !document.getElementById('desconhecePai').checked) {
                alert('É necessário preencher o nome do pai ou selecionar a opção "Sem informação".');
                return false;
            }
        }
        return true;
    }

    /**
     * Seta os atributos dos dados pessoais a serem salvos
     * @param oParametros
     */
    function setValoresDadosPessoais(oParametros) {
        oParametros.dados_pessoais = {
            "cns": dadosPessoais.cns.value,
            "codigo_cartao_sus": iCodigoCartaoSus,
            "nome": dadosPessoais.nome.value,
            "nomeSocial": dadosPessoais.nomeSocial.value,
            "nomeMae": dadosPessoais.nomeMae.value,
            "nomePai": dadosPessoais.nomePai.value,
            "sexo": dadosPessoais.sexo.value,
            "racaCor": dadosPessoais.racaCor.value,
            "codigo_etnia": dadosPessoais.codigo_etnia.value,
            "label_etnia": dadosPessoais.label_etnia.value,
            "fatorRH": dadosPessoais.fatorRH.value,
            "tipoSangue": dadosPessoais.tipoSangue.value,
            "dataNascimento": dadosPessoais.dataNascimento.__toLocaleDateString(),
            "nacionalidade": dadosPessoais.nacionalidade.value,
            "paisOrigem": dadosPessoais.paisOrigem.value,
            "municipioNascimento": dadosPessoais.municipioNascimento.value,
            "ufNascimento": dadosPessoais.ufNascimento.value,
            "codigoIbge": dadosPessoais.codigoIbge.value,
            "dataObito": dadosPessoais.dataObito.getValue() != null ? dadosPessoais.dataObito.__toLocaleDateString() : '',
            "informacaoObito": dadosPessoais.informacaoObito.value,
            "cadastroInativo": dadosPessoais.cadastroInativo.checked === true,
            "cgsMunicipio": dadosPessoais.cgsMunicipio.value == 't',
            "cpf": dadosPessoais.cpf.value,
            "rg": dadosPessoais.rg.value,
            "distritoSanitario": dadosPessoais.distritoSanitario.value,
            "unidadeOrigem":dadosPessoais.unidadeOrigem.value
        }
    }

    /**
     * Seta os atributos dos contatos a serem salvos
     * @param oParametros
     */
    function setValoresContatos(oParametros) {

        oParametros.contato = {
            'telefone_fixo': contato.telefone_fixo.getValue(),
            'telefone_celular': contato.telefone_celular.getValue(),
            'fax': contato.fax.getValue(),
            'email': contato.email.getValue(),
            'endereco_principal': contato.endereco_principal.getValue()
        };
    }

    validacoes.push(validaDadosPessoais);

    validacoes.push(function () {

        if (!contato.telefone_fixo.getValue() && !contato.telefone_celular.getValue()) {

            alert(_M(MENSAGENS_MANUTENCAO_CGS + 'contato_telefone_vazio'));
            contato.telefone_fixo.inputElement.focus();
            return false;
        }

        if (contato.email.getValue() && !contato.email.isValid()) {

            alert(_M(MENSAGENS_MANUTENCAO_CGS + 'contato_email_invalido'));

            contato.telefone_fixo.inputElement.focus();
            return false;
        }

        if (!contato.endereco_principal.getValue()) {

            alert(_M(MENSAGENS_MANUTENCAO_CGS + 'contato_endereco_deve_ser_informado'));

            contato.endereco_principal.botaoLancar.focus();
            return false;
        }

        return true;
    });

    /****************************************************
     * ************** CONTROLES DE EVENTOS **************
     * **************************************************
     */
    $('desconheceMae').observe('click', function () {
        bloqueiaNome(this);
    });

    $('desconhecePai').observe('click', function () {
        bloqueiaNome(this);
    });

    $('nacionalidade').observe('change', function () {
        validaNacionalidade();
    });

    $('racaCor').observe('change', function () {
        validaRaca();
    });

    $('cns').oninput = function () {
        js_ValidaCampos(this, 1, 'CNS', true, 't');
    };

    $('nome').oninput = function () {
        js_ValidaCampos(this, 2, 'Nome', true, 't');
    };

    $('nomeSocial').oninput = function () {
        js_ValidaCampos(this, 2, 'Nome', true, 't');
    };

    $('nomeMae').oninput = function () {
        js_ValidaCampos(this, 2, 'Nome da Mãe', true, 't');
    };

    $('nomePai').oninput = function () {
        js_ValidaCampos(this, 2, 'Nome do Pai', true, 't');
    };

    informacaoObito.addEventListener('change', function () {
        controlarCadastroInativo();
    });


    /**
     * Quando a tela for carregada preencherá os dados na tela
     */
    function carregaDadosPessoais() {
        callbackCarregamento.dadosPessoais = function (dados, informacoesPadrao, dados_contato) {
            /**
             * Preenche "País de Origem"
             */
            $('paisOrigem').length = 0;

            informacoesPadrao.paisOrigem.each(function (oPais) {
                $('paisOrigem').add(new Option(oPais.label_pais, oPais.codigo_pais));
            });

            $('paisOrigem').value = 10;

            /**
             * Preenche "Distritos Sanitários"
             */

            $('distritoSanitario').add(new Option('', ''));

            if (informacoesPadrao.distritosSanitarios.length > 0) {
                for (let distrito of informacoesPadrao.distritosSanitarios) {
                    $('distritoSanitario').add(new Option(distrito.descricao, distrito.codigo));
                }
            }

            if (!dados) {
                return false;
            }

            dadosPessoais.cadastroInativo.checked = dados.cadastroInativo == 't';
            dadosPessoais.cgsMunicipio.setValue(dados.cgsMunicipio);

            /**
             * Dados do Usuário
             */
            dadosPessoais.cns.setValue(dados.cns);
            dadosPessoais.nome.setValue(dados.nome);
            dadosPessoais.nomeSocial.setValue(dados.nome_social);
            dadosPessoais.sexo.setValue(dados.sexo);
            dadosPessoais.racaCor.setValue(dados.raca);
            dadosPessoais.codigo_etnia.setValue(dados.codigo_etnia);
            dadosPessoais.label_etnia.setValue(dados.label_etnia);
            dadosPessoais.fatorRH.setValue(dados.fator_rh);
            dadosPessoais.tipoSangue.setValue(dados.tipo_sanguineo);

            /**
             * Dados de Nascimento
             */
            dadosPessoais.dataNascimento.setValue(dados.data_nascimento);
            dadosPessoais.nacionalidade.setValue(dados.nacionalidade);
            dadosPessoais.paisOrigem.setValue(dados.paisOrigem);
            dadosPessoais.municipioNascimento.setValue(dados.municipio_nascimento);
            dadosPessoais.ufNascimento.setValue(dados.uf_nascimento);
            dadosPessoais.codigoIbge.setValue(dados.codigo_ibge_nascimento);

            dadosPessoais.nomeMae.setValue(dados.nome_mae);
            dadosPessoais.nomePai.setValue(dados.nome_pai);
            dadosPessoais.cpf.setValue(dados.cpf);
            dadosPessoais.rg.setValue(dados.rg);
            dadosPessoais.distritoSanitario.setValue(dados.distritoSanitario);

            if (dados_contato) {
                contato.email.setValue(dados_contato.email);

                telefone = dados_contato.telefone_celular;
                if (telefone) {
                    telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 7) + '-' + telefone.slice(7, 11);
                    dados_contato.telefone_celular = telefone;
                }

                telefone = dados_contato.telefone_fixo;
                if (telefone) {
                    telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 6) + '-' + telefone.slice(6, 10);
                    dados_contato.telefone_fixo = telefone;
                }

                telefone = dados_contato.fax;
                if (telefone) {
                    telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 6) + '-' + telefone.slice(6, 10);
                    dados_contato.fax = telefone;
                }

                contato.telefone_fixo.setValue(dados_contato.telefone_fixo);
                contato.telefone_celular.setValue(dados_contato.telefone_celular);
                contato.fax.setValue(dados_contato.fax);

                contato.endereco_principal.setValue(dados_contato.endereco);
            }

            if ($("desconheceMae").checked = dados.nome_mae == "SEM INFORMAÇÃO") {
                bloqueiaNome($("desconheceMae"));
            }

            if ($("desconhecePai").checked = dados.nome_pai == "SEM INFORMAÇÃO") {
                bloqueiaNome($("desconhecePai"));
            }
            /**
             * Dados do Óbito
             */
            dadosPessoais.dataObito.setValue(dados.data_obito);
            dadosPessoais.informacaoObito.setValue(dados.informacao_obito == 't' ? "sim":"nao");
            
            if(dados.informacao_obito == 't'){
                controlarCadastroInativo();
            }
        
            /**
             * Dados do sistema
             */
            dadosPessoais.dataAlteracao.setValue(dados.data_alteracao);
            dadosPessoais.dataCadastro.setValue(dados.data_cadastro);
            dadosPessoais.unidadeOrigem.setValue(dados.unidade_origem);            

            iCodigoCartaoSus = dados.codigo_cartao_sus;
            $('codigoCgs').innerHTML = ' - CGS: ' + iCgs;

            validaNacionalidade();
            validaRaca();
            existeCgsUnd();
        };
    }

    document.addEventListener("DOMContentLoaded", function (event) {
        carregaDadosPessoais();
    });

    $('btn-consulta-cns').addEventListener('click', function () {
        new AjaxRequest(
            'sau4_cgs.RPC.php',
            {
                'sExecucao': 'buscarNumeroCgsPorCns',
                'cns': $('cns').value
            },
            function (retorno, erro) {
                if (erro) {
                    alert(retorno.sMessage.urlDecode());
                    return false;
                }

                window.location.href = `sau1_manutencaocgs001.php?cgs=${retorno.numeroCgs}`;
            }
        ).execute();
    });

    $('cpf').addEventListener('change', function () {
        if ($('cpf').value.length == 11) {
            $('cpf').value = $('cpf').value.replaceAll('-', '');
            $('cpf').value = $('cpf').value.replaceAll('.', '');
            $('cpf').value = $('cpf').value.replaceAll('_', '');
            $('cpf').value = $('cpf').value.substr(0, 3) + "." + $('cpf').value.substr(3, 3) + "." + $('cpf').value.substr(6, 3) + "-" + $('cpf').value.substr(9, 2);

            if ($('cpf').value != "" && $('cpf').value.length < 14) {
                document.hasFocus();
                alert("CPF inválido");
                $('cpf').focus();
                return false;
            }
        }
    });

    $('btn-consulta-cpf').addEventListener('click', function () {
        dadosPessoais.cpf = $('cpf');
        cpfSemCaractere = dadosPessoais.cpf.value;
        if (cpfSemCaractere == "") {
            return;
        }

        if (dadosPessoais.cpf.value) {
            cpfSemCaractere = cpfSemCaractere.replace('-', '');
            cpfSemCaractere = cpfSemCaractere.replaceAll('.', '');
            cpfSemCaractere = cpfSemCaractere.replaceAll('_', '');
            new AjaxRequest(
                'sau4_cgs.RPC.php',
                {
                    'sExecucao': 'buscarNumeroCgsPorCpf',
                    'cpf': cpfSemCaractere
                },
                function (retorno, erro) {
                    if (erro) {
                        alert(retorno.sMessage.urlDecode());
                        return false;
                    }

                    window.location.href = `sau1_manutencaocgs001.php?cgs=${retorno.numeroCgs}`;
                }
            ).execute();
        }
    });

    function mascara(objeto, isCelular = false) {
        if (event.charCode >= 48 && event.charCode <= 57) {
            if (objeto.value.length == 0 || objeto.value.length == 1)
                objeto.value = '(' + objeto.value;

            if (objeto.value.length == 3)
                objeto.value = objeto.value + ')';

            if (objeto.value.length == 4)
                objeto.value = objeto.value + ' ';

            if (objeto.value.length == 10 && isCelular) {
                objeto.value = objeto.value + '-';
            } else if (objeto.value.length == 9 && isCelular == false) {
                objeto.value = objeto.value + '-';
            }
        } else {
            event.preventDefault();
            return false;
        }
    }

    function mascaraPaste(objeto, isCelular = false) {

        var telefone = event.clipboardData.getData('Text');
        telefone = telefone.replace('-', '');
        telefone = telefone.replace(' ', '');
        telefone = telefone.replace('(', '');
        telefone = telefone.replace(')', '');
        if (isCelular) {
            telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 7) + '-' + telefone.slice(7, 11);
        } else {
            telefone = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 6) + '-' + telefone.slice(6, 10);
        }
        objeto.value = telefone;
    }

    function retornaIdadePaciente(dados) {
        let idadeCompleta = dados.oCgs.sIdadeCompleta.urlDecode();
        $('linhaDaIdadeCompleta').innerHTML = `<p style="display: inline-block">Idade: <span style="color: red;">${idadeCompleta}</span></p>`;
    }

    function existeCgsUnd() {
        let cgsUnd = iCgs;

        if (cgsUnd != null) {
            let diretrizes = {'sExecucao': 'buscarDadosCgs', 'iCgs': cgsUnd}

            let oAjaxRequest = new AjaxRequest('sau4_cgs.RPC.php', diretrizes, retornaIdadePaciente);
            oAjaxRequest.execute();
        }
    }

    function validaRg() {
        let campoRg = document.getElementById('rg');

        campoRg.addEventListener('keyup', function () {
            if (isNaN(campoRg.value)) {
                campoRg.value = '';
                alert('O RG deve ser preenchido apenas com números!');
            }
        })
    }

    function controlarCadastroInativo(){
        let cadastroInativo = document.getElementById('cadastroInativo');
        let mensagemBloqueioInativo = document.getElementById('mensagemBloqueioInativo');
        let informacaoObito = document.getElementById('informacaoObito');

        if (informacaoObito.value == 'sim') {            
            trDataObito.removeAttribute('style');
            mensagemBloqueioInativo.style.display = 'inline';
            cadastroInativo.checked = true; 
            cadastroInativo.disabled = true;            
        } else {
            oInputDataObito.value = "";
            trDataObito.setAttribute('style', 'display:none');
            mensagemBloqueioInativo.style.display = 'none';
            cadastroInativo.checked = true; 
            cadastroInativo.disabled = false;
        }
        
    }
    
    validaRg();

</script>
