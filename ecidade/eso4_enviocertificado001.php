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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('dbforms/db_funcoes.php');

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBSeller Serviços de Informática Ltda</title>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script src="scripts/scripts.js" rel="script" type="text/javascript"></script>
    <script src="scripts/strings.js" rel="script" type="text/javascript"></script>
    <script src="scripts/prototype.js" rel="script" type="text/javascript"></script>
    <script src="scripts/arrays.js" rel="script" type="text/javascript"></script>
    <script src="scripts/widgets/DBFileUpload.widget.js" rel="script" type="text/javascript"></script>
    <script src="scripts/AjaxRequest.js" rel="script" type="text/javascript"></script>
    <script src="scripts/classes/http/http.js" rel="script" type="text/javascript"></script>
    <style>
        #message {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            margin-top: 10px;
            padding: 20px;
            font-size: 20px;

        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            margin-top: 10px;
            padding: 20px;
            font-size: 20px;
        }
      
    </style>
</head>
<body class="body-default">
<div class="container">
    <form id="form_certificado">
        <fieldset>
            <legend>Configuração do Certificado</legend>
            <table class="form-container">
                <tbody>
                <tr id="tr_empregador" class="d-none">
                    <td>
                        <label for="empregador">Empregador:</label>
                    </td>
                    <td>
                        <select name="empregador" id="empregador"></select>
                    </td>
                </tr>
                <tr id="tr_contribuinte" class="d-none">
                    <td>
                        <label for="descricao">Contribuinte:</label>
                    </td>
                    <td>
                        <select id="contribuinte" name="contribuinte">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="documento">Documento:</label>
                    </td>
                    <td>
                        <input type="text" id="documento" class="readonly field-size3" disabled>
                        <input type="text" id="tipo" class="readonly field-size1" title="Tipo" disabled>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Senha do Certificado:</label>
                    </td>
                    <td>
                        <input type="password" id="password" name="password">
                    </td>
                </tr>
                <tr id="tr_procuracao" style="display: none">
                    <td>
                        <label for="procuracao">Possui Procura&ccedil;&atilde;o:</label>
                    </td>
                    <td>
                        <select name="procuracao" id="procuracao">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </td>
                </tr>
                <tr id="tr_procuracaodocumento" style="display: none">
                    <td>
                        <label for="procuracaodocumento">CPF/CNPJ (Somente Números):</label>
                    </td>
                    <td>
                        <input type="text" id="procuracaodocumento" onBlur="js_verificaCGCCPF(this)" maxlength="14"
                               class="field-size3">
                    </td>
                </tr>
                </tbody>
            </table>
            <fieldset class="separator">
                <legend>Clique no botão "Arquivo" e selecione o certificado</legend>
                <div id="ctnImportacao"></div>
            </fieldset>
        </fieldset>
        <input type="button" id="processar" value="Processar" disabled/>
    </form>

    </div>
<div id="message"></div>
<?php
db_menu();
?>
<script rel="script" type="text/javascript">
    (() => {
        message.classList.remove('alert-danger')
        message.classList.remove('alert-success')
        message.innerHTML = ''
        const EFD_REINF = '1';
        const ESOCIAL = '2';
        const urlParams = new URLSearchParams(window.location.search);
        const integracao = urlParams.has('integracao') ? urlParams.get('integracao') : '2';
        const trEmpregador = document.getElementById('tr_empregador');
        const trContribuinte = document.getElementById('tr_contribuinte');
        const selectEmpregador = document.getElementById('empregador');
        const selectContribuinte = document.getElementById('contribuinte');
        const inputDocumento = document.getElementById('documento');
        const inputTipo = document.getElementById('tipo');
        const inputPassword = document.getElementById('password');
        const buttonProcessar = document.getElementById('processar');
        const formCertificado = document.getElementById('form_certificado');
        const empregadores = new Map();
        const contribuintes = new Map();
        const selectProcuracao = document.getElementById('procuracao');
        const trProcuracao = document.getElementById('tr_procuracao');
        const trProcuracaoDocumento = document.getElementById('tr_procuracaodocumento');
        const inputProcuracaoDocumento = document.getElementById('procuracaodocumento');
        var contribuinteSelecionado = {};
        var empregadorSelecionado = {};
        var inputUploadFile = null;

        const callBackUpload = retorno => {
            if (retorno.error) {
                alert(retorno.error);
                buttonProcessar.disabled = true;
                return false;
            }

            const extension = ['crt', 'pfx', 'p12'];

            if (!extension.in_array(retorno.extension.toLowerCase())) {
                alert('Arquivo inválido.\nArquivo selecionado deve ser um certificado com a extensão "' +
                    extension.implode(', ') + '".');
                buttonProcessar.disabled = true;
                inputUploadFile.value = '';

                return false;
            }

            buttonProcessar.disabled = false;
        };

        const fileUpload = new DBFileUpload({
            callBack: callBackUpload,
            labelButton: 'Arquivo'
        });

        const changeSelectEmpregador = () => {
            empregadorSelecionado = empregadores.get(selectEmpregador.value);
            inputDocumento.defaultValue = empregadorSelecionado.cnpj;
            inputTipo.defaultValue = empregadorSelecionado.cnpj.length === 11 ? 'CPF' : 'CNPJ';
        };

        const changeSelectContribuinte = () => {
            contribuinteSelecionado = contribuintes.get(selectContribuinte.value);
            inputDocumento.defaultValue = contribuinteSelecionado.cnpj;
            inputTipo.defaultValue = 'CNPJ';
        }

        const changeSelectProcuracao = () => {
            var opcao = selectProcuracao.value;

            if (opcao == 1) {
                trProcuracaoDocumento.style.display = "table-row";
            } else {
                trProcuracaoDocumento.style.display = "none";
                inputProcuracaoDocumento.value = '';
            }
        };

        const clickButtonProcessar = () => {

                const formData = new FormData();
                const parametros = {
                    exec: 'empregador',
                    senha: encodeURIComponent(tagString(inputPassword.value)),
                    sFile: fileUpload.file,
                    sPath: fileUpload.filePath,
                    integracao: integracao
                };

                if (inputPassword.value === '') {
                    alert('O campo "Senha do Certificado" é obrigatório.');
                    return false
                }

                if (inputUploadFile.value === '') {
                    alert('O campo "Arquivo" é obrigatório.');
                    return false 
                }

                if (integracao === EFD_REINF) {
                    parametros.empregador = selectContribuinte.value;
                    parametros.documento = contribuinteSelecionado.cnpj;
                    parametros.razao_social = contribuinteSelecionado.descricao;
                }

                if (integracao === ESOCIAL) {
                    parametros.empregador = selectEmpregador.value;
                    parametros.documento = empregadorSelecionado.cnpj;
                    parametros.razao_social = empregadorSelecionado.nome;
                }
                var opcao = selectProcuracao.value;
                if (opcao == 1) {
                    if (inputProcuracaoDocumento.value == '') {
                        alert("Campo CPF/CNPJ não preenchido.")
                        return false;
                    }
                    if (!js_verificaCGCCPF(inputProcuracaoDocumento)) {
                        return false;
                    }
                    parametros.procuracao_documento = inputProcuracaoDocumento.value;
                }

                formData.append('json', JSON.stringify(parametros));

                HttpClient.post('eso4_esocialapi.RPC.php', {
                    body: formData
                }).then(response => {
                    if (response.Code === 422) {
                        message.innerHTML = 'Certificado não instalado. Favor verificar Senha.'
                        message.classList.add('alert-danger')
                    } else {
                        if (response.dataValidadeCertificado.dataValidadeCertificado != undefined) {
                            let data = response.dataValidadeCertificado.dataValidadeCertificado.split('-')
                            let dataFormatada = data[2]+'/'+data[1]+'/'+data[0]
                            message.innerHTML = 'Certificado Instalado com Sucesso . Data do Certificado Digital atual possui validade até . '+dataFormatada
                            message.classList.add('alert-success')
                        }
                    }

                    if (response.erro) {
                        inputPassword.value = '';

                        return false;
                    }

                    formCertificado.reset();
                    inputUploadFile.value = '';
                    resetaProcuracao();
                });
            
        };

        const resetaProcuracao = () => {
            trProcuracaoDocumento.style.display = "none";
            inputProcuracaoDocumento.value = '';
        }

        const adicionarListeners = () => {
            buttonProcessar.addEventListener('click', clickButtonProcessar);

            if (integracao == EFD_REINF) {
                selectContribuinte.addEventListener('change', changeSelectContribuinte);
                selectContribuinte.dispatchEvent(new Event('change'));
            }

            if (integracao === ESOCIAL) {
                selectEmpregador.addEventListener('change', changeSelectEmpregador);
                selectEmpregador.dispatchEvent(new Event('change'));
            }

            selectProcuracao.addEventListener('change', changeSelectProcuracao);
            selectProcuracao.dispatchEvent(new Event('change'));
        };

        const inicializar = () => {
            fileUpload.show(document.getElementById('ctnImportacao'));
            inputUploadFile = document.querySelector('.inputUploadFile');
            inputUploadFile.classList.add('field-size5');

            const formData = new FormData();
            formData.append('acao', 'inicializar');
            formData.append('integracao', integracao);

            HttpClient.post('sped02_preenchimento.RPC.php', {
                body: formData
            }).then(response => {
                if (response.erro) {
                    throw response.mensagem;
                }

                if (integracao === EFD_REINF) {
                    trProcuracao.style.display = "table-row";
                    const contribuinte = response.contribuinte;

                    contribuinte.forEach((i, key) => {
                        let selected = key === 0 ;
                        let option = new Option(i.descricao, i.cgm, selected, selected)

                        selectContribuinte.appendChild(option);
                        contribuintes.set(String(i.cgm), i);
                    });

                    trContribuinte.classList.remove('d-none');
                }

                if (integracao === ESOCIAL) {
                    trProcuracao.style.display = "table-row";
                    response.empregadores.map((empregadorOption, chave) => {
                        const selecionado = chave === 0;

                        selectEmpregador.add(
                            new Option(empregadorOption.nome, empregadorOption.cgm),
                            selecionado,
                            selecionado
                        );
                        empregadores.set(empregadorOption.cgm, empregadorOption);
                    });
                    trEmpregador.classList.remove('d-none');
                }
            }).then(adicionarListeners).catch(mensagem => alert(mensagem));
        };

        inicializar();
    })();
</script>
</body>
</html>
