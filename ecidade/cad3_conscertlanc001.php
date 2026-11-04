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
require_once modification("libs/db_utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_app.utils.php");

$clrotulo = new rotulocampo;

$clrotulo->label('k155_sequencial');
$clrotulo->label('k155_descricao');
$clrotulo->label('k156_observacao');
$clrotulo->label('k125_valor');
$clrotulo->label('k125_datalanc');
$clrotulo->label('p58_codproc');
$clrotulo->label('p58_numero');
$clrotulo->label('k160_abatimento');
$clrotulo->label('k160_data');
$clrotulo->label('k160_nometitular');
$clrotulo->label('k160_numeroprocesso');
$clrotulo->label('k160_sequencial');

$oDaoCertLancImov = new cl_certlancimov();
$certidoesMatricula = [];
$anoSessao = db_getsession("DB_anousu");

if (isset($_GET['matricula'])) {
    $sSqlCertidoes = $oDaoCertLancImov->sql_certidoes_matricula($_GET['matricula']);
    $rsCertidoes = db_query($sSqlCertidoes);
    $certidoesMatricula = db_utils::getCollectionByRecord($rsCertidoes);
}

?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <?php
    db_app::load("scripts.js, prototype.js, strings.js, estilos.css");
    ?>
</head>

<style>
    .hiddenComponent {
        display: none;
    }
</style>

<body>
    <fieldset style="margin: auto; width: fit-content; margin-top: 15px;">
        <legend>Certidão de Lançamento</legend>
        <form method="POST" action="cad3_conscertlanc002.php">
            <table>

                <tr>
                    <td>
                        <label>Processo do Sistema:</label>
                    </td>
                    <td>
                        <select name='processoDoSistema' id="processoDoSistema">
                            <option selected value='0'>Selecione</option>
                            <option value='1'>Sim</option>
                            <option value='2'>Não</option>
                        </select>
                    </td>
                </tr>

                <tr id="linhaNumeroProcesso">
                    <td>
                        <label><?= $Lk160_numeroprocesso ?></label>
                    </td>
                    <td>
                        <input name='numeroProcesso' id='numeroProcesso' type="number">
                    </td>
                </tr>

                <tr id="linhaTitularProcesso">
                    <td>
                        <label><?= $Lk160_nometitular ?></label>
                    </td>
                    <td>
                        <input name='titularProcesso' id="titularProcesso" type="text">
                    </td>
                </tr>

                <tr id="linhaDataProcesso">
                    <td>
                        <label><?= $Lk160_data ?></label>
                    </td>
                    <td>
                        <input name='dataProcesso' id="dataProcesso" type="date">
                    </td>
                </tr>

                <tr id="linhaProcessoSistemaInterno">
                    <td colspan="2">

                        <div>

                            <fieldset>
                                <legend><strong>Dados do Processo</strong></legend>

                                <table align="center">
                                    <tr>
                                        <td title="<?= @$Tp58_numero ?>">
                                            <?php
                                            db_ancora($Lp58_numero, 'js_pesquisaProcesso(true)', 1)
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input('p58_numero', 10, '', true, 'text', $db_opcao, "onchange='js_pesquisaProcesso(false)'", '', '', '', 20);
                                            db_input('z01_nome', 40, isset($Iz01_nome) ? $Iz01_nome : '', true, 'text', 3, "", 'z01_nomeprocesso');
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td title="Ano">Ano</td>
                                        <td>
                                            <input type="number" id="anoSessao" name="anoSessao" value="<?=$anoSessao?>" style="width: 82px;" onchange='js_pesquisaProcesso(false)'>
                                        </td>
                                    </tr>
                                    <input type="hidden" id='p58_codproc' name='p58_codproc' value=''>
                                    <input type="hidden" id='numeroProcessoConcatenado' name='numeroProcessoConcatenado' value=''>
                                </table>

                            </fieldset>

                        </div>

                    </td>
                </tr>

                <tr>
                    <td>
                        <label style="margin-top: 0;"><?= $Lk156_observacao ?></label>
                    </td>
                    <td>
                        <textarea name='observacao' cols="60" rows="5"></textarea>
                    </td>
                </tr>
            </table>

            <div style="width: 100%; display:flex; justify-content:center; margin-top: 10px;">
                <input id='botaoProcessar' type="button" value="Processar">
            </div>

            <input name="matricula" type="hidden" value="<?= $_GET['matricula'] ?>">
        </form>
    </fieldset>

    <?php if (count($certidoesMatricula) > 0) { ?>
        <fieldset style="margin: auto; width: fit-content; margin-top: 15px;">
            <legend>Certidões de Lançamento geradas:</legend>

            <table class="form-container" border="1" style="max-width: 900px;">
                <thead>
                    <tr style="background-color: #e1e1e1">
                        <td class="text-center">Certidão</td>
                        <td class="text-center">Data/Hora de emissão</td>
                        <td class="text-center">Processo</td>
                        <td class="text-center">Usuário</td>
                        <td class="text-center">Titular</td>
                        <td class="text-center">Observação</td>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($certidoesMatricula as $certidao) {

                        $dataEmissao = (new Datetime($certidao->j176_emissao))->format('d/m/Y');

                        echo "
                        <tr>
                            <td class='text-center field-size3'>{$certidao->j176_sequencial}</td>
                            <td class='text-center field-size4'>{$dataEmissao}</td>
                            <td class='text-center field-size3'>{$certidao->j176_processo}</td>
                            <td class='text-center field-size7'>{$certidao->usuario}</td>
                            <td class='text-center field-size7'>{$certidao->j176_titular}</td>
                            <td class='text-left field-size7'>{$certidao->j176_observacao}</td>
                        </tr>
                    ";
                    }
                    ?>
                </tbody>
            </table>
        </fieldset>
    <?php } ?>
</body>

<script>
    const selectProcessoDoSistema = document.getElementById('processoDoSistema');
    const linhaNumeroProcesso = document.getElementById('linhaNumeroProcesso');
    const linhaTitularProcesso = document.getElementById('linhaTitularProcesso');
    const linhaDataProcesso = document.getElementById('linhaDataProcesso');
    const linhaProcessoSistemaInterno = document.getElementById('linhaProcessoSistemaInterno');
    const botaoProcessar = document.getElementById('botaoProcessar')
    const nomeProcesso = document.getElementById('z01_nomeprocesso');
    const codigoProcesso = document.getElementById('p58_codproc');
    const numeroProcesso = document.getElementById('p58_numero');
    const numeroProcessoConcatenado = document.getElementById("numeroProcessoConcatenado");
    const anoSessao = document.getElementById("anoSessao");

    function toggleTipoDeProcesso(event) {
        switchTipoDeProcesso(Number(event.target.value));
    }

    function switchTipoDeProcesso(option) {
        switch (Number(option)) {
            case (1):
                selectProcessoDoSistema.value = '1';
                linhaNumeroProcesso.classList.add('hiddenComponent');
                linhaTitularProcesso.classList.add('hiddenComponent');
                linhaDataProcesso.classList.add('hiddenComponent');
                linhaProcessoSistemaInterno.classList.remove('hiddenComponent');
                break;

            case (2):
                selectProcessoDoSistema.value = '2';
                linhaNumeroProcesso.classList.remove('hiddenComponent');
                linhaTitularProcesso.classList.remove('hiddenComponent');
                linhaDataProcesso.classList.remove('hiddenComponent');
                linhaProcessoSistemaInterno.classList.add('hiddenComponent');
                break;
        }
    }

    function js_pesquisaProcesso(mostra) {
        if (numeroProcesso.value != '' && anoSessao.value != '') {
            numeroProcesso.value = numeroProcesso.value.split('/')[0];
            numeroProcessoConcatenado.value = numeroProcesso.value + '/' + anoSessao.value;
            
            if (mostra == true) {
                js_OpenJanelaIframe('this', 'db_iframe_nomes', 'func_protprocesso_isencao.php?funcao_js=parent.js_mostraProcesso|p58_codproc|z01_nome|p58_numero', 'Pesquisa', true, '0');
            } else {
                if (numeroProcesso.value != '') {
                    js_OpenJanelaIframe('this', 'db_iframe_nomes', 'func_protprocesso_isencao.php?pesquisa_chave=' + numeroProcessoConcatenado.value + '&funcao_js=parent.js_mostraProcessoHide&sCampoPesquisa=p58_codproc', 'Pesquisa', false, '0');
                } else {
                    nomeProcesso.value = '';
                }
            }
        }
    }

    function js_mostraProcessoHide(chave, chave1, erro) {
        codigoProcesso.value = chave;
        nomeProcesso.value = chave1;
        if (erro == true) {
            numeroProcesso.focus();
            numeroProcesso.value = '';
            codigoProcesso.value = '';
        }

    }

    function js_mostraProcesso(chave1, chave2, chave3) {
        codigoProcesso.value = chave1;
        nomeProcesso.value = chave2;
        numeroProcesso.value = chave3.split('/')[0]
        db_iframe_nomes.hide();
    }

    function js_processaCertidao() {
        numeroProcesso.value = p58_codproc.value
        botaoProcessar.type = 'submit'
        botaoProcessar.click();
    }

    window.onload = function() {
        selectProcessoDoSistema.addEventListener('change', toggleTipoDeProcesso);
        botaoProcessar.addEventListener('click', js_processaCertidao);
        switchTipoDeProcesso(1);
    }
</script>

</html>