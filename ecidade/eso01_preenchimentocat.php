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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

db_postmemory($_POST);
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
        db_app::load("Input/DBInputCheckboxRadio.widget.js, Input/DBCheckBox.widget.js,Input/DBRadio.widget.js,Collection.widget.js");
        db_app::load("avaliacao/DBViewFormulario.classe.js, avaliacao/DBViewGrupoPerguntas.classe.js,avaliacao/DBViewPergunta.classe.js,avaliacao/DBViewResposta.classe.js,awesomplete.js,avaliacao/DBAutoComplete.js,classes/eSocial/DBAutoCompleteEsocial.js, avaliacao/DBViewRespostaNula.classe.js");
        db_app::load("AjaxRequest.js,estilos.css,grid.style.css,avaliacao.css,awesomplete.css");
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
            <legend><label for="cgm">Escolha o Empregador</label></legend>
            <input type="hidden" id='preenchimento' value='' />
            <select id = 'cgm' style="width:100%" onchange='buscarAvaliacao();'>
                <option value="">Selecione o empregador</option>
            </select>
        </fieldset>
        <fieldset>
        <legend>Formulário de Cadastro para o eSocial</legend>
            <div id="questionario"></div>
        </fieldset>
        <input type="button" id="anterior"  name="anterior"  value="Anterior"  class="controle" />
        <input type="button" id="novo"      name="novo"      value="Novo"      class="controle" disabled />
        <input type="button" id="salvar"    name="salvar"    value="Salvar"    class="controle" disabled />
        <input type="button" id="pesquisar" name="pesquisar" value="Pesquisar" class="controle" />
        <input type="button" id="excluir" name="excluir" value="Excluir" class="controle" />
        <input type="button" id="proximo"   name="proximo"   value="Próximo"   class="controle" />
    </form>
    <script type="text/javascript">
        var viewAvaliacao = '';
        var iCGMAnterior = '';
        var sRpc = 'eso01_preenchimentocat.RPC.php';
        var instituicao = '';
        (function() {
            instituicao = <?=db_getsession("DB_instit")?>;
            var parametros = {'exec' : 'getEmpregadores', 'instituicao' : instituicao};

            new AjaxRequest('eso4_esocialapi.RPC.php', parametros, function (retorno) {
                if (retorno.erro) {
                    alert("Desculpe, não encontramos nenhum Empregador vinculado na instituição.\nContate o suporte.");
                    return;
                }

                $('cgm').options.length = 0;
                $('cgm').add(new Option('Selecione o empregador', ''));
                for(var empregador of retorno.empregadores) {
                    var nome = empregador.documento + ' - ' + empregador.nome;
                    $('cgm').add(new Option(nome, empregador.cgm));
                }

                if (retorno.empregadores.length == 1) {
                    $('cgm').value = retorno.empregadores[0].cgm;
                    buscarAvaliacao();
                }
            }).setMessage('Buscando empregadores.').execute();
        })();

        function buscarAvaliacao() {
            if ($F('cgm') == '') {
                $('salvar').disabled = true;
                $('novo').disabled = true;
                $('questionario').innerHTML = '';
                $('preenchimento').value = '';
                return false;
            }

            if (!empty(iCGMAnterior) && iCGMAnterior != $F('cgm')) {
                if(!confirmaSaida("Se você trocar de empregador os dados que não foram salvos serão perdidos.\nTem certeza que deseja trocar?")) {
                    $('cgm').value = iCGMAnterior;
                    return false;
                }
                $('preenchimento').value = '';
            }

            iCGMAnterior = $F('cgm');
            removeEventoBotoes();
            $('salvar').disabled = false;
            $('novo').disabled = false;
            $('questionario').innerHTML = '';

            var iCGM   = $F('cgm');
            var oDados = {
                'exec' : 'buscarAvaliacao',
                'iCGM' : $F('cgm'),
                'preenchimento' : $F('preenchimento')
            };

            AjaxRequest.create(sRpc, oDados, montarAvaliacao)
                .setMessage('Buscando dados...')
                .execute();
        }

        function montarAvaliacao(oResponse, lErro) {
            if (lErro) {
                alert(oResponse.mensagem);
            }

            viewAvaliacao = DBViewFormulario.makeFromObject(oResponse.oFormulario)
                .setEvent('changeStep', controlarBotoes)
                .show($('questionario'));

            DBViewRespostaNula.adicionaRespostaNula(viewAvaliacao);

            $('proximo').observe('click', function() {
                this.blur();
                viewAvaliacao.avancarGrupo();
            });

            $('anterior').observe('click', function() {
                var sMensagem = "As informações preenchidas, poderão ser perdidas.\n";
                sMensagem    += "Tem certeza que deja voltar?";

                if(confirm(sMensagem)) {
                    viewAvaliacao.recurarGrupo();
                }
            });

            $('salvar').observe('click', function() {
                salvarQuestionario(viewAvaliacao);
            });

            const nrInsc = document.querySelector('input[identificador="nrInsc"]');

            $(nrInsc).observe('change', function() {
                nrInsc.value = nrInsc.value.replace(/[^0-9]/g, '');
            })
        }

        $('novo').observe('click', function() {
            novoFormulario();
        });

        function removeEventoBotoes() {
            $('salvar').stopObserving('click');
            $('proximo').stopObserving('click');
            $('anterior').stopObserving('click');
        }

        function confirmaSaida (sMensagem) {
            if(typeof sMensagem == 'undefined' || sMensagem == null || sMensagem == false) {
                sMensagem = 'Você está saindo do cadastro do e-social.\nAntes de sair, salve seus dados.';
            }

            if (!confirm(sMensagem)) {
                return false;
            }
            return true;
        }

        var controlarBotoes = function(event) {
            DBAutoCompleteEsocial.gerarAutoComplete();
            DBViewRespostaNula.adicionaRespostaNula(viewAvaliacao);
            var status = this.getStatus();

            $('proximo').disabled = true;
            $('anterior').disabled = true;
            $('salvar').disabled = true;
            $('novo').disabled = true;

            if (status.grupoPosterior) {
                $('proximo').disabled = false;
            }

            if (status.grupoAnterior) {
                $('anterior').disabled = false;
            }

            if (status.grupoAtual) {
                $('salvar').disabled = false;
                $('novo').disabled = false;
            }
        };

        function salvarQuestionario(viewAvaliacao) {
            var matricula = null;
            var cpf = null;
            var dataAcidente = null;

            if(!viewAvaliacao.getStatus().grupoAtual.isValido()) {
                alert("Há informações obrigatórias inconsistentes.\nVerifique.");
                return false;
            }

            /*Pega o codigo de preenchimento, caso exista faz alteração, senão cria novo registro*/
            preenchimento = $('preenchimento').value;

            viewAvaliacao.grupos.itens.each(function(grupo){
                if (grupo.identificador_campo == 'ideVinculo' || grupo.identificador_campo == 'cat') {
                    grupo.perguntas.itens.each(function(pergunta) {
                        if (pergunta.identificador_campo == 'matricula') {
                            pergunta.elemento.down("input").value = pergunta.elemento.down("input").value.replace(/[^0-9]/g, '');
                            matricula = pergunta.elemento.down("input").value;
                        }
                        if (pergunta.identificador_campo == 'cpfTrab') {
                            pergunta.elemento.down("input").value = pergunta.elemento.down("input").value.replace(/[^0-9]/g, '');
                            cpf = pergunta.elemento.down("input").value;
                        }
                        if (pergunta.identificador_campo == 'dtAcid') {
                            dataAcidente = pergunta.elemento.down("input").value;
                        }
                    });
                }
            });
            AjaxRequest.create(
                sRpc,
                {
                    exec : 'salvarAvaliacao',
                    iCGM : iCGMAnterior,
                    iCodigoAvaliacao : viewAvaliacao.codigo,
                    matricula : matricula,
                    cpf : cpf,
                    dataAcidente : dataAcidente,
                    iCodigoPreenchimento : preenchimento,
                    aPerguntasRespostas : viewAvaliacao.getDados()
                },
                function(oResponse, lErro){
                    if (lErro) {
                        alert(oResponse.mensagem);
                        return ;
                    }

                    $('preenchimento').value = oResponse.preenchimento;
                    alert(oResponse.mensagem);
                    viewAvaliacao.avancarGrupo();
                }
            ).setMessage('Salvando dados...').execute();
            return true;
        }


        $('pesquisar').addEventListener('click', function () {
            var iCgm = $F('cgm');
            var sUrl = 'func_avaliacaogruporespostacat.php';
            sUrl += '?chave_eso35_empregador='+iCgm+'&instituicao='+instituicao+'&funcao_js=parent.buscaRespostas|db_preenchimento|eso36_empregador';

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_avaliacaogruporespostaocat', sUrl, 'Pesquisar Comunicados de Acidente de Trabalho por Empregador', true);
        });

        function buscaRespostas(preenchimento, cgm) {
            db_iframe_avaliacaogruporespostaocat.hide();
            $('preenchimento').value = preenchimento;
            $('cgm').value = cgm;
            buscarAvaliacao();
        }

        function novoFormulario() {
            if ($F('cgm') != '') {
                if(confirm("Se você criar um Processo Administrativo/Judicial novo, os dados que não foram salvos serão perdidos.\nTem certeza que deseja continuar?")) {
                    $('preenchimento').value = '';
                    buscarAvaliacao();
                } else {
                    return false;
                }
            }
        }

        /**
         * Exclui o dados da Resposta
         */
        function excluir() {
            id = $('preenchimento').value;
            if (empty(id)) {
                alert('Nenhum CAT foi selecionado.');
                return false;
            }

            if (!confirm('Confirma a exclusão do CAT do Servidor?')) {
                return;
            }
            var request = {
                exec: 'remover',
                formulario: viewAvaliacao.codigo,
                codigo_resposta: id
            };

            new AjaxRequest(sRpc, request, function(response, erro) {
                alert(response.mensagem);
                if (erro) {
                    return;
                }

                window.location.href = 'eso01_preenchimentocat.php';

                $('limpar').click();
                iCodigoRespostaFormulario = '';
            }).setMessage('Aguarde, removendo resposta ...').execute();
        }
        $('excluir').observe('click', function() {
            excluir();
        });
    </script>
    <?php db_menu(); ?>
</body>
