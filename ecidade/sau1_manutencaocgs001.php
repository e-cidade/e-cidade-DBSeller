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
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

$oGet = db_utils::postMemory($_GET);
$lPermiteAlteracao = db_permissaomenu(db_getsession('DB_anousu'), 1000004, 228479) == "true";
$sDisplay = isset($oGet->lBloqueiaBotoes) ? "style='display: none;'" : "";
$db_acessado = db_getsession("DB_acessado", false);

if (empty($db_acessado)) {
    db_putsession("DB_acessado", "0");
}
?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    $assets = array(
        "scripts.js",
        "prototype.js",
        "estilos.css",
        "AjaxRequest.js",

        /**
         * Abas
         */
        "DBAbas.widget.js",
        "DBAbasItem.widget.js",

        "windowAux.widget.js",
        "dbautocomplete.widget.js",
        "dbmessageBoard.widget.js",
        "dbtextField.widget.js",
        "dbcomboBox.widget.js",
        "dbViewCadEndereco.classe.js",

        /**
         * Inputs
         */
        "Input/DBInput.widget.js",
        "Input/DBInputDate.widget.js",
        "Input/DBInputFoto.widget.js",
        "Input/DBInputEmail.widget.js",
        "Input/DBInputEndereco.widget.js",
        "Input/DBInputTelefone.widget.js",
        "Input/DBInputCpf.widget.js",

        /**
         * Ancora
         */
        "DBAncora.widget.js",
        "DBLookUp.widget.js",
        /**
         * Validador de CNS
         */
        "saude/validaCNS.js",

        /**
         * Grid
         */
        "Collection.widget.js",
        "datagrid.widget.js",
        "DatagridCollection.widget.js",
        "dbViewCadastroDocumento.js",

        "dbtextFieldData.widget.js"

    );
    db_app::load($assets);
    ?>
    <style type="text/css">
        .container > input[type="button"],
        .container > input[type="submit"] {
            width: 80px;
        }

        .container .form-container > tbody > tr > td:first-child {
            width: 200px;
        }

        .aba, .abaAtiva {
            width: 100px;
        }
    </style>
    <script>
        var validacoes = [];
        var callbackCarregamento = {
            dadosPessoais: function (dadosCGS, dadosPadrao) {
                return true;
            },
            // contatos      : function(dadosCGS, dadosPadrao){
            //   return true;
            // },
            // documentos    : function(dadosCGS, dadosPadrao){
            //   return true;
            // },
            // biometria     : function(dadosCGS, dadosPadrao){
            //   return true;
            // },
            outrosDados: function (dadosCGS, dadosPadrao) {
                return true;
            },
        };
    </script>
</head>
<body class="body-default">

<form class="container" style="min-width: 700px">
    <fieldset class="">
        <legend>Manutenção do Cadastro Geral da Saúde<span id="codigoCgs"></span></legend>
        <input id="permite-alteracao" type="hidden" value="<?= $lPermiteAlteracao ?>">
        <div id="ctnAbas">
            <!-- CONTAINER REFERENTE A ABA DADOS PESSOAIS -->
            <div id="ctnAbaDadosPessoais">
                <?php
                require_once("dbforms/db_frmcgsdadospessoais.php"); ?>
            </div>

            <!--
          FOI PEDIDO PARA RETIRAR NO REDMINE 16722

          CONTAINER REFERENTE A ABA CONTATOS
          <div id="ctnAbaContatos">
            <?php
            //require_once("dbforms/db_frmcgscontato.php"); ?>
          </div>

          CONTAINER REFERENTE A ABA DOCUMENTOS
          <div id="ctnDocumentos">
             <?php
            //require_once("dbforms/db_frmcgsdocumentos.php"); ?>
          </div>

          CONTAINER REFERENTE A ABA BIOMETRIA
          <div id="ctnBiometria">
            <?php
            //require_once("dbforms/db_frmcgsbiometria.php"); ?>
          </div>

           CONTAINER REFERENTE A ABA OUTROS DADOS -->
            <div id="ctnOutrosDados">
                <?php
                require_once modification("dbforms/db_frmcgsoutrosdados.php"); ?>
            </div>
        </div>
    </fieldset>
    <input type="button" id="pesquisar" value="Pesquisar" func-arquivo="func_cgs_und.php"
           func-objeto="func_nome" <?= $sDisplay ?> />
    <input type="submit" id="salvarDados" value="Salvar"/>
    <input type="button" id="novo" value="Novo" <?= $sDisplay ?> />
    <input type="button" id="excluir" value="Excluir" disabled <?= $sDisplay ?> />
</form>
</body>
<?php
if (!isset($oGet->lBloqueiaMenu)) {
    db_menu();
}
?>
</html>
<script>
    /**
     * Dados básicos
     */
    const MENSAGENS_MANUTENCAO_CGS = 'saude.ambulatorial.sau1_manutencaocgs001.';
    var oGet = js_urlToObject();
    var sRpc = "sau4_cgs.RPC.php";
    var iCgs = !!oGet.cgs ? oGet.cgs : null;
    var iCodigoCartaoSus = null;

    /**
     * Criação do elemento DBAbas, com as respectivas abas existentes
     */
    var oDBAba = new DBAbas($('ctnAbas'));
    var oAbaDadosPessoais = oDBAba.adicionarAba('Dados Pessoais', $('ctnAbaDadosPessoais'));
    // var oAbaContatos      = oDBAba.adicionarAba('Contatos',       $('ctnAbaContatos'));
    // var oAbaDocumentos    = oDBAba.adicionarAba('Documentos',     $('ctnDocumentos'));
    // var oAbaBiometria     = oDBAba.adicionarAba('Biometria',      $('ctnBiometria'));
    var oAbaOutrosDados = oDBAba.adicionarAba('Outros Dados', $('ctnOutrosDados'));

    $('codigoCgs').innerHTML = '';

    /**
     * Executa cada callback que será implementado nos arquivos do "dbforms"
     */
    function carregaDados(flagTrocaAba) {
        /**
         * Carregamento dos dados
         */
        var parametros = {
            "sExecucao": !!iCgs ? "getDadosCadastroAlteracao" : "getDadosCadastroNovo",
            "cgs": iCgs
        };

        // biometria.foto_nova.setValue('');

        AjaxRequest.create(sRpc, parametros, function (resposta) {

            var informacoesCGS = resposta.informacoesCGS || {};// resposta.informacoesCGS.dados_pessoais : null;


            if (informacoesCGS.dados_pessoais) {
                const cpf = informacoesCGS.dados_pessoais.cpf;
                if (cpf) {
                    informacoesCGS.dados_pessoais.cpf = cpf.substr(0, 3) + "." + cpf.substr(3, 3) + "." + cpf.substr(6, 3) + "-" + cpf.substr(9, 2);
                }
            }

            callbackCarregamento.dadosPessoais(informacoesCGS.dados_pessoais || null, resposta.informacoesPadrao, informacoesCGS.contato || null);
            // callbackCarregamento.contatos( informacoesCGS.contato || null, resposta.informacoesPadrao);
            // callbackCarregamento.biometria( informacoesCGS.biometria || null, resposta.informacoesPadrao);
            callbackCarregamento.outrosDados(informacoesCGS.outros_dados || null, resposta.informacoesPadrao);
            // callbackCarregamento.documentos( null, resposta.informacoesPadrao );

            $('usuarioCadastro').value = trataObjetoUsuarioSistema(informacoesCGS.dados_sistema)
            /**
             * Cria os comportamentos básicos para a tela
             */
            criarComportamentos();

            if (flagTrocaAba && $('Dados Pessoais').classList.contains('abaAtiva')) {
                oAbaDadosPessoais.setVisibilidade(false);
            }
        })

            .setMessage(_M(MENSAGENS_MANUTENCAO_CGS + 'buscando_dados'))

            .execute();
    }

    function trataObjetoUsuarioSistema(dados_sistema) {

        if (dados_sistema === null || dados_sistema === undefined) {

            dados_sistema = {
                usuario: ""
            };

            return "";
        }

        return dados_sistema.usuario;

    }

    /**
     * Cria os comportamentos da tela referentes a botoões e lookups de pesquisa
     */
    function criarComportamentos() {
        /**
         * Botão excluir
         */
        $('excluir').stopObserving('click');
        $('excluir').observe('click', excluirCGS);
        $('excluir').disabled = !iCgs;

        /**
         * Botão novo
         */
        $('novo').stopObserving('click');
        $('novo').observe('click', function () {
            return window.location.href = 'sau1_manutencaocgs001.php';
        });

        /**
         * Aba documentos
         */
        // oAbaDocumentos.bloquear();

        // if(!!iCgs) {
        //   oAbaDocumentos.desbloquear();
        // }

        /**
         * Postagem dos dados
         */
        document.forms[0].stopObserving("submit");
        document.forms[0].observe("submit", enviarFormulario);


        var codigoCGS = document.createElement('input');
        codigoCGS.data = 'z01_i_cgsund';

        var nomeCGS = document.createElement('input');
        nomeCGS.data = 'z01_v_nome';

        var lookup = new DBLookUp($('pesquisar'), codigoCGS, nomeCGS);
        lookup.setCallBack("onClick", function (parametros) {
            window.location.href = 'sau1_manutencaocgs001.php?cgs=' + parametros[0];
        });

        lookup.setObjetoLookUp('db_iframe_cgs_und');
        lookup.setQueryString("&lDesabilitaCgs&aceitaInativo");

        if (document.getElementById('permite-alteracao').value != 1) {
            document.getElementById('excluir').style.display = "none";
            if (iCgs) {
                //   lookup.abrirJanela(true);
                bloquearCampo(document.getElementById('btn-consulta-cns'), false);
                bloquearCampo(document.getElementById('btn-consulta-cpf'), false);
                bloquearCampo(document.getElementById('cadastroInativo'));
                bloquearCampo(document.getElementById('cgsMunicipio'));
                bloquearCampo(document.getElementById('cns'));
                bloquearCampo(document.getElementById('cpf'));
                bloquearCampo(document.getElementById('nome'));
                bloquearCampo(document.getElementById('nomeSocial'));
                bloquearCampo(document.getElementById('nomeMae'));
                bloquearCampo(document.getElementById('desconheceMae'));
                bloquearCampo(document.getElementById('nomePai'));
                bloquearCampo(document.getElementById('desconhecePai'));
                bloquearCampo(document.getElementById('sexo'));
                bloquearCampo(document.getElementById('racaCor'));
                bloquearCampo(document.getElementById('fatorRH'));
                bloquearCampo(document.getElementById('tipoSangue'));
                bloquearCampo(document.getElementById('dataNascimento'));
                bloquearCampo(document.getElementById('dataNascimento').nextElementSibling, false);
                bloquearCampo(document.getElementById('nacionalidade'));
                bloquearCampo(document.getElementById('paisOrigem'));
                bloquearCampo(document.getElementById('contato_email'));
                bloquearCampo(document.getElementById('informacaoObito'));
                bloquearCampo(document.getElementById('escolaridade'));
                bloquearCampo(document.getElementById('z01_numcgm'));
                bloquearCampo(document.getElementById('ed47_i_codigo'));
                bloquearCampo(document.getElementById('ov02_sequencial'));
                bloquearCampo(document.getElementById('rh70_sequencial'));
                bloquearCampo(document.getElementById('z01_c_bolsafamilia'));
                bloquearCampo(document.getElementById('z01_i_estciv'));
                bloquearCampo(document.getElementById('z01_v_micro'));
                bloquearCampo(document.getElementById('z01_i_familiamicroarea'));
                bloquearCampo(document.getElementById('z01_c_nomeresp'));

                const municipio = document.createElement('label');
                municipio.innerText = document.getElementById('colunaMunicipioNascimento').firstChild.text;
                document.getElementById('colunaMunicipioNascimento').removeChild(document.getElementById('colunaMunicipioNascimento').firstChild);
                document.getElementById('colunaMunicipioNascimento').insertBefore(municipio, document.getElementById('colunaMunicipioNascimento').childNodes[0]);

                const cgm = document.getElementById('ancoraCGM');
                const cgmPai = cgm.parentElement;
                const cge = document.getElementById('ancoraCGE');
                const cgePai = cge.parentElement;
                const cidadao = document.getElementById('ancoraCidadao');
                const cidadaoPai = cidadao.parentElement;
                const ocupacao = document.getElementById('ancoraOcupacao');
                const ocupacaoPai = ocupacao.parentElement;

                cgmPai.innerText = cgm.text;
                cgePai.innerText = cge.text;
                cidadaoPai.innerText = cidadao.text;
                ocupacaoPai.innerText = ocupacao.text;

            } else {
                document.getElementById('novo').style.display = "none";
            }
        }
        document.getElementById('contato_endereco_principal').nextElementSibling.removeAttribute('class');
        document.getElementById('contato_endereco_principal').nextElementSibling.removeAttribute('type');
    }

    function bloquearCampo(elemento, isCampo = true) {
        elemento.disabled = true;
        if (isCampo) {
            elemento.style.color = "black";
            elemento.style.backgroundColor = "#deb887";
        }
    }

    /**
     * Responsável por chamar a exclusão de um CGS
     */
    function excluirCGS() {

        if (!confirm('Deseja realmente excluir o CGS: \n\n' + iCgs + ' - ' + dadosPessoais.nome.getValue() + '?')) {
            return;

        }

        var oParametros = {'sExecucao': 'excluirCgs', 'iCgs': iCgs};
        AjaxRequest.create(sRpc, oParametros, function (oRetorno, lErro) {

            alert(oRetorno.sMessage.urlDecode());

            if (lErro) {
                return;
            }

            window.location.href = 'sau1_manutencaocgs001.php';
        }).setMessage(_M(MENSAGENS_MANUTENCAO_CGS + 'excluindo_cgs'))
            .execute();
    }

    /**
     * Responsável por validar se o cpf já existe na base de dados
     */
    function validaCpf(oParametros) {
        oParametros.dados_pessoais.cpf = oParametros.dados_pessoais.cpf.replace('-', '');
        oParametros.dados_pessoais.cpf = oParametros.dados_pessoais.cpf.replaceAll('.', '');
        oParametros.dados_pessoais.cpf = oParametros.dados_pessoais.cpf.replaceAll('_', '');
        if (oParametros.dados_pessoais.cpf != '') {
            oParametrosRequisicao = {
                'sExecucao': 'buscarCgsPorCpfFiltrandoCgs',
                'iCgs': iCgs,
                'cpf': oParametros.dados_pessoais.cpf
            };

            AjaxRequest.create(sRpc, oParametrosRequisicao, function (oRetorno, lErro) {
                if (oRetorno.cpfValido == false && oParametros.dados_pessoais.cpf != '') {
                    alert("CPF já existente na base de dados.");
                } else {
                    salvar(oParametros);
                }
            }).execute();
        } else {
            salvar(oParametros);
        }
    }

    function salvar(oParametros) {
        var oParametrosRequisicao = {'sExecucao': 'salvarCgs', 'iCgs': iCgs};
        oParametrosRequisicao.dados_pessoais = oParametros.dados_pessoais;
        oParametrosRequisicao.contato = oParametros.contato;
        oParametrosRequisicao.contato.telefone_celular = oParametrosRequisicao.contato.telefone_celular.replace('-', '').replace('(', '').replace(')', '').replace(' ', '');
        oParametrosRequisicao.contato.telefone_fixo = oParametrosRequisicao.contato.telefone_fixo.replace('-', '').replace('(', '').replace(')', '').replace(' ', '');
        oParametrosRequisicao.contato.fax = oParametrosRequisicao.contato.fax.replace('-', '').replace('(', '').replace(')', '').replace(' ', '');
        oParametrosRequisicao.outrosDados = oParametros.outrosDados;
        oParametrosRequisicao.outrosDados.observacoes = encodeURIComponent(tagString(oParametros.outrosDados.observacoes));

        AjaxRequest.create(sRpc, oParametrosRequisicao, function (oRetorno, lErro) {
            alert(oRetorno.sMessage.urlDecode());

            if (lErro) {
                return;
            }

            document.getElementById('novo').click();

        }).setMessage(_M(MENSAGENS_MANUTENCAO_CGS + 'salvando_dados'))
            .execute();
    }

    function enviarFormulario(event) {
        event.preventDefault();
        event.stopPropagation();

        var validacao, erro = false;

        for (validacao of validacoes) {

            if (!validacao()) {
                return;
            }
        }

        /**
         * Envia os dados a serem salvos. Cada formulário retorna seus dados, setando os atributos no objeto a ser enviado
         * para o RPC
         * @type {{sExecucao: string}}
         */

        var oParametros = {};

        setValoresDadosPessoais(oParametros);
        setValoresContatos(oParametros);
        setValoresOutrosDados(oParametros);

        validaCpf(oParametros);
    }

    carregaDados(false);
</script>
