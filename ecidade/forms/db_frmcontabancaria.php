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

//MODULO: Configuracoes
$clcontabancaria->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("db89_codagencia");
$clrotulo->label("db83_instit");
?>
<script>
    function js_functionVerificaIdentificador() {

        var iIdentificador = document.getElementById('db83_identificador').value;
        if (iIdentificador.length < 11) {
            alert("Campo identificador(CNPJ) inválido.");
            return false;
        }

        if (document.getElementById('db83_bancoagencia').value.trim() == '') {
            alert("Campo Agência deve ser informado.");
            return false;
        }
        return true;
    }
</script>
<div class="container">
    <fieldset>
        <legend>Cadastro de Conta Bancária</legend>
        <form name="form1" method="post" action="" onsubmit="return js_functionVerificaIdentificador();">
            <table class="form-container">
                <tr>
                    <td nowrap title="<?= @$Tdb83_descricao ?>">
                        <?= @$Ldb83_descricao ?>
                    </td>
                    <td>
                        <?php
                        db_input('db83_sequencial', 10, $Idb83_sequencial, true, 'text', 3, "");
                        db_input('db83_descricao', 50, $Idb83_descricao, true, 'text', $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tdb83_bancoagencia ?>">
                        <?php
                        db_ancora(@$Ldb83_bancoagencia, "js_pesquisadb83_bancoagencia(true);", $db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('db83_bancoagencia', 10, $Idb83_bancoagencia, true, 'text', $db_opcao, " onchange='js_pesquisadb83_bancoagencia(false);'");
                        db_input('db89_codagencia', 10, $Idb89_codagencia, true, 'text', 3, '');
                        db_input('db89_digito', 1, '', true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tdb83_conta ?>">
                        <?= @$Ldb83_conta ?>
                    </td>
                    <td>
                        <?php
                        db_input('db83_conta', 15, $Idb83_conta, true, 'text', $db_opcao, "");
                        db_input('db83_dvconta', 1, $Idb83_dvconta, true, 'text', $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tdb83_identificador ?>">
                        <?= @$Ldb83_identificador ?>
                    </td>
                    <td>
                        <?php
                        db_input('db83_identificador', 15, 1, true, 'text', $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tdb83_codigooperacao ?>">
                        <?= @$Ldb83_codigooperacao ?>
                    </td>
                    <td>
                        <?php
                        db_input('db83_codigooperacao', 4, $Idb83_codigooperacao, true, 'text', $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tdb83_tipoconta ?>">
                        <?= @$Ldb83_tipoconta ?>
                    </td>
                    <td>
                        <?php
                        $x = array('1' => 'Conta Corrente', '2' => 'Conta Poupanca', '3' => 'Conta Aplicacao');
                        db_select('db83_tipoconta', $x, true, $db_opcao, "style='width: 150px;'");
                        ?>
                    </td>
                </tr>
                <?php if (isRioDeJaneiro()) : ?>
                    <tr>
                        <td nowrap title="<?php echo $Tdb83_contaplano ?>">
                            <?php echo $Ldb83_contaplano ?>
                        </td>
                        <td>
                            <?php
                            $aContaPlano = array('t' => 'SIM', 'f' => 'NÃO');
                            db_select('db83_contaplano', $aContaPlano, true, $db_opcao, "style='width: 150px;'");
                            ?>
                        </td>
                        <?php
                        $db83_contaunica = 'f';
                        db_input('db83_contaunica', 1, 0, true, 'hidden', 1);
                        ?>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td nowrap title="<?= @$Tdb83_contaunica ?>">
                            <?= @$Ldb83_contaunica ?>
                        </td>
                        <td>
                            <?php
                            $x = array('f' => 'NÃO', 't' => 'SIM');
                            db_select('db83_contaunica', $x, true, $db_opcao, "style='width: 150px;'");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?php echo $Tdb83_contaplano ?>">
                            <?php echo $Ldb83_contaplano ?>
                        </td>
                        <td>
                            <?php
                            $funcaoOnchange = $db_opcao == 1 ? "onchange='mostrarVinculoConta()';" : '';
                            $aContaPlano = array('t' => 'Sim', 'f' => 'Não');
                            db_select('db83_contaplano', $aContaPlano, true, $db_opcao, "style='width: 150px;' {$funcaoOnchange}");
                            ?>
                        </td>
                    </tr>
                    <tr id="vincularConta" style="display: <?= $db_opcao == 1 ? "" : "none" ?>">
                        <td>
                            <label for="tipo_de_vinculo"><b>Vínculo:</b></label>
                        </td>
                        <td>
                            <?php
                            $tipos = ["0" => "selecione"];
                            $daoBancosTipo = new cl_bancovinculocontatipo();
                            $tipoVinculos = $daoBancosTipo->getAll();
                            foreach ($tipoVinculos as $tipoVinculo) {
                                $tipos[$tipoVinculo->db501_sequencial] = $tipoVinculo->db501_descricao;
                            }
                            db_select("tipo_de_vinculo", $tipos, true, 1, "style='width: 150px;'");
                            ?>
                        </td>
                    </tr>
                    <?php
                endif;
                ?>
                <tr>
                    <td nowrap title="<?php echo $Tdb83_instit ?>">
                        <?php echo $Ldb83_instit ?>
                    </td>
                    <td>
                        <?php
                        $rsInstituicoes = db_query("select codigo, nomeinst from db_config order by nomeinst");
                        $aInstituticoes = array("" => "Selecione a instituição");
                        db_utils::makeCollectionFromRecord($rsInstituicoes, function ($oInstituicao) use (&$aInstituticoes) {
                            $aInstituticoes[$oInstituicao->codigo] = $oInstituicao->nomeinst;
                        });
                        db_select('db83_instit', $aInstituticoes, true, $db_opcao, "style='width: 100%	;'");
                        ?>
                    </td>
                </tr>                
            </table>
    </fieldset>

    <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>" type="submit" id="db_opcao" value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> />
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
    </form>
</div>
<script>
    function js_pesquisadb83_bancoagencia(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_bancoagencia',
                'func_bancoagencia.php?digito=true&funcao_js=parent.js_mostrabancoagencia1|db89_sequencial|db89_codagencia|db89_digito',
                'Pesquisa conta bancária',
                true
            );
        } else {
            if (document.form1.db83_bancoagencia.value != '') {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_bancoagencia',
                    'func_bancoagencia.php?digito=true&pesquisa_chave=' + document.form1.db83_bancoagencia.value + '&funcao_js=parent.js_mostrabancoagencia',
                    'Pesquisa',
                    false
                );
            } else {
                document.form1.db89_codagencia.value = '';
            }
        }
    }

    function js_mostrabancoagencia(chave, chave1, erro) {
        if (erro) {
            document.form1.db83_bancoagencia.value = '';
        }
        document.form1.db89_codagencia.value = chave;
        document.form1.db89_digito.value = chave1;
        document.form1.db89_digito.value = chave1;
    }

    function js_mostrabancoagencia1(chave1, chave2, chave3) {

        document.form1.db83_bancoagencia.value = chave1;
        document.form1.db89_codagencia.value = chave2;
        document.form1.db89_digito.value = chave3;

        db_iframe_bancoagencia.hide();
    }

    function mostrarVinculoConta() {
        var contaPlano = document.getElementById('db83_contaplano').value == 't';
        document.getElementById('vincularConta').style.display = contaPlano ? '' : 'none';
    }

    function js_pesquisa() {
        js_OpenJanelaIframe(
            '',
            'db_iframe_contabancaria',
            'func_contabancariacadastro.php?funcao_js=parent.js_preenchepesquisa|db83_sequencial',
            'Pesquisa conta bancária',
            true
        );
    }

    function js_preenchepesquisa(chave) {
        db_iframe_contabancaria.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }
</script>
