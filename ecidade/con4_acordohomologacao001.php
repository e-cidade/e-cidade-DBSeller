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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>

    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>

    <style>
        label, select {
            display: flex;
        }
    </style>
</head>
<body class="body-default">
    <div class="container">
        <form action="" id="formulario">
            <fieldset>
                <legend>Parâmetros Globais</legend>
                <table>
                    <tr>
                        <td>
                            <label for="" class="bold">Homologação automática:</label>
                        </td>
                        <td>
                            <select name="" id="ac59_homologacaoauto">
                                <option value="f">Não</option>
                                <option value="t">Sim</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="" class="bold">Exibe campo fiscal no cadastro de comissões:</label>
                        </td>
                        <td>
                            <select name="" id="ac59_cadastrocomissoesfiscal">
                                <option value="f">Não</option>
                                <option value="t">Sim</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="" class="bold" title="Permite liquidação do empenho com ou sem aviso, ou não permite
com aviso caso o contrato não encontra-se no portal LICITACON TCE/RS.">
                                Valida Licitacon:
                            </label>
                        </td>
                        <td>
                            <select name="" id="ac59_validalicitacon">
                                <option value="1">Permitir sem avisar</option>
                                <option value="2">Permitir com aviso</option>
                                <option value="3">Não permitir com aviso</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="bold" title="Define se a seleção de processos será por instituição ou global.">
                                Seleciona processo por instituição:
                            </label>
                        </td>
                        <td>
                            <select name="" id="ac59_permiteporinstit">
                                <option value="t">Sim</option>
                                <option value="f">Não</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" name="ac59_sequencial" id="ac59_sequencial"/>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" id="btnSalvar" value="Salvar"/>
        </form>
    </div>

    <script>
        const RPC = 'ac4_acordoparam.RPC.php';

        const getParametrosConfigurados = async() => {
            const formData = new FormData();
            formData.append('exec', 'getParametrosConfigurados');

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando parâmetros configurados.`
            }

            const retorno = await HttpClient.post(RPC, parametros);

            if(retorno.erro) return;

            preencheFormulario(retorno);
        }
        const preencheFormulario = (dados) => {
            const elementos = document.getElementById('formulario').elements;
            Array.from(elementos).map(campo => {
                if(campo.id != "") {
                    const { type } = document.getElementById(campo.id);
                    if(type.includes('select')) {
                        const options = document.getElementById(campo.id).options;
                        Array.from(options).map(option => {
                            if(option.value == dados[campo.id]) {
                                option.selected = true;
                            }
                        })
                    } else {
                        campo.value = dados[campo.id];
                    }
                }
            });
        }
        const salvarParametros = async () => {
            const elementos = document.getElementById('formulario').elements;
            const formData = new FormData();
            const ac59_homologacaoauto = formulario.ac59_homologacaoauto.value == 1 ? 't' : 'f';

            formData.append('exec', 'salvarParametrosHomologacao');
            Array.from(elementos).map(campo => formData.append(campo.id, campo.value));

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, salvando parâmetros configurados.`
            }

            const retorno = await HttpClient.post(RPC, parametros);

            if(retorno.erro) {
                alert(retorno.message.urlDecode());
            } else {
                alert('Parâmetros salvos com sucesso.');
            }
        }
        const configuraEventListeners = () => {
            const btnSalvar = document.getElementById('btnSalvar');
            btnSalvar.addEventListener('click', salvarParametros)
        }
        const modificarValorBotao = () => {
            document.getElementById('btnSalvar').value = 'Salvar';
        }
        const init = async () => {
            await getParametrosConfigurados();
            configuraEventListeners();
            modificarValorBotao();
        }

        init();

    </script>
</body>
</html>
