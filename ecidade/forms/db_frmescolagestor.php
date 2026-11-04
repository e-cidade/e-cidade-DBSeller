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
$oDaoEscolaGestorCenso = new cl_escolagestorcenso();
$oDaoEscolaGestorCenso->rotulo->label();

$oRotulo = new rotulocampo();
$oRotulo->label("z01_nome");
?>
<div class="container">
    <form name="form1" method="POST" action="">
        <fieldset>
            <legend>
                <b>Gestor da Escola</b>
            </legend>
            <table class="form-container">
                <tr>
                    <td nowrap title="<?php echo @$Ted325_escola; ?>">
                        <label for="ed325_escola">
                            <?php
                            db_ancora($Led325_escola, "", 3);
                            ?>
                        </label>
                    </td>
                    <td nowrap="nowrap">
                        <?php
                        $ed325_escola = db_getsession("DB_coddepto");
                        db_input('ed325_escola', 10, $Ied325_escola, true, 'text', 3, "");
                        db_input('ed18_c_nome', 50, @$Ied18_c_nome, true, 'text', 3, "");
                        db_input('ed17_i_escola', 20, 'ed17_i_escola', true, 'hidden', 3, "");
                        db_input('ed325_sequencial', 20, 'ed325_sequencial', true, 'hidden', 3, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo @$Ted325_rechumano; ?>">
                        <label for="ed325_rechumano">
                            <?php
                            db_ancora("Gestor:", "js_pesquisaed254_i_rechumano(true);", $db_opcao);
                            ?>
                        </label>
                    </td>
                    <td nowrap="nowrap">
                        <?php
                        db_input('ed325_rechumano', 10, $Ied325_rechumano, true, 'text', 3, 'onchange="js_pesquisaed254_i_rechumano(false);"');
                        db_input('z01_nome', 50, $Iz01_nome, true, 'text', 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="z01_cgccpf">CPF:</label>
                    </td>
                    <td>
                        <?php
                        db_input('z01_cgccpf', 10, @$Iz01_cgccpf, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo @$Ted325_email; ?>">
                        <label for="ed325_email">
                            <?php
                            echo @$Led325_email;
                            ?>
                        </label>
                    </td>
                    <td>
                        <?php
                        db_input('ed325_email', 100, $Ied325_email, true, 'text', $db_opcao, "");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="<?php echo($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")); ?>"
               type="button" id="db_opcao" value="<?php echo($db_opcao == 1 ? "Salvar" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")); ?>"
            <?php echo($db_botao == false ? "disabled" : "") ?> onclick="return js_salvarGestorCenso();">
    </form>
</div>
<fieldset class="subcontainer" style="width: 800px">
    <legend>Gestores</legend>
    <div id="ctnGestores">
    </div>
</fieldset>

<script>
    var sUrlRPCGestorEscola = "edu4_dadosgestorescola.RPC.php";

    const clear = () => {
        $('ed325_sequencial').value = '';
        $('ed325_rechumano').value = '';
        $('ed325_email').value = '';
        $('z01_cgccpf').value = '';
        $('z01_nome').value = '';
    };

    const excluirGestor =  (gestor) => {
        if (!confirm("Deseja realmente excluir o gestor:\n" + gestor.nome)) {
            return;
        }
        js_divCarregando('Aguarde, Excluindo gestor', 'msgBox');

        const parametros = {'exec': 'excluir', 'codigo' : gestor.sequencial};
        new Ajax.Request(sUrlRPCGestorEscola, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(parametros),
            onComplete: (response) => {
                js_removeObj('msgBox');
                const retorno = JSON.parse(response.responseText);
                alert(retorno.sMessage.urlDecode());
                if (retorno.status == 1) {
                    collection.remove(gestor.sequencial);
                    gridGestores.reload();
                    clear();
                }
            }
        });
    };

    const collection = new Collection().setId("sequencial");
    const gridGestores = new DatagridCollection(collection).configure({
        order: false,
        height: 120
    });
    gridGestores.addColumn('nome', {'label': 'Gestor', 'width': "50%"});
    gridGestores.addColumn('email', {'label': 'Email', 'width': "35%"});
    gridGestores.addAction("A", "Alterar", function (evento, gestor) {
        $('ed325_sequencial').value = gestor.sequencial;
        $('ed325_rechumano').value = gestor.rechumano;
        $('ed325_email').value = gestor.email;
        $('z01_cgccpf').value = gestor.cpf;
        $('z01_nome').value = gestor.nome;
    });
    gridGestores.addAction("E", "Excluir", function (evento, gestor) {
        excluirGestor(gestor);
    });

    gridGestores.show($('ctnGestores'));

    function js_getGestorEscola() {
        clear();
        var oParametro = new Object();
        oParametro.exec = "getGestorEscolaCenso";

        js_divCarregando('Aguarde, Carregando dados do Gestor', 'msgBox');

        new Ajax.Request(sUrlRPCGestorEscola, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: js_mostrarDadosGestor
        });
    }

    function js_mostrarDadosGestor(oResponse) {

        js_removeObj('msgBox');

        var oRetorno = JSON.parse(oResponse.responseText);

        collection.clear();

        oRetorno.gestores.forEach(function (gestor) {
            collection.add({
                'sequencial': gestor.ed325_sequencial,
                'rechumano': gestor.ed325_rechumano,
                'email': gestor.ed325_email.urlDecode(),
                'cpf': gestor.z01_cgccpf,
                'nome': gestor.z01_nome.urlDecode(),
            });
        });

        gridGestores.reload();
    }

    function js_pesquisaed254_i_rechumano(lMostra) {

        if (lMostra == true) {

            js_OpenJanelaIframe('', 'db_iframe_rechumano',
                'func_rechumano.php?funcao_js=parent.js_mostrarechumano1|ed20_i_codigo|z01_nome|dl_cpf|rh37_descr',
                'Pesquisa de Recursos Humanos', true);
        } else {

            if (document.form1.ed325_rechumano.value != '') {

                js_OpenJanelaIframe('', 'db_iframe_rechumano',
                    'func_rechumano.php?pesquisa_chave=' + document.form1.ed325_rechumano.value + '&funcao_js=parent.js_mostrarechumano',
                    'Pesquisa de Recursos Humanos', false);

            } else {

                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_mostrarechumano(chave, erro) {

        if (erro == true) {

            document.form1.z01_nome.value = chave;
            document.form1.identificacao.value = '';

        }
        document.form1.ed325_email = '';
    }

    function js_mostrarechumano1(chave1, chave2, chave3) {

        if (document.form1.ed325_rechumano.value != chave1) {
            document.form1.ed325_email.value = '';
        }

        document.form1.ed325_rechumano.value = chave1;
        document.form1.z01_nome.value = chave2;
        document.form1.z01_cgccpf.value = chave3;

        db_iframe_rechumano.hide();
    }

    /**
     * @todo validar email
     */
    function js_validar() {

        if ($F('ed325_rechumano') == '') {

            alert("Gestor não informado!");
            return false;
        }

        if ($F('ed325_email') == '') {

            alert('Informe um email de contato!');

            return false;
        }

        if ($F('ed325_email') != '') {

            var oExpressaoValidacaoEmail = new RegExp("[A-Za-z0-9_.-]+@([A-Za-z0-9_]+\.)+[A-Za-z]{2,4}");

            if (!$F('ed325_email').match(oExpressaoValidacaoEmail)) {

                alert('Informe um email válido!');
                return false;
            }
        }

        return true;
    }

    function js_salvarGestorCenso() {

        if (!js_validar()) {

            return false;
        }

        var oParametro = new Object();

        oParametro.exec = "salvarGestorEscola";
        oParametro.sequencial = $('ed325_sequencial').value;
        oParametro.iRecHumano = $('ed325_rechumano').value;

        oParametro.sEmail = encodeURIComponent($F('ed325_email'));

        js_divCarregando('Aguarde, Salvando dados do gestor', 'msgBox');

        new Ajax.Request(sUrlRPCGestorEscola, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParametro),
            onComplete: js_retornoSalvarGestor
        });
    }

    function js_retornoSalvarGestor(oResponse) {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oResponse.responseText);

        alert(oRetorno.sMessage.urlDecode());
        if (oRetorno.status == 1) {
            js_getGestorEscola();
        }
    }

    $('ed325_email').style.width = '100%';

    js_getGestorEscola();
</script>
