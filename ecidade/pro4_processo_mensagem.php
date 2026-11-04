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
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
$processo = $oGet->processo;
$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <style type="text/css">


        .container-mensagens {
            width: 50%;
            margin: auto;
        }

        .container-mensagem {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .mensagem-prefeitura {
            text-align: left;
            font-size: 15px;
            padding: 5px;
            width: 80%;
            min-height: 40px;
            background: #bae8c4;
            margin-bottom: 5px;
            box-shadow: 2px 2px 2px 2px #888888;
            border-radius: 5px;
            align-self: flex-end;
        }

        .mensagem-cidadao {
            font-size: 15px;
            text-align: left;
            padding: 5px;
            float: left;
            width: 80%;
            min-height: 40px;
            background: #ccdcff;
            margin-bottom: 5px;
            box-shadow: 2px 2px 2px 2px #888888;
            border-radius: 5px;
            align-self: flex-start;
        }

        .mensagem-resposta {
            color: #666666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            max-width: 100%;
        }

        .data-mensagem {
            background: #eaeaea;
            width: 100px;
            margin-bottom: 5px;
            align-self: center;
            text-align: center;
            font-size: 10px;
            padding: 3px;
            box-shadow: 1px 1px 1px 1px #888888;
        }

        .botao-refresh {
            position: fixed;
            right: 10px;
            top: 40%;
            border-radius: 50px;
            height: 50px;
            width: 50px;
            background: #50c45054;
            border: none;
            box-shadow: 1px 3px 10px;
            font-size: 9px;
        }

        .botao-novaMensagem {
            position: fixed;
            right: 10px;
            top: 50%;
            border-radius: 50px;
            height: 50px;
            width: 50px;
            background: rgba(73, 100, 245, 0.33);
            border: none;
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 1);
            font-size: 9px;
            transform: scale(1);
            animation: pulse 2s infinite;
        }

        .sem-mensagem {
            padding: 10px;
            background: #dc9e5fc7;
            text-align: center;
            margin: auto;
            margin-top: 40%;
            border-radius: 5px;
            box-shadow: 2px 2px 2px 1px #656060cc;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(0, 0, 0, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
            }
        }


    </style>
</head>
<body style="background: white!important" onLoad="a=1">
<h1>
    <button onclick="getMensagens()" class="botao-refresh"><i class="fas fa-sync-alt"></i></button>
    <button onclick="novaMensagem(<?= $processo ?>)" title="Nova Mensagem" class="botao-novaMensagem"><i
            class="fa fa-plus"></i></button>
</h1>
<div class="container-mensagens" id="container"></div>
<script src="scripts/session.js"></script>
<script>

    var session = null;
    const numero_processo = <?=$processo?>;
    const containerHTML = document.getElementById("container");

    function scrollToBottom() {
        document.querySelector("body").scroll({
            top: containerHTML.scrollHeight,
            left: 0,
            behavior: 'smooth'
        });
    }

    function scrollMensagem(codigo) {
        const el = document.getElementById(`mensagem_${codigo}`);
        el.scrollIntoView({block: 'start', behavior: 'smooth'});
    }

    function visualizarDocumentos(codigosEStorage) {
        if (<?=$visualizarEmOutraJanela?>) {
            window.open(`db_visualizador_documentos.php?ids=${codigosEStorage}`);
        } else {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_visualizador_imagens',
                `db_visualizador_documentos.php?ids=${codigosEStorage}`,
                'Visualizador de documentos',
                true
            );
        }
    }

    function responderMensagem(codigoAndamento) {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_processo_form_mensagem',
            `pro4_processo_form_mensagem.php?codigoAndamento=${codigoAndamento}&tipoMensagem=respostaPrefeitura`,
            'Responder Mensagem',
            true,
            0,
            0
        );
    }

    function novaMensagem() {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_processo_form_mensagem',
            `pro4_processo_form_mensagem.php?codigoProcesso=${numero_processo}&tipoMensagem=mensagemPrefeitura`,
            'Nova Mensagem',
            true,
            0,
            0
        );
    }

    function checkMensagemUsuario(tipodespacho) {
        const tiposUsuario = [1000, 1001];
        return tiposUsuario.includes(tipodespacho);
    }

    async function salvarVisualizacao(codigoMensagem) {
        try {
            let config = session.getConfigRequest();
            config.method = 'POST';
            const response = await fetch(
                `${session.requestApi}/patrimonial/ouvidoria/atendimento/processo-eletronico/savevisualizacao/${codigoMensagem}`,
                config
            );
            const data = await response.json();
            if (data.success) {
                const el = document.getElementById(`mensagem_${codigoMensagem}`);
                el.title = titulo = `Visto em ${data.data.data_visualizacao} por ${data.data.usuario_visualizou}`
            }
            console.log(data.message);
        } catch (e) {
            console.log(e);
        }

    }

    async function getMensagens() {

        try {
            js_divCarregando("Carregando mensagens...", 'loading_message');
            const response = await fetch(
                `${session.requestApi}/patrimonial/ouvidoria/atendimento/processo-eletronico/mensagens/${numero_processo}`,
                session.getConfigRequest()
            );
            const responseData = await response.json();
            const data = responseData.data;
            html = ``;
            data.forEach((mensagem, key) => {
                const classMensagem = checkMensagemUsuario(mensagem.tipo_despacho) ? 'mensagem-cidadao' : 'mensagem-prefeitura';
                const anexos = [];
                mensagem.anexos.forEach(anexo => {
                    anexos.push(anexo.id_estorage);
                });
                let htmlBotaoAnexos = ``;
                if (anexos.length > 0) {
                    htmlBotaoAnexos += `<i class="fa fa-file" onclick="visualizarDocumentos('${anexos.join(",")}')"></i>`;
                }
                let htmlBotaoResponder = ``;
                if (checkMensagemUsuario(mensagem.tipo_despacho) && !mensagem.referencia_codigo) {
                    htmlBotaoResponder += `<i class="fa fa-reply-all" title="Responder mensagem do cidadao" onclick="responderMensagem(${mensagem.codigo_andamento})"></i>`;
                }
                if (key == 0) {
                    html += `
                         <div class="container-mensagem">
                         <div  class="data-mensagem">
                             ${mensagem.data}
                          </div>
                         </div>
                      `;
                }
                if (key > 0 && mensagem.data != data[key - 1].data) {
                    html += `
                         <div class="container-mensagem">
                         <div  class="data-mensagem">
                             ${mensagem.data}
                          </div>
                         </div>
                      `;
                }
                let mensagemReferencia = ``;
                if (mensagem.referencia_codigo) {
                    mensagemReferencia = `<div  onclick="scrollMensagem(${mensagem.referencia_codigo})" class="mensagem-resposta">
                    ${mensagem.referencia_mensagem}
                                        </div>`
                }
                let titulo = '';
                if (!mensagem.data_visualizacao || mensagem.data_visualizacao == '') {
                    salvarVisualizacao(mensagem.codigo);
                } else {
                    titulo = `Visto em ${mensagem.data_visualizacao} por ${mensagem.usuario_visualizou}`
                }
                html += `
                    <div class="container-mensagem">
                        <div
                            class="${classMensagem}"
                            id="mensagem_${mensagem.codigo}"
                            title="${titulo}"
                        >
                            ${mensagem.mensagem}
                            <br>
                             ${mensagemReferencia}
                             <div style="text-align: right">
                              ${htmlBotaoResponder}
                              ${htmlBotaoAnexos}
                              ${mensagem.hora}
                            </div>
                        </div>
                    </div>
                `;
            });
            if (data.length < 1) {
                this.html = `<div class="sem-mensagem"><h1 >Não possui mensagens!</h1></div>`;
            }
            containerHTML.innerHTML = html;
            js_removeObj('loading_message');
            scrollToBottom();
        } catch (e) {
            js_removeObj('loading_message');
        }
    }

    async function main() {
        session = await PHPSession.loadData();
        getMensagens();
    }

    main();


</script>
</body>
</html>
