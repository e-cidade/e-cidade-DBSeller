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

$aCGM = array();
$sMsg = null;

try {
    $sSqlCGM  = '     select distinct z01_numcgm as cgm,                 ';
    $sSqlCGM .= '            z01_cgccpf||\' - \'||z01_nome as empregador ';
    $sSqlCGM .= '       from rhlota                                      ';
    $sSqlCGM .= ' inner join cgm                                         ';
    $sSqlCGM .= '         on rhlota.r70_numcgm = cgm.z01_numcgm          ';
    $sSqlCGM .= '      where r70_instit = '. db_getsession("DB_instit")   ;
    $sSqlCGM .= '   order by z01_numcgm '                                 ;

    $rsSqlCGM = db_query($sSqlCGM);

    if (!$rsSqlCGM) {
        throw new DBException("Ocorreu um erro ao consultar os CGM vinculados as lotações.\nContate o suporte.");
    }

    if (pg_num_rows($rsSqlCGM) > 0) {
        $aCGM = db_utils::makeCollectionFromRecord($rsSqlCGM, function ($oItemCGM) {
            return (object)array('cgm'=>$oItemCGM->cgm,'empregador'=>$oItemCGM->empregador);
        });
    } else {
        throw new DBException("Desculpe, não encontramos nenhum Empregador vinculado na instituição.\nContate o suporte.");
    }
} catch (Exception $e) {
    $sMsg = $e->getMessage();
}
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
                <input type = "hidden" id = 'preenchimento' value = '' />
                <legend><label for="cgm">Escolha o Empregador</label></legend>
                <select id = 'cgm' style="width:100%" onchange="buscarAvaliacao(event)">
                    <?php
                    if (!empty($aCGM)) {
                        foreach ($aCGM as $oCGM) {
                            ?>
                            <option value="<?php echo $oCGM->cgm; ?>"><?php echo $oCGM->empregador; ?></option>
                            <?php
                        }
                    }
                    ?>
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
        <input type="button" id="proximo"   name="proximo"   value="Próximo"   class="controle" />

    </form>
    <script type="text/javascript">
        var viewAvaliacao = '';
        var iCGMAnterior = '';
        var sRpc = 'eso01_preenchimentolotacaotributaria.RPC.php';
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

        function buscarAvaliacao(event) {
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

            if (!empty(iCGM)) {
                oDados.iCGM = iCGM;
            }

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
                viewAvaliacao.recurarGrupo();
            });

            $('salvar').observe('click', function() {
                salvarQuestionario(viewAvaliacao);
            });
            const codigoLotacao = document.querySelector('input[identificador="codLotacao"]');
            if (codigoLotacao.value != '') {
                  codigoLotacao.disabled = true;
            }
        }

        $('novo').observe('click', function() {
            novoFormulario();
        });


        function salvarQuestionario(viewAvaliacao) {
            var codLotacao = null;
            if(! viewAvaliacao.getStatus().grupoAtual.isValido()) {
                alert("Há informações obrigatórias inconsistentes.\nVerifique.");
                return false;
            }

            /*Pega o codigo de preenchimento, caso exista faz alteração, senão cria novo registro*/
            preenchimento = $('preenchimento').value;

            viewAvaliacao.grupos.itens.each(function(grupo){
                if (grupo.identificador_campo == 'ideLotacao') {
                    grupo.perguntas.itens.each(function(pergunta) {
                        if (pergunta.identificador_campo == 'codLotacao') {
                            pergunta.elemento.down("input").value = pergunta.elemento.down("input").value;
                            codLotacao = pergunta.elemento.down("input").value;
                        }
                    });
                }
            });

            AjaxRequest.create(
                'eso01_preenchimentolotacaotributaria.RPC.php',
                {
                    exec                  : 'salvarAvaliacao',
                    iCGM                  : iCGMAnterior,
                    iCodigoAvaliacao      : viewAvaliacao.codigo,
                    codLotacao            : codLotacao,
                    iCodigoPreenchimento  : preenchimento,
                    aPerguntasRespostas   : viewAvaliacao.getDados()
                },
                function(oResponse, lErro){
                    if (!iCodigoGrupo || lErro) {
                        alert(oResponse.mensagem);
                    }
                    if (lErro) {
                        return ;
                    }
                    viewAvaliacao.avancarGrupo();
                }
            ).setMessage('Salvando dados...').execute();
            return true;
        }
        $('pesquisar').addEventListener('click', function () {
            var iCgm = $F('cgm');
            var sUrl = 'func_rhlotacaotributaria.php';
            sUrl += '?chave_rh268_numcgm='+iCgm+'&instituicao='+instituicao+'&funcao_js=parent.buscaRespostas|db_preenchimento|eso04_cgm|';

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_avaliacaogruporespostalotacaotributaria', sUrl, 'Pesquisar Lotação Tributária por Empregador', true);
        });

        function buscaRespostas(preenchimento, cgm, processo, empregador) {
            db_iframe_avaliacaogruporespostalotacaotributaria.hide();
            $('preenchimento').value = preenchimento;
            $('cgm').value = cgm;
            buscarAvaliacao();
        }

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

            $('proximo').disabled  = true;
            $('anterior').disabled = true;
            $('salvar').disabled   = true;

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
        function novoFormulario() {
            if ($F('cgm') != '') {
                if(confirm("Se você criar um NOVO, os dados que não foram salvos serão perdidos.\nTem certeza que deseja continuar?")) {
                    $('preenchimento').value = '';
                    buscarAvaliacao();
                } else {
                    return false;
                }
            }
        }
    </script>
    <?php
    db_menu();
    if (!empty($sMsg)) {
        db_msgbox($sMsg);
    }
    ?>
</body>
