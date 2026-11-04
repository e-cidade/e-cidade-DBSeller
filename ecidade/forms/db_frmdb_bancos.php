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

// MODULO: configuracoes
$cldb_bancos->rotulo->label();

?>

<form 
    name="form1" 
    method="post"
    enctype="multipart/form-data"
>
    <link rel="stylesheet" type="text/css" href="estilos.css" />
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css" rel="stylesheet" />
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css" rel="stylesheet" />
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/session.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputCNPJ.js"></script>

    <input 
        type="hidden" 
        name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
        value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" 
    > 

    <div id="container">
        <div id="dadosdobanco" class="container-info-banck">
            <fieldset class="fieldsetPrincipal">
                <legend>Dados do Banco</legend>
                <table border="0">
                    <tr>
                        <td nowrap title="<?= @$Tdb90_codban ?>">
                            <?= @$Ldb90_codban ?>
                        </td>
                        <td>
                            <?php
                            db_input('db90_codban', 10, $Idb90_codban, true, 'text', $db_opcao, "")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Tdb90_descr ?>">
                            <?= @$Ldb90_descr ?>
                        </td>
                        <td>
                            <?php
                            db_input('db90_descr', 40, $Idb90_descr, true, 'text', $db_opcao, "")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Tdb90_digban ?>">
                            <?= @$Ldb90_digban ?>
                        </td>
                        <td>
                            <?php
                            db_input('db90_digban', 2, $Idb90_digban, true, 'text', $db_opcao, "")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Tdb90_abrev ?>">
                            <?= @$Ldb90_abrev ?>
                        </td>
                        <td>
                            <?php
                            db_input('db90_abrev', 20, $Idb90_abrev, true, 'text', $db_opcao, "")
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Tdb90_logo ?>">
                            <?= @$Ldb90_logo ?>
                        </td>
                        <td>
                            <input type="file" name="db90_logo" size="40" />
                        </td>
                    </tr>
                </table>

                <?php
                if (isset($db90_logo) and $db90_logo != "") {
                ?>
                    <table style="margin-top: 15px">
                        <tr>
                            <td> <b>Imagem gravada no banco</b></td>
                        </tr>
                        <tr>
                            <td align="center">
                                <img src="mostralogo.php?db90_logo=<?= $db90_logo ?>">
                            </td>
                        </tr>
                    </table>
                <?php
                } else {
                    echo "Não possui imagem gravada no banco";
                }
                ?>
            </fieldset>
        </div>

        <div id="pix" class="container-info-banck">
            <fieldset class="fieldsetPrincipal">
                <legend>Informe os dados para configuração</legend>
                <div>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Banco: </b>
                                </label>
                                <input 
                                    disabled
                                    type="text"
                                    id="fake_db90_codban"
                                    value="<?= (isset($GLOBALS["db90_codban"]) ? $GLOBALS["db90_codban"] : "") ?>"
                                    style="width: 80px;"
                                >
                                <input 
                                    disabled
                                    type="text" 
                                    id="fake_db90_descr"
                                    value="<?= (isset($GLOBALS["db90_descr"]) ? $GLOBALS["db90_descr"] : "") ?>"
                                    style="width: 200px;"
                                >
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Numero do Convénio: </b>
                                </label>
                                <input 
                                    type="text" 
                                    id="db90_numconv" 
                                    style="width: 285px;"
                                    <?= ($db_opcao == 2 ? "" : "disabled") ?>
                                >
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Chave PIX: </b>
                                </label>
                                <input 
                                    type="text" 
                                    id="db90_chave_pix" 
                                    style="width: 285px;"
                                    <?= ($db_opcao == 2 ? "" : "disabled") ?>
                                >
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Tipo Ambiente: </b>
                                </label>
                                <select name="db90_tipo_ambiente" id="db90_tipo_ambiente">
                                    <option value="1">Produção</option>
                                    <option value="2">Homologação</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Usar CNPJ do Município para CGMs desatualizados:</b>
                                </label>
                                <select name="db90_cnpj_municipio" id="db90_cnpj_municipio">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr id="trCpfCnpj">
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>CNPJ: </b>
                                </label>
                                <input 
                                    type="text" 
                                    id="db90_cnpj" 
                                    style="width: 285px;"
                                    <?= ($db_opcao == 2 ? "" : "disabled") ?>
                                >
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Client Id: </b>
                                </label>
                                <input 
                                    type="text" 
                                    id="db90_login" 
                                    style="width: 285px;"
                                    <?= ($db_opcao == 2 ? "" : "disabled") ?>
                                >
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Client Secret: </b>
                                </label>
                                <input 
                                    type="text" 
                                    id="db90_senha" 
                                    style="width: 285px;"
                                    <?= ($db_opcao == 2 ? "" : "disabled") ?>
                                >
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label style="width:200px; display: inline-block;">
                                    <b>Chave API: </b>
                                </label>
                                <input 
                                    type="text" 
                                    id="db90_chave_api" 
                                    style="width: 285px;"
                                    <?= ($db_opcao == 2 ? "" : "disabled") ?>
                                >
                            </td>
                        </tr>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
    <div>
        <div class="btns-action">
            <input 
                type="button" 
                id="db_opcao" 
                onclick="requestBanco(this)"
                value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" 
                <?= ($db_botao == false ? "disabled" : "") ?>
            >
            <input
                name="pesquisar"
                type="button" 
                id="pesquisar" 
                value="Pesquisar"
                onclick="js_pesquisa()"
            >
        </div>
    </div>
</form>

<style>

.btns-action {
    text-align: center;
    margin-top: 1em;
}

.container-info-banck {
    padding: 1em 0;
}

</style>

<script>
    var 
        oAbas  = new DBAbas(document.getElementById("container")),
        codban = <?= (isset($GLOBALS["db90_codban"]) ? "'" . $GLOBALS["db90_codban"] . "'" : "null") ?>,
        form   = null;
    
    const
        url       = "<?= ECIDADE_REQUEST_PATH ?>",
        dados_pix = [];

    const inputCNPJ = new DBInputCNPJ(document.getElementById('db90_cnpj'));

    oAbas.adicionarAba('Dados do Banco', document.getElementById("dadosdobanco"));
    
    window.onload = function()
    {
        PHPSession.loadData().then(() => {
            document.getElementById("db90_codban").addEventListener("keyup", function(event)
            {
                let input = event.target;

                document.getElementById("fake_db90_codban").value = input.value;
            });

            document.getElementById("db90_descr").addEventListener("keyup", function(event)
            {
                let input = event.target;

                document.getElementById("fake_db90_descr").value = input.value;
            });

            document.getElementById('db90_cnpj_municipio').addEventListener("change", function(event){
                let situacao = event.target.value;
                makeRowTableCnpj(situacao);
            });
            
            form = document.form1;

            if (codban != null)
            {
                oAbas.adicionarAba('PIX', document.getElementById("pix"));

                HttpClient.get(url + "v4/api/configuracao/banco-pix/" + codban)
                .then((res) => {
                    if (res.status == "Success") {
                        return montar_dados_pix(res.message);
                    }
                        
                    alert(res.message);
                    js_pesquisa();
                })
            }
            else 
            {
                document.getElementById("pix").style.display = "none";
            }
        });
    }

    function requestBanco(event)
    {
        if (event.value == "Alterar")
        {
            let data = new FormData();

            dados_pix.forEach((input) => {
                
                let value = input.value;

                if (input.id === 'db90_cnpj') 
                {
                    value = inputCNPJ.getValue();
                }

                data.append(input.id, value);
            });

            data.append("db90_codban", codban);
            PHPSession.appendFormData(data);
            
            HttpClient.post(url + "v4/api/configuracao/banco-pix/atualizar/" + codban, {body: data})
            .then((res) => {
                if (res.status == "Success")
                {
                    form.submit();
                    return;
                }

                alert(res.message);
            });
        }
        else if (event.value == "Incluir")
        {
            form.submit();

            return;
        }
        else if (event.value == "Excluir")
        {
            HttpClient.delete(url + "v4/api/configuracao/banco-pix/excluir/" + codban)
            .then((res) => {
                if (res.status == "Success")
                {
                    form.submit();
                    return;
                }

                alert(res.message);
            });
        }

        return false;
    }

    function montar_dados_pix(data)
    {
        if (typeof data == "object")
        {
            for (let index in data)
            {
                let input = document.getElementById(index);

                if (input != null)
                {   
                  
                    if (typeof data[index] == "boolean") {
                        data[index] = data[index] === true ? 1 : 0;
                    }
                    
                    if (index == 'db90_cnpj_municipio') {
                        makeRowTableCnpj(data[index]);
                    }
                    
                    dados_pix.push(input);
                    input.value = data[index];
                    
                    if (index == 'db90_cnpj') {
                        inputCNPJ.setValue(data[index]);
                    }
                }
            }
        }
    }

    function js_pesquisa()
    {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo', 
            'db_iframe_db_bancos', 
            'func_db_bancos.php?funcao_js=parent.js_preenchepesquisa|db90_codban', 
            'Pesquisa', 
            true
        );
    }

    function js_preenchepesquisa(chave)
    {
        db_iframe_db_bancos.hide();

        <?php
            if ($db_opcao != 1)
            {
                echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
            }
        ?>
    }

    function makeRowTableCnpj(situacao)
    {
        let tableCnpj = document.getElementById('trCpfCnpj');
        
        if (parseInt(situacao) === 1)
        {
            tableCnpj.style.display = '';
        }else{
            tableCnpj.style.display = 'none';
        }
    }
</script>
