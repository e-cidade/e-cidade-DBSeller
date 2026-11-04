<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

//MODULO: pessoal
$clrhfuncao->rotulo->label();

if ($db_opcao == 2) {
    $sTituloFormulario = "Alteração";
} else if ($db_opcao == 3) {
    $sTituloFormulario = "Exclusão";
} else {
    $sTituloFormulario = "Cadastro";
}
?>
<div class="container">
    <form name="form1" method="post" action="">

        <fieldset style="width: 500px;">
            <legend style="font-weight: bold;">&nbsp;<?= $sTituloFormulario; ?> de Cargos&nbsp;</legend>
            <fieldset style="border:none;">
                <table class="form-container">
                    <tr>
                        <td width="100" nowrap title="<?= @$Trh37_funcao ?>"><?= @$Lrh37_funcao ?></td>
                        <td>
                            <?php
                            db_input('rh37_funcao', 10, $Irh37_funcao, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= $Trh37_descr ?>">
                            <label for="rh37_descr"><?= $Lrh37_descr ?></label>
                        </td>
                        <td>
                            <?php
                            db_input('rh37_descr', 44, $Irh37_descr, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= $Trh37_descricaocompleta ?>">
                            <label for="rh37_descricaocompleta"><?= $Lrh37_descricaocompleta ?></label>
                        </td>
                        <td>
                            <?php
                            db_input('rh37_descricaocompleta', 44, $Irh37_descricaocompleta, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= $Trh37_rhinstrucao ?>">
                            <label for="rh37_rhinstrucao"><?= $Lrh37_rhinstrucao; ?></label>
                        </td>
                        <td nowrap>
                            <?php
                            $result_instru = $clrhinstrucao->sql_record($clrhinstrucao->sql_query_file());
                            db_selectrecord("rh37_rhinstrucao", $result_instru, "", $db_opcao, "", "", "", "", "", 1);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Trh37_funcaogrupo ?>">
                            <?php
                            db_ancora($Lrh37_funcaogrupo, 'js_buscagrupo(true);', 1);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('rh37_funcaogrupo', 10, $Irh37_funcaogrupo, true, 'text', $db_opcao, "onchange='js_buscagrupo(false);'");
                            db_input('rh37_funcaogrupodescr', 30, '', true, 'text', 3, '');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Trh37_vagas ?>"><?= @$Lrh37_vagas ?></td>
                        <td>
                            <?php
                            db_input('rh37_vagas', 10, $Irh37_vagas, true, 'text', $db_opcao, "")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Trh37_cbo ?>"><?= @$Lrh37_cbo ?></td>
                        <td>
                            <?php
                            db_input('rh37_cbo', 10, $Irh37_cbo, true, 'text', $db_opcao, "")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Trh37_class ?>"><?= @$Lrh37_class ?></td>
                        <td>
                            <?php
                            db_input('rh37_class', 10, $Irh37_class, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>

 <tr>
                        <td nowrap title="<?= @$Trh37_acumcargo ?>"><?= @$Lrh37_acumcargo ?></td>
                        <td>
                            <?php
                            $aAtivox = array("" => "Nenhuma das opções","true" => "Sim", "false" => "Não");
                            db_select('rh37_acumcargo', $aAtivox, true, $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Trh37_ativo ?>"><?= @$Lrh37_ativo ?></td>
                        <td>
                            <?php
                            $aAtivo = array("t" => "Sim", "f" => "Não");
                            db_select('rh37_ativo', $aAtivo, true, $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <?php if(isParaiba()): ?>
                    <tr>
                        <td> Tipo Cargo: </td>
                        <td>
                        <?php 
                        $tiposCargo = array(
                            "0" => "Inativos / Pensionistas",
                            "1" => "Efetivos",
                            "2" => "Eletivos",
                            "3" => "Cargo comissionado",
                            "4" => "Função de confiança", 
                            "5" => "Contratação por excepcional interesse público",
                            "6" => "Emprego público",
                            "7" => "Benefício previdenciário temporário",
                            "8" => "À disposição",
                            "" => "Não informado"
                        );
                        db_select('rh267_dados', $tiposCargo, true, $db_opcao);?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                <fieldset>
                    <legend style="font-weight: bold;">&nbsp;Lei&nbsp;</legend>
                    <?php
                    db_textarea('rh37_lei', 5, 60, $Irh37_lei, true, 'text', $db_opcao, "");
                    ?>
                </fieldset>

            </fieldset>
            <fieldset class="separator">
                <legend>Dados eSocial</legend>
                <table class="form-container">
                    <tr>
                        <td nowrap title="Data de início da validade das informações para o eSocial.">
                            <label class="bold" for="rh37_datainicial" id="lbl_rh37_datainicial">Inicio de validade:</label>
                        </td>
                        <td>
                            <?php db_inputdata(
                                'rh37_datainicial',
                                @$rh37_datainicial_dia,
                                @$rh37_datainicial_mes,
                                @$rh37_datainicial_ano,
                                true,
                                'text',
                                $db_opcao,
                                ""
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="Data de final da validade das informações para o eSocial.">
                            <label class="bold" for="rh37_datafinal" id="lbl_rh37_datafinal">Fim de validade:</label>
                        </td>
                        <td>
                            <?php db_inputdata(
                                'rh37_datafinal',
                                @$rh37_datafinal_dia,
                                @$rh37_datafinal_mes,
                                @$rh37_datafinal_ano,
                                true,
                                'text',
                                $db_opcao,
                                ""
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <fieldset>
                          <legend>Descrição das atividades desempenhadas: </legend>
                          <?php
                            db_textarea('rh37_descricaoatividades', 3,55,0,true,'text',$db_opcao);
                          ?>
                        </fieldset>
                      </td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>

        <input onclick="return verificaDados()"
               name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
               type="submit"
               id="db_opcao"
               value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
               <?= ($db_botao == false ? "disabled" : "") ?> >

        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
    </form>
</div>

<script>
function verificaDados() {
    const inputDataInicial = document.querySelector("input[name=rh37_datainicial]");
    const inputDataFinal = document.querySelector("input[name=rh37_datafinal]");

    if (inputDataInicial.value || inputDataFinal.value) {

        if (!inputDataInicial.value) {
            alert("É necessário preencher a Data de Inicio de validade das informações no eSocial.");
            return false;
        }

        if (inputDataFinal.value) {
            const dataInicial = Date.convertFrom(inputDataInicial.value, DATA_PTBR);
            const dataFinal = Date.convertFrom(inputDataFinal.value, DATA_PTBR);

            if (dataInicial.compararData(dataFinal, COMPARACAO_MAIOR)) {
                alert("Data de Inicio de validade deve ser menor que a Data de Fim de validade.");
                return false;
            }
        }
    }
    return true;
}

function js_buscagrupo(mostra) {
    if (mostra == true) {
        var sUrlOpen = 'func_rhfuncaogrupo.php?funcao_js=parent.js_preencheGrupo|rh100_sequencial|rh100_descricao';
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhfuncao', sUrlOpen, 'Pesquisa', true);
    } else {
        if (document.form1.rh37_funcaogrupo.value != '') {
            var iFuncaoGrupo = document.form1.rh37_funcaogrupo.value;
            var sUrlOpenGrupo = 'func_rhfuncaogrupo.php?pesquisa_chave=' + iFuncaoGrupo + '&funcao_js=parent.js_mostrargrupos';
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhfuncao', sUrlOpenGrupo, 'Pesquisa', false);
        } else {
            document.form1.rh37_funcaogrupodescr.value = '';
        }
    }
}

function js_mostrargrupos(chave, erro) {
    document.form1.rh37_funcaogrupodescr.value = chave;
    if (erro == true) {
        document.form1.rh37_funcaogrupo.focus();
        document.form1.rh37_funcaogrupo.value = '';
    }
}

function js_preencheGrupo(chave, descricao) {
    document.form1.rh37_funcaogrupo.value = chave;
    document.form1.rh37_funcaogrupodescr.value = descricao;
    db_iframe_rhfuncao.hide();
}


function js_pesquisa() {
    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhfuncao', 'func_rhfuncao.php?funcao_js=parent.js_preenchepesquisa|rh37_funcao', 'Pesquisa', true);
}

function js_preenchepesquisa(chave) {
    db_iframe_rhfuncao.hide();
    <?php
    if ($db_opcao != 1) {
        echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
    }
    ?>
}
</script>
