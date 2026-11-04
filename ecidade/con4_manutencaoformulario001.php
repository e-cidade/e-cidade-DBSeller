<?php
/*
*     E-cidade Software Publico para Gestao Municipal
*  Copyright (C) 2009  DBSeller Servicos de Informatica
*                    www.dbseller.com.br
*                 e-cidade@dbseller.com.br
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
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

use ECidade\RecursosHumanos\ESocial\Model\Configuracao;

$rh01_regist = '';
$matricula = null;
$lTrazerSugestoes = false;
db_postmemory($_GET);

if (!empty($_GET['esocial'])) {

    try {
        $esocial = new Configuracao();
        $_GET["formulario"] = $esocial->getFormulario($_GET['esocial']);
    } catch (Exception $e) {
        db_redireciona(sprintf('db_erros.php?fechar=true&db_erro=%s', $e->getMessage()));
    }
}

if (empty($_GET["formulario"])) {

    db_redireciona('con4_manutencaoformularios.php');
    exit;
}
$formulario = \ECidade\Configuracao\Formulario\Repository\Formulario::getById($_GET["formulario"]);
if (empty($formulario)) {

    db_redireciona('con4_manutencaoformularios.php');
    exit;
}
$descricao = $formulario->getNome();

?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js");
    db_app::load("prototype.js");
    db_app::load("object.js");

    db_app::load("Input/DBInput.widget.js, DBInputHora.widget.js, Input/DBInputCep.widget.js,Input/DBInputCNPJ.js,Input/DBInputCpf.widget.js,Input/DBInputDate.widget.js");
    db_app::load("Input/DBInputInteger.widget.js, Input/DBInputTelefone.widget.js,Input/DBInputValor.widget.js");
    db_app::load("Input/DBInputCheckboxRadio.widget.js, Input/DBCheckBox.widget.js,Input/DBRadio.widget.js,Collection.widget.js, Input/DBSelect.widget.js");
    db_app::load("avaliacao/DBViewFormulario.classe.js, avaliacao/DBViewGrupoPerguntas.classe.js, avaliacao/DBViewPergunta.classe.js, avaliacao/DBViewResposta.classe.js, avaliacao/DBViewRespostaNula.classe.js");
    db_app::load("AjaxRequest.js,estilos.css,grid.style.css,avaliacao.css");
    db_app::load("widgets/windowAux.widget.js");
    db_app::load("widgets/dbmessageBoard.widget.js");
    db_app::load("HTTPRequest.js");
    db_app::load("Collection.widget.js");
    db_app::load("datagrid.widget.js");
    db_app::load("widgets/DatagridCollection.widget.js");
    db_app::load("awesomplete.js,avaliacao/DBAutoComplete.js,classes/eSocial/DBAutoCompleteEsocial.js");
    db_app::load("awesomplete.css");
    db_postmemory($_POST);

    if (!empty($rh01_regist)) {
        $matricula = $rh01_regist;
    }
    ?>
    <style>
        .controle {
            width: 80px;
        }

        #anterior {
            margin-left: 2px;
            float: left;
        }

        #proximo {
            margin-right: 2px;
            float: right;
        }

        .db-tooltip {
            display: none;
        }
    </style>
</head>
<body>

<form class="container" style="width: 800px;">
    <fieldset>
        <legend>Formulário de Cadastro <?= $descricao ?></legend>
        <div id="questionario"></div>
    </fieldset>
    <input type="hidden" id="iCodigoResposta"/>
    <input type="button" id="anterior" name="anterior" value="Anterior" class="controle"/>
    <input type="button" id="limpar" name="limpar" value="Limpar" class="controle"/>
    <input type="button" id="salvar" name="salvar" value="Salvar" class="controle"/>
    <input type="button" id="excluir" name="excluir" value="Excluir" class="controle"/>
    <input type="button" id="pesquisar" name="Pesquisar" value="Pesquisar" class="controle"/>
    <input type="button" id="proximo" name="proximo" value="Próximo" class="controle"/>
</form>
</body>
</html>
<?php
db_menu();
?>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script>

    var viewAvaliacao = null;
    var instituicao = <?=db_getsession('DB_instit')?>;

    /**
     * Criar como Componente
     */
    (function (exports) {

        const URL_RPC = 'con4_manutencaoformulario.RPC.php';
        var oGet = js_urlToObject();
        oGet.formulario = <?php echo $_GET["formulario"]?>;
        iCodigoFormulario = oGet.formulario;

        var pagina = 0;
        var totalPaginas = 0;
        var janelaCarregada = false;
        var iCodigoRespostaFormulario = '';
        var lFormularioCarregado = false;
        var iRespostaSelecionada = null;
        var camposDescricaoVinculo = [];

        function getDadosFormulario(iCodigoResposta) {

            iRespostaSelecionada = iCodigoResposta;
            $('questionario').innerHTML = '';
            if (empty(iCodigoResposta)) {
                iCodigoResposta = '';
            }
            iCodigoRespostaFormulario = iCodigoResposta;

            var iCodigoFormulario = oGet.formulario;
            var request = {
                'exec': 'getDadosFormulario',
                'formulario': iCodigoFormulario,
                'codigo_resposta': iCodigoResposta
            };
            var oAjaxRequest = new AjaxRequest(URL_RPC, request, montarAvaliacao);
            oAjaxRequest.setMessage('Buscando dados do formulario...');
            oAjaxRequest.execute();
        }

        function montarAvaliacao(oResponse, lErro) {
            if (lErro) {
                alert(oResponse.mensagem);
                return;
            }

            camposDescricaoVinculo = oResponse.aVinculos;

            viewAvaliacao = DBViewFormulario.makeFromObject(oResponse.oFormulario, 'select')
                .setEvent('changeStep', controlarBotoes)
                .show($('questionario'));

            if (lFormularioCarregado) {
                return;
            }
            $('proximo').observe('click', function () {

                this.blur();
                viewAvaliacao.avancarGrupo();
            });

            $('anterior').observe('click', function () {
                viewAvaliacao.recurarGrupo();
            });

            $('salvar').observe('click', function () {
                salvarFormulario(viewAvaliacao, iCodigoRespostaFormulario, false);
            });

            $('limpar').observe('click', function () {

                if (viewAvaliacao.getStatus().grupoAtual) {
                    viewAvaliacao.getStatus().grupoAtual.limparRespostas();
                }
                iCodigoRespostaFormulario = '';
            });

            $('pesquisar').observe('click', function () {
                pesquisar(iCodigoFormulario, instituicao);
            });

            $('excluir').observe('click', function () {
                excluir(iCodigoRespostaFormulario);
            });

            lFormularioCarregado = true;

            pesquisar(iCodigoFormulario, instituicao);
        }

        /**
         * Controla a navegação dos botoes proximo/anterior
         */
        controlarBotoes = function (event) {

            DBAutoCompleteEsocial.gerarAutoComplete();
            DBViewRespostaNula.adicionaRespostaNula(viewAvaliacao);

            var camposDescricao = getCamposDescricao(iRespostaSelecionada);
            camposDescricao.forEach(function (campo) {
                camposDescricaoVinculo.forEach(function (vinculo) {
                    if (vinculo.identificadorCampo == campo.identificadorCampo) {
                        var fCampo = document.querySelector("[identificador=" + campo.identificadorCampo + "]");
                        if (!isNaN(fCampo.value)) {
                            if (fCampo) { fCampo.value = vinculo.descricao; }
                        }
                    }
                });
            });

            var fInstituicao = document.querySelector("input[identificador=instituicao]");
            if (fInstituicao) {
                fInstituicao.closest("fieldset").style.display = "none";
            }

            var status = this.getStatus();

            $('proximo').disabled = true;
            $('anterior').disabled = true;
            $('salvar').disabled = true;

            if (status.grupoPosterior) {
                $('proximo').disabled = false;
            }

            if (status.grupoAnterior) {
                $('anterior').disabled = false;
            }

            if (status.grupoAtual) {
                $('salvar').disabled = false;
            }
        };

        /**
         *
         * @param viewAvaliacao
         * @param iCodigoGrupo
         * @returns {boolean}
         */
        salvarFormulario = function (viewAvaliacao, iCodigoGrupo, avancar) {

            if (!viewAvaliacao.getStatus().grupoAtual.isValido()) {
                alert("Há informações obrigatórias inconsistentes.\nVerifique.");
                return false;
            }

            var iCodigoResposta = iCodigoGrupo ? iCodigoGrupo : $F('iCodigoResposta');
            var lRetorno = true;
            var oAjaxRequest = new AjaxRequest(
                'con4_manutencaoformulario.RPC.php',
                {
                    exec: 'salvar',
                    formulario: viewAvaliacao.codigo,
                    codigo_resposta: iCodigoResposta,
                    aPerguntasRespostas: viewAvaliacao.getDados(iCodigoGrupo),
                    camposDescricao: getCamposDescricao(iCodigoResposta)
                },

                function (oResponse, lErro) {

                    alert(oResponse.mensagem);
                    if (lErro) {
                        lRetorno = false;
                        return;
                    }

                    $('iCodigoResposta').value = oResponse.iCodigoResposta;

                    if (avancar) {
                        viewAvaliacao.avancarGrupo();
                    }
                }
            );

            oAjaxRequest.setMessage('Salvando dados...');
            oAjaxRequest.execute();
            return lRetorno;
        }

        /**
         * Realiza a pesquisa do formulário
         */
        pesquisar = function (iCodigFormulario, instituicao) {

            /**
             * Janela de pesquisa já foi criada.
             */
            if (janelaCarregada) {

                $('filtro').value = '';
                pagina = 1
                janelaPesquisa.show();
                pesquisarDados(null, false, pagina, iCodigoFormulario, instituicao);
                return;
            }
            ;

            janelaPesquisa = new windowAux('wndPesquisarDados', 'Pesquisar Dados', 1200, 650);

            var sContent = "<div style='width:1190px'>";
            sContent += "  <div class='container'>";
            sContent += "    <fieldset>";
            sContent += "      <legend>Filtro para Pesquisa";
            sContent += "      </legend>";
            sContent += "      <table>";
            sContent += "        <tr>";
            sContent += "          <td>";
            sContent += "            <label for='filtro'><b>Filtro:</b></label>";
            sContent += "          </td>";
            sContent += "          <td>";
            sContent += "            <input id='filtro' size='30'>";
            sContent += "          </td>";
            sContent += "        <tr>";
            sContent += "      </table>";
            sContent += "    </fieldset>";
            sContent += "    <input type='button' value='Pesquisar' id='btnPesquisarDados'>";
            sContent += "  </div>";
            sContent += "  <div>";
            sContent += "    <fieldset>";
            sContent += "      <legend>Respostas</legend>";
            sContent += "      <div id='divGridContainer'></div>";
            sContent += "    </fieldset>";
            sContent += "    <div class='container'>";
            sContent += "      <input type='button' disabled value='Anterior' id='btnPaginaAnterior'>";
            sContent += "      <span style='background-color:white;font-weight: bold'>";
            sContent += "         Página <span id='numeroPagina'>" + pagina + "</span>";
            sContent += "         de <span id='numeroTotalPaginas'>" + totalPaginas + "</span>";
            sContent += "      </span>";
            sContent += "      <input type='button' value='Proxima' id='btnProximaPagina'>";
            sContent += "    </div>";
            sContent += "  </div>";
            sContent += "</div>";
            janelaPesquisa.setContent(sContent);

            var oMessageBoard = new DBMessageBoard('msgboard1', 'Pesquisa de dados do Formulário', '', janelaPesquisa.getContentContainer());
            oMessageBoard.show();

            $('btnProximaPagina').observe('click', function () {

                console.log('next', pagina, totalPaginas);
                if (pagina == totalPaginas) {
                    $('btnProximaPagina').disabled();
                } else {
                    pagina++;
                }

                oCollectionDados.clear();
                pesquisarDados($F('filtro'), false, pagina, iCodigoFormulario, instituicao);
            });

            $('btnPaginaAnterior').observe('click', function () {

                pagina--;
                if (pagina < 1) {
                    pagina = 1;
                }
                oCollectionDados.clear();
                pesquisarDados($F('filtro'), false, pagina, iCodigoFormulario, instituicao);
            });

            $('btnPesquisarDados').observe('click', function () {

                pagina = 1;
                oCollectionDados.clear();
                pesquisarDados($F('filtro'), false, pagina, iCodigoFormulario, instituicao);
            });
            oCollectionDados = new Collection().setId('id_grupo_resposta');
            exports.oCollectionDados = oCollectionDados;

            oDataGridDadosFormulario = new DatagridCollection(oCollectionDados).configure({
                'order': false,
                'height': 300
            });
            oDataGridDadosFormulario.setEvent('onclickrow', function (item) {

                getDadosFormulario(item.ID);
                janelaPesquisa.hide();
            });
            janelaPesquisa.show();
            janelaCarregada = true;
            pesquisarDados(null, true, pagina, iCodigoFormulario, instituicao);
        }

        /**
         * Exclui o dados da Resposta
         */
        function excluir(id) {

            if (empty(id)) {
                alert('Nenhum formulário selecionado.');
                return false;
            }

            if (!confirm('Confirma a exclusão dos dados?')) {
                return;
            }
            var request = {
                exec: 'remover',
                formulario: iCodigoFormulario,
                codigo_resposta: id
            };

            new AjaxRequest(URL_RPC, request, function (response, erro) {

                alert(response.mensagem);
                if (erro) {
                    return;
                }

                window.location.reload();

                $('limpar').click();
                iCodigoRespostaFormulario = '';
            }).setMessage('Aguarde, removendo resposta ...').execute();
        }

        function getCamposDescricao(iCodigoResposta) {
            var camposDescricao = [];

            var fCBO = document.querySelector("[identificador=codCBO_descricao]");
            if (fCBO) {
                camposDescricao.push({
                    identificadorResposta: iCodigoResposta,
                    identificadorCampo: 'codCBO_descricao',
                    descricao: fCBO.value
                });
            }

            return camposDescricao;
        }

        /**
         * Pesquisa os dados da grid
         * @param data
         * @param buildGrid
         * @param pagina
         * @param iCodigoFormulario
         */
        function pesquisarDados(data, buildGrid, pagina, iCodigoFormulario, instituicao) {

            oCollectionDados.clear();
            js_divCarregando('Aguarde, pesquisando dados...', 'msgDiv');
            var request = new HTTPRequest();
            filter = null;
            if (!empty(data)) {
                filter = [{
                    'chave': 'filtro',
                    'valor': [data],
                }];
            }
            if (empty(pagina) || pagina < 1) {
                pagina = 1;
            }

            $('btnPaginaAnterior').enable();
            if (pagina == 1) {
                $('btnPaginaAnterior').disable();
            }
            $('numeroPagina').innerHTML = pagina;
            
            let rota = 'configuracao/formulario/' + iCodigoFormulario + '/instituicao/' + instituicao;
            //let rota = 'configuracao/formulario'
            let formData = new FormData();
            formData.append('filter', filter);
            formData.append('number_page', pagina);
            formData.append('size_page', 15);
            let URL  ="<?= ECIDADE_REQUEST_PATH ?>v4/api/"+rota;
           
            HttpClient.post(URL, {body: formData}).then(response => {
                if (response.error) {
                    js_removeObj('msgDiv');
                    alert(response.message);
                } else {
                    let data = response.data;
                    totalPaginas = data.meta.pagination.total;
                    $('numeroTotalPaginas').innerHTML = totalPaginas;

                    js_removeObj('msgDiv');
                    if (buildGrid) {

                        var iTamanho = (100 / data.meta.fields.length);

                        for (field of data.meta.fields) {
    
                            if (field.identificador == 'id_grupo_resposta') {
                                continue;
                            }
                            var alinhamento = 'left';
                            switch (field.tipo) {
                                case '8':
                                case '6':
                                    alinhamento = 'right';
                                    break;
                                case '2':
                                case '3':
                                case '4':
                                case '5':
                                case '9':
                                    alinhamento = 'center';
                                    break;
                            }
                            // Diminui perguntas extensas para nao quebrar a grid
                            descricao = field.descricao;
    
                            if (descricao.length > 25) {
                                descricao = descricao.substr(0, 25) + '...';
                            }
                            oDataGridDadosFormulario.addColumn(field.identificador, {
                                'label': descricao,
                                'width': iTamanho + "%",
                                'align': alinhamento
                            });
                        }
                    }
                    $('btnProximaPagina').enable();
                    if (data.data.length < 15) {
                        $('btnProximaPagina').disable();
                    }
                    for (dados of (data.data)) {
                        oCollectionDados.add(dados);
                    }
                    if (buildGrid) {
                        oDataGridDadosFormulario.show($('divGridContainer'));
                    }
                    oDataGridDadosFormulario.reload();

                }
            });

        }

        exports.controlarBotoes = controlarBotoes;
        exports.pesquisar = pesquisar;
        exports.salvarFormulario = salvarFormulario;
        getDadosFormulario();

    })(window);


    var sUrl = js_urlToObject();

</script>
