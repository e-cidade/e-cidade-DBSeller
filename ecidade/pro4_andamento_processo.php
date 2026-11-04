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

$cldbdepart = new cl_db_depart;
$orgao = $cldbdepart->get_orgao();
$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" href="estilos.css"/>
    <link rel="stylesheet" href="estilos/grid.style.css"/>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link href="skins/default/estilos/widgets/DBModal.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBModal.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/patrimonio/AndamentoProcesso.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/tributario/issqn/redesim/ExecutarAcaoRedesim.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/tributario/issqn/redesim/EnviaRespostaRedesim.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/tributario/issqn/redesim/VisualizarDadosRedesim.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
    <script type="text/javascript" src="scripts/socket.io.js"></script>

    <style>
        .processoParaUsuarioLogado {
            font-weight: bold;
        }

        #gridProcessos tr:hover {
            opacity: 0.7;
        }

        input[type=checkbox], input[type=radio] {
            vertical-align: middle;
        }

        .background-receber {
            background-color: #fff;
        }

        .background-recebido {
            background-color: #d7ffd9;
        }

        .background-despachado {
            background-color: #ffddc1;
        }

        .background-externo {
            background-color: #C89EE8
        }

        .btnAtualizarContainer {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
            border: 1px solid #8D8D8D;
            border-left: 1px solid #8D8D8D;
            border-right: 1px solid #8D8D8D;
            margin-right: -5px;
        }

        #camposDinamicos {
            margin-top: 0;
            width: 80%;
        }

        #camposDinamicos > div {
            display: flex;
            align-items: center;
            flex-direction: row;
            justify-content: space-between;
            margin: 10px;
        }
        #camposDinamicos > div > label {
            width: 35%;
            text-align: right;
        }
        #camposDinamicos > div > :not(label) {
            width: 65%;
            margin-left: 5px;
        }
        #camposDinamicos > div > input[type=data] {
            width: calc(65% - 18px);
        }
        #camposDinamicos > div > input[type=data]~input[type=button] {
            width: 18px;
        }

        #camposDinamicos .error-field {
            border: 1px solid #FF0000;
        }
    </style>
</head>
<body bgcolor="#cccccc" style='margin: 30px 0 0 0'>
<div class="alert alert-primary text-left" role="alert">
    Por padrão serão carregados
    os processos conforme o exercício configurado, caso deseje buscar processos
    de exercício anteriores, utilize o filtro ano.
</div>
<div class="container">
    <fieldset>
        <legend><strong>Órgão: <?php echo $orgao ?></strong></legend>
        <div>
            <form>
                <fieldset>
                    <legend>Filtros</legend>
                    <table class="form-container">
                        <tr>
                            <td style="text-align: center;">
                                <label style="padding-left: 5px" for="">Processo:</label>
                                <input type="text" name="" class="filtro" id="processo" value="">
                                <label style="padding-left: 5px" for="">Requerente:</label>
                                <input type="text" name="" class="filtro" id="requerente" value="">
                                <label style="padding-left: 5px" for="">Descrição:</label>
                                <input type="text" name="" class="filtro" id="descricao">
                                <label style="padding-left: 5px" for="">Data:</label>
                                <input type="text" name="dataInicio" class="filtro" id="dataInicio" value="">
                                <label style="padding-left: 5px" for="">Ano:</label>
                                <input style="width: 55px" type="text" name="anoProcesso" class="filtro" id="anoProcesso" maxlength="4"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                <button class="button--light filtro" id="atualizarAno" title="Recarregar" style="border-radius: 2px;">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </td>
                        </tr>
                            </td>
                    </table>
                </fieldset>
            </form>
        </div>
        <div style="width: 100%;display: inline-flex">
            <div style="width: 300px;">
                <form>
                    <table class="form-container"
                    <tr>
                        <td align="left">
                            <fieldset style="text-align: left; position: relative;">
                                <legend>Filtrar Responsável:</legend>
                                <input
                                    type="checkbox"
                                    name="usuario"
                                    class="filtro"
                                    id="usuario"
                                    data-responsavel="1"
                                >
                                <label for="usuario"
                                       style="padding:1px;border: 1px solid black; vertical-align: middle"
                                >
                                    <b>Meu</b>
                                </label>

                                <input type="checkbox"
                                       name="outros"
                                       class="filtro"
                                       id="outros"
                                       data-responsavel="2"
                                />
                                <label for="outros"
                                       style="padding:1px;border: 1px solid black; vertical-align: middle"
                                >
                                    <b>Outros</b>
                                </label>


                                <input type="checkbox"
                                       name="departamento"
                                       class="filtro"
                                       id="departamento"
                                       data-responsavel="0"
                                />
                                <label for="departamento"
                                       style="padding:1px;border: 1px solid black; vertical-align: middle"
                                >
                                    <b>Departamento</b>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </table>
                </form>
            </div>
            <div style="width: 300px;">
                <form>
                    <table class="form-container"
                    <tr>
                        <td align="left">
                            <fieldset style="text-align: left; position: relative;">
                                <legend>Filtrar Tipo Processo:</legend>
                                <input type="checkbox" name="eletronico" class="filtro" id="eletronico"
                                       data-tipoprocesso="1">
                                <label for="eletronico"
                                       style="padding:1px;border: 1px solid black; vertical-align: middle">
                                    <b>Eletrônico</b>
                                </label>

                                <input type="checkbox" name="manual" class="filtro" id="manual" data-tipoprocesso="0">
                                <label for="manual"
                                       style="padding:1px;border: 1px solid black; vertical-align: middle">
                                    <b>Manual</b>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </table>
                </form>
            </div>
            <div style="width: 600px;">
                <form>
                    <table class="form-container">
                        <tr>
                            <td align="left">
                                <fieldset style="text-align: left; position: relative;">
                                    <legend>Filtrar status:</legend>
                                    <input type="checkbox" name="receber" class="filtro" id="receber" data-codigo="1">
                                    <label for="receber" class="background-receber"
                                           style="padding:1px;border: 1px solid black; vertical-align: middle">
                                        <b>A receber</b>
                                    </label>

                                    <input type="checkbox" name="recebido" class="filtro" id="recebido" data-codigo="2">
                                    <label for="recebido" class="background-recebido"
                                           style="padding:1px;border: 1px solid black; vertical-align: middle">
                                        <b>Recebidos</b>
                                    </label>

                                    <input type="checkbox" name="despachado" class="filtro" id="despachado"
                                           data-codigo="3">
                                    <label for="despachado" class="background-despachado"
                                           style="padding:1px;border: 1px solid black; vertical-align: middle">
                                        <b>Despachados</b>
                                    </label>
                                    <input type="checkbox" name="externo" class="filtro" id="externo" data-codigo="4">
                                    <label for="externo" class="background-externo"
                                           style="padding:1px;border: 1px solid black; vertical-align: middle">
                                        <b>Externos</b>
                                    </label>
                                    <input type="checkbox" name="mensagemCheck" class="filtro" id="mensagemCheck">
                                    <label for="mensagemCheck" class="background-mensagem"
                                           style="padding:1px;border: 1px solid black; vertical-align: middle">
                                        <b>Mensagens não lidas</b>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <div id="gridProcessos" style="width: 1200px;overflow: auto"></div>
        <div class="btnAtualizarContainer">
            <button class="button button--light button--block" id="atualizar" title="Recarregar">
                <i class="fa fa-redo" aria-hidden="true"></i>
            </button>
        </div>
    </fieldset>
</div>

<?php db_menu(); ?>
</body>
<script>
    var visualizarEmOutraJanela = Boolean(<?php echo $visualizarEmOutraJanela == 'true' ? 'true' : 'false' ?>);
    const date = new Date();
    new DBInputDate(document.getElementById('dataInicio'));
    document.getElementById('anoProcesso').value = <?= db_getsession('DB_anousu') ?>;
    setTimeout(() => {
        (function () {

            var data = new FormData();
            data.append('acao', 'info');
            HttpClient.post('con4_ecidadeinfo.RPC.php', {body: data}).then(function (response) {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                localStorage.setItem("idUsuario", response.usuario.sequencial);
                const andamentoProcesso = new AndamentoProcesso();
                andamentoProcesso.setApiUrl(response.url + 'v4/api/');
                andamentoProcesso.setVisualizarEmOutraJanela(visualizarEmOutraJanela);
                andamentoProcesso.setCodigoInstituicao(response.instituicao.sequencial);
                andamentoProcesso.setCodigoDepartamento(response.departamento.sequencial);
                andamentoProcesso.setWindowAux(new windowAux('windowAux', 'Andamento do processo', 1150, 700));
                andamentoProcesso.setWindowTransferencia(
                    new windowAux('windowTransferencia', 'Transfêrencia', 500, 250)
                );
                andamentoProcesso.setBotaoAtualizar(document.getElementById('atualizar'));
                andamentoProcesso.setBotaoAtualizarAno(document.getElementById('atualizarAno'));
                andamentoProcesso.adicionarAcaoAtualizar();
                andamentoProcesso.exibeGridProcessos(document.getElementById('gridProcessos'));
                andamentoProcesso.buscaOuvidoria(null);
                andamentoProcesso.buscaProcessos(true);
                andamentoProcesso
                    .setFiltroData(document.getElementById('dataInicio'))
                    .setDataAno(document.getElementById('anoProcesso'))
                    .setFiltroDescricao(document.getElementById('descricao'))
                    .setFiltroRequerente(document.getElementById('requerente'))
                    .setFiltroProcesso(document.getElementById('processo'))
                    .setFiltroMensagem(document.getElementById('mensagemCheck'))
                    .addFiltroStatus(document.getElementById('receber'))
                    .addFiltroStatus(document.getElementById('recebido'))
                    .addFiltroStatus(document.getElementById('despachado'))
                    .addFiltroStatus(document.getElementById('externo'))
                    .addFiltroTipoProcesso(document.getElementById('eletronico'))
                    .addFiltroTipoProcesso(document.getElementById('manual'))
                    .addFiltroResponsavel(document.getElementById("usuario"))
                    .addFiltroResponsavel(document.getElementById("departamento"))
                    .addFiltroResponsavel(document.getElementById("outros"));
                andamentoProcesso.filtrar();
            });
        })();
    }, 500);

    window.addEventListener('load', async () => {
        await PHPSession.loadData();
    });

</script>
<style>
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.5);
        }
        100% {
            transform: scale(1);
        }
    }

    .pulsacao {
        animation-name: pulse;
        animation-duration: 1s;
        animation-iteration-count: infinite;
        animation-timing-function: ease-in-out;
    }
</style>
