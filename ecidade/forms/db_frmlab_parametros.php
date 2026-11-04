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

use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Enum\ModeloImpressao;

//MODULO: TFD
$cllab_parametros->rotulo->label();
$disabled = "";
$nameBotao = 'incluir';
$acaoBotao = 'Incluir';

if ($db_opcao == 2 || $db_opcao == 22) {
    $nameBotao = 'alterar';
    $acaoBotao = 'Alterar';
}

if ($db_opcao == 3 || $db_opcao == 33) {
    $nameBotao = 'excluir';
    $acaoBotao = 'Excluir';
}

if ($db_botao == false) {
    $disabled = "disabled='disabled'";
}

$tamanhoFonte = $la49_tamanhofonte;
$modelos = ModeloImpressao::getCodigosDescricoes();
?>
<div class="container">
    <form name="form1" method="post" action="">
        <fieldset>
            <legend>Parâmetros</legend>
            <table>
                <tr style="display: none;">
                    <td nowrap title="<?= $Tla49_i_codigo ?>">
                        <?= $Lla49_i_codigo ?>
                    </td>
                    <td>
                        <?php
                        db_input('la49_i_codigo', 10, $Ila49_i_codigo, true, 'text', 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $Tla49_c_estrutural ?>">
                        <?= $Lla49_c_estrutural ?>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'la49_c_estrutural',
                            40,
                            $Ila49_c_estrutural,
                            true,
                            'text',
                            $db_opcao1,
                            "onchange='js_laboratorio();'"
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $Tla49_i_exameduplo ?>">
                        <?= $Lla49_i_exameduplo ?>
                    </td>
                    <td>
                        <?php
                        $aX = array('2' => 'NÃO', '1' => 'SIM');
                        db_select('la49_i_exameduplo', $aX, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Tla49_modelocoletaamostra ?>">
                        <?= $Lla49_modelocoletaamostra ?>
                    </td>
                    <td>
                        <?php
                        db_select('la49_modelocoletaamostra', $modelos, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Tla49_identificaprioridade ?>">
                        <?= $Lla49_identificaprioridade ?>
                    </td>
                    <td>
                        <?php
                        $opcoes = array('f' => 'NÃO', 't' => 'SIM');
                        db_select('la49_identificaprioridade', $opcoes, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Tla49_habilitarabsurdo ?>">
                        <?= $Lla49_habilitarabsurdo ?>
                    </td>
                    <td>
                        <?php
                        $habilitado = array('f' => 'NÃO', 't' => 'SIM');
                        db_select('la49_habilitarabsurdo', $habilitado, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr id="filtroModeloComprovanTeRequisicao">
                    <td>
                        <label class="bold">Modelo da Requisição de Exames:</label>
                    </td>
                    <td>
                        <select id="tipoModeloComprovanTeRequisicao" name="la49_modelocomprovanterequisicao">
                            <option value="0">MODELO 1</option>
                            <option value="1">MODELO 2</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Tla49_autorizarexamesaoconfirmar ?>">
                        <?= $Lla49_autorizarexamesaoconfirmar ?>
                    </td>
                    <td>
                        <?php
                        $autorizarExamesAoConfirmar = array('f' => 'Não', 't' => 'Sim');
                        db_select('la49_autorizarexamesaoconfirmar', $autorizarExamesAoConfirmar, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Tla49_numerocontroleinterno ?>">
                        <?= $Lla49_numerocontroleinterno ?>
                    </td>
                    <td>
                        <?php
                        $habilitarNumeroControleInterno = array('f' => 'Não', 't' => 'Sim');
                        db_select('la49_numerocontroleinterno', $habilitarNumeroControleInterno, true, $db_opcao);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= $Tla49_habilitargrupo ?>">
                        <?= $Lla49_habilitargrupo ?>
                    </td>
                    <td>
                        <?php
                        $habilitarGrupoExames = array('f' => 'Não', 't' => 'Sim');
                        db_select('la49_habilitargrupo', $habilitarGrupoExames, true, $db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= $Tla49_habilitartriagemlaboratorial ?>">
                        <?= $Lla49_habilitartriagemlaboratorial ?>
                    </td>
                    <td>
                        <?php
                        $habilitarGrupoExames = array('f' => 'Não', 't' => 'Sim');
                        db_select('la49_habilitartriagemlaboratorial', $habilitarGrupoExames, true, $db_opcao,"onchange='verificaTriagemLaboratorial()'");
                        ?>
                    </td>
                </tr>l
                <tr id="trUnidadesDispensadas" style="text-align:right;display:none">
                    <td nowrap title="<?= $Tla49_unidadesdispensadastriagem ?>">
                       <?= $Lla49_unidadesdispensadastriagem ?>
                    </td>
                    <td>
                        <?php
                        db_input(
                            'la49_unidadesdispensadastriagem',
                            40,
                            3,
                            true,
                            'text',
                            1,
                            "",
                            "",
                            "",
                            "",
                            255
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset id="separadorLaudo" class="separator">
                            <legend>Laudo do Exame</legend>
                            <table>
                                <tr>
                                    <td nowrap title="<?= $Tla49_modelolaudo ?>">
                                        <?= $Lla49_modelolaudo ?>
                                    </td>
                                    <td>
                                        <?php
                                        $modelosLaudo = array('1' => 'Modelo 1', '2' => 'Modelo 2', '3' => 'Modelo 3');
                                        db_select(
                                            'la49_modelolaudo',
                                            $modelosLaudo,
                                            true,
                                            $db_opcao,
                                            "onchange='js_definicoesLaudo()'"
                                        );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td nowrap title="<?= $Tla49_exibeliberacaoporexame ?>">
                                        <?= $Lla49_exibeliberacaoporexame ?>
                                    </td>
                                    <td>
                                        <?php
                                        $habilitado = array('t' => 'SIM', 'f' => 'NÃO');
                                        db_select('la49_exibeliberacaoporexame', $habilitado, true, $db_opcao);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label title="<?= $Tla49_espacolinhas ?>"><b>Espaço entre linhas:</b></label>
                                    </td>
                                    <td>
                                        <?php
                                        $opcoesEspacoLinha = [
                                            '0' => 'Sem espaçamento',
                                            '1' => '1',
                                            '1.5' => '1,5',
                                            '2' => '2',
                                            '2.5' => '2,5'
                                        ];
                                        db_select('la49_espacolinhas', $opcoesEspacoLinha, true, $db_opcao);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label title="<?= $Tla49_tamanhofonte ?>">
                                            <b>Tamanho da fonte:</b>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        $la49_tamanhofonte = empty($la49_tamanhofonte) ? '7' : $la49_tamanhofonte;

                                        $maxTamanhoFonte = 14;
                                        $opcoesFonte = [];
                                        for ($i = 6; $i <= $maxTamanhoFonte; $i++) {
                                            $opcoesFonte[$i] = $i;
                                        }
                                        db_select('la49_tamanhofonte', $opcoesFonte, true, $db_opcao);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <td colspan='2'>
                        <fieldset id="filtroIntegracao" class="separator">
                            <legend>Integração Luckmann</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label class="bold">Tipo: </label>
                                    </td>
                                    <td>
                                        <select id="tipoIntegracao" name="la49_integracao">
                                            <option value="0">Sem Integração</option>
                                            <option value="1">Luckmann</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: none" id="filtroLuckman" colspan='2'>
                                        <fieldset>
                                            <legend>Luckmann</legend>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <label class="bold" for="caminhoPedidos">Pasta Arquivos
                                                            Pedidos: </label>
                                                        <input type="text"
                                                               id="caminhoPedidos"
                                                               name="caminhoPedidos"
                                                               value="<?= $caminhoPedidos ?>"/>
                                                    </td>
                                                <tr>
                                                    <td>
                                                        <label class="bold" for="caminhoResultados">Pasta Arquivos
                                                            Resultados: </label>
                                                        <input type="text"
                                                               id="caminhoResultados"
                                                               name="caminhoResultados"
                                                               value="<?= $caminhoResultados ?>"/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <td colspan='2'>
                        <fieldset id="filtroIntegracaoInfinity" class="separator">
                            <legend>Integração Infinity</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label class="bold">Tipo: </label>
                                    </td>
                                    <td>
                                        <select id="tipoIntegracaoInfinity" name="la49_integracaoinfinity">
                                            <option value="0">Sem Integração</option>
                                            <option value="1">Infinity</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr style="display: none" id="filtroInfinity">
                                    <td colspan='2'>
                                        <fieldset>
                                            <legend>Infinity</legend>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <label class="bold" for="caminhoPedidosInfinity">Pasta Arquivos
                                                            Pedidos: </label>
                                                        <input type="text"
                                                               id="caminhoPedidosInfinity"
                                                               name="caminhoPedidosInfinity"
                                                               value=" <?= $caminhoPedidosInfinity ?> "/>
                                                    </td>
                                                <tr>
                                                    <td>
                                                        <label class="bold" for="caminhoResultadosInfinity">Pasta
                                                            Arquivos Resultados: </label>
                                                        <input type="text"
                                                               id="caminhoResultadosInfinity"
                                                               name="caminhoResultadosInfinity"
                                                               value=" <?= $caminhoResultadosInfinity ?>"/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>

            </table>
        </fieldset>
        <input name="<?= $nameBotao ?>" type="submit" id="db_opcao" value="<?= $acaoBotao ?>" <?= $disabled ?>
               onclick='limpaCamposCaminhoArquivos();  limpaCamposCaminhoArquivosInfinity();'>
        <input id='valorIntegracao' value="<?= $la49_integracao ?>" type='hidden'/>
        <input id='valorIntegracaoInfinity' value="<?= ($la49_integracaoinfinity === 't' ? 1 : 0) ?>" type='hidden'/>
        <input id='modelocomprovanterequisicao' value="<?= $la49_modelocomprovanterequisicao ?>" type='hidden'/>
    </form>
</div>
<script>
    var condicao
    new DBToogle('filtroIntegracao', condicao)
    new DBToogle('separadorLaudo', true);

    let condicaoInfinity;
    let toogleInfinity = new DBToogle('filtroIntegracaoInfinity', condicaoInfinity);

    function js_laboratorio() {
        lFlag = false;
        iNivel = 1;
        str = document.form1.la49_c_estrutural.value;

        if (str == '') {
            alert(" Preencha um estrutural");
            return false;

        }
        if (str[0] == '.') {
            alert("nao pode comecar");
            return false;
        }
        if (str[str.length - 1] == '.') {
            alert("nao pode comecar 00");
            return false;
        }
        for (i = 0; i < str.length; i++) {
            if (str[i] != '0' && str[i] != ".") {
                alert("Digite um estrutural válido" + str[i]);
                return false;
            }
            if (str[i] == '.') {
                if (lFlag) {
                    alert("pontos");
                }
                lFlag = true;
                iNivel++;
                continue;
            }
            lFlag = false;
        }
        if (iNivel < 2) {
            alert("nivel");
            return false;
        }
        return true;
    }

    function validaTipo() {

        let opcoes = $('tipoIntegracao').options.length;

        for (let contador = 0; contador < opcoes; contador++) {
            if ($('tipoIntegracao').options[contador].value == $F('valorIntegracao')) {
                $('tipoIntegracao').options[contador].setAttribute('selected', 'selected');
            }
        }

        if ($('tipoIntegracao').value === '0') {
            document.getElementById('filtroLuckman').setStyle({
                'display': 'none'
            })
            condicao = false
        }

        if ($('tipoIntegracao').value === '1') {
            document.getElementById('filtroLuckman').setStyle({
                'display': ''
            })
            condicao = true
        }
    }

    function limpaCamposCaminhoArquivos() {
        if ($('tipoIntegracao').value === '0') {
            document.getElementById('caminhoPedidos').value = ''
            document.getElementById('caminhoResultados').value = ''
        }
    }

    function controlaExibicaoModeloReqExames() {
        let optsModeloRequisicao = $('tipoModeloComprovanTeRequisicao').options.length;
        for (let cont = 0; cont < optsModeloRequisicao; cont++) {
            if ($('tipoModeloComprovanTeRequisicao').options[cont].value == $F('modelocomprovanterequisicao')) {
                $('tipoModeloComprovanTeRequisicao').options[cont].setAttribute('selected', 'selected');
            }
        }
    }

    function js_definicoesLaudo() {

        let tamanhoFonte = <?php echo $tamanhoFonte;?>;
        /**
         * Para o modelo 1 se a fonte tiver salva acima de 10,
         * ja atribui-se, automaticamente, para 10, o valor maximo,
         * para esse modelo.
         */
        if ($('la49_modelolaudo').value == '1' && tamanhoFonte > 10) {
            tamanhoFonte = 10;
        }

        $('la49_tamanhofonte').innerHTML = '';

        let maxTamanhoFonte = 14;

        for (let i = 6; i <= maxTamanhoFonte; i++) {

            let option = document.createElement('option');
            option.text = i;
            option.value = i;

            if (i == tamanhoFonte) {
                option.selected = true;
            }

            /**
             * Para o modelo 1 todas as opcoes que vem acima de 10 são desabilitadas
             * para não haver quebra no relatorio
             */
            if ($('la49_modelolaudo').value == '1' && i >= 11) {
                option.disabled = true;
            }

            $('la49_tamanhofonte').appendChild(option);
        }
    }

    validaTipo()
    controlaExibicaoModeloReqExames()
    validaTipoInfinity();
    verificaTriagemLaboratorial();
    $('tipoIntegracao').addEventListener('change', function () {
        validaTipo()
    });

    $('tipoIntegracaoInfinity').addEventListener('change', function () {
        validaTipoInfinity();
    });


    $('la49_c_estrutural').addClassName('field-size-max')
    $('la49_i_exameduplo').addClassName('field-size-max')
    $('la49_habilitarabsurdo').addClassName('field-size-max')
    $('tipoModeloComprovanTeRequisicao').addClassName('field-size-max')
    $('la49_autorizarexamesaoconfirmar').addClassName('field-size-max')
    $('la49_numerocontroleinterno').addClassName('field-size-max')
    $('la49_habilitargrupo').addClassName('field-size-max')
    document.getElementById('caminhoPedidos').addClassName('field-size-max')
    document.getElementById('caminhoResultados').addClassName('field-size-max')
    document.getElementById('la49_modelolaudo').addClassName('field-size-max')
    document.getElementById('la49_exibeliberacaoporexame').addClassName('field-size-max')
    document.getElementById('caminhoPedidosInfinity').addClassName('field-size-max')
    document.getElementById('caminhoResultadosInfinity').addClassName('field-size-max')
    document.getElementById('la49_identificaprioridade').addClassName('field-size-max')
    document.getElementById('la49_habilitartriagemlaboratorial').addClassName('field-size-max')
    document.getElementById('la49_unidadesdispensadastriagem').addClassName('field-size-max')

    function validaTipoInfinity() {

        let opcoes = $('tipoIntegracaoInfinity').options.length;

        for (let contador = 0; contador < opcoes; contador++) {
            if ($('tipoIntegracaoInfinity').options[contador].value == $F('valorIntegracaoInfinity')) {
                $('tipoIntegracaoInfinity').options[contador].setAttribute('selected', 'selected');
            }
        }

        if ($('tipoIntegracaoInfinity').value === '0') {
            document.getElementById('filtroInfinity').setStyle({
                'display': 'none'
            })
            condicaoInfinity = false
        }

        if ($('tipoIntegracaoInfinity').value === '1') {
            document.getElementById('filtroInfinity').setStyle({
                'display': ''
            })
            condicaoInfinity = true
        }
    }

    function limpaCamposCaminhoArquivosInfinity() {
        if ($('tipoIntegracaoInfinity').value === '0') {
            document.getElementById('caminhoPedidosInfinity').value = ''
            document.getElementById('caminhoResultadosInfinity').value = ''
        }
    }

    function verificaTriagemLaboratorial(){        
        document.getElementById('trUnidadesDispensadas').style.display= 'contents';
        if($('la49_habilitartriagemlaboratorial').value == 'f'){
            $('la49_unidadesdispensadastriagem').value = '';
            document.getElementById('trUnidadesDispensadas').style.display= 'none';
        } 
    }

</script>
