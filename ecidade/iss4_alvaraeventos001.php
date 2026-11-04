<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
?>
<html>
<head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBInputHora.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBAncora.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body class="body-default">
    <div class="container">
        <form id="frmAlvaraEventos" name="frmAlvaraEventos">
            <fieldset>
                <legend>Liberação de Alvará de Eventos</legend>
                <table>
                    <tr>
                        <td>
                            <label for="q170_codigo"><b>Código:</b></label>
                        </td>
                        <td>
                            <input id="q170_codigo" name="q170_codigo" type="text" data="q170_codigo" class="field-size2 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a id="ancoraOrdemServico" href="#">
                                <label for="q170_ordemservico">Ordem de serviço:</label>
                            </a>
                        </td>
                        <td>
                            <input id="q170_ordemservico" name="q170_ordemservico" type="text" data="q168_codigo" class="field-size2"/>
                            <input id="descricaoOrdemServico" name="descricaoOrdemServico" type="text" data="q168_descricao" class="field-size7 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a id="ancoraTipoEvento" href="#">
                                <label for="q168_tipoevento">Tipo de Alvará:</label>
                            </a>
                        </td>
                        <td colspan="3">
                            <input id="q170_tipoalvara" name="q170_tipoalvara" type="text" data="q98_sequencial" class="field-size2"/>
                            <input id="descricaoTipoEvento" name="descricaoTipoEvento" type="text" data="q98_descricao" class="field-size7 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <fieldset>
                                <legend>Dados da Ordem de Serviço</legend>
                                <table>
                                    <tr>
                                        <td>
                                            <label for="q168_cgm"><b>CGM:<b></label>
                                        </td>
                                        <td colspan="3">
                                            <input id="q168_cgm" name="q168_cgm" type="text" data="z01_numcgm" class="field-size2 readonly" disabled="disabled"/>
                                            <input id="descricaoCGM" name="descricaoCGM" type="text" data="z01_nome" class="field-size7 readonly" disabled="disabled"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q168_inscricao"><b>Inscrição:</b></label>
                                        </td>
                                        <td colspan="3">
                                            <input id="q168_inscricao" name="q168_inscricao" type="text" data="q02_inscr" class="field-size2 readonly" disabled="disabled"/>
                                            <input id="descricaoInscricao" name="descricaoInscricao" type="text" data="z01_nome" class="field-size7 readonly" disabled="disabled"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q168_descricao"><b>Descrição:</b></label>
                                        </td>
                                        <td colspan="3">
                                            <input id="q168_descricao" name="q168_descricao" type="text" class="field-size9 readonly" disabled="disabled"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q168_localizacao"><b>Localização:</b></label>
                                        </td>
                                        <td colspan="3">
                                            <input id="q168_localizacao" name="q168_localizacao" type="text" class="field-size9 readonly" disabled="disabled"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q168_dataemissao"><b>Data de emissão:</b></label>
                                        </td>
                                        <td colspan="3">
                                            <input id="q168_dataemissao" name="q168_dataemissao" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q168_datainicio"><b>Data de início:</b></label>
                                        </td>
                                        <td>
                                            <input id="q168_datainicio" name="q168_datainicio" disabled="disabled"/>
                                        </td>
                                        <td>
                                            <label for="q168_horainicio"><b>Hora de início:</b></label>
                                        </td>
                                        <td>
                                            <input id="q168_horainicio" name="q168_horainicio" class="field-size1 readonly" disabled="disabled" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q168_datafim"><b>Data de fim:</b></label>
                                        </td>
                                        <td>
                                            <input id="q168_datafim" name="q168_datafim" disabled="disabled"/>
                                        </td>
                                        <td>
                                            <label for="q168_horafim"><b>Hora de fim:</b></label>
                                        </td>
                                        <td>
                                            <input id="q168_horafim" name="q168_horafim" class="field-size1 readonly" disabled="disabled" />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="q170_certidaobombeiro"><b>Alvará de bombeiro:<b></label>
                        </td>
                        <td>
                            <input id="q170_certidaobombeiro" name="q170_certidaobombeiro" type="text" class="field-size9"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="q170_dataemissao"><b>Data de emissão:</b></label>
                        </td>
                        <td>
                            <input id="q170_dataemissao" name="q170_dataemissao" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="q170_estimativapublico"><b>Estimativa de público:<b></label>
                        </td>
                        <td>
                            <input id="q170_estimativapublico" name="q170_estimativapublico" type="text" class="field-size2"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="mensagempadrao"><b>Mensagem padrão:<b></label>
                        </td>
                        <td>
                            <select id="mensagempadrao" name="mensagempadrao" type="select" class="field-size2">
                                <option value="0" selected>Selecionar...</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <fieldset class="separator">
                                <legend>Observação</legend>
                                <textarea id="q170_observacao"></textarea>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" name="salvar" id="salvar" value="Salvar">
            <input type="button" name="limpar" id="limpar" value="Limpar">
            <input type="button" name="pesquisar" id="pesquisar" value="Pesquisar">
            <input type="button" name="imprimir" id="imprimir" value="Imprimir" disabled>
        </form>
    </div>
</body>
<script type="text/javascript">
    const
        url = "<?php echo ECIDADE_REQUEST_PATH;?>",
        apiUrl = `${url}v4/api/`,
        dataAtual = new Date(),
        q170_dataemissao = new DBInputDate($('q170_dataemissao')),
        q168_dataemissao = new DBInputDate($('q168_dataemissao')),
        q168_datainicio = new DBInputDate($('q168_datainicio')),
        q168_datafim = new DBInputDate($('q168_datafim')),
        q168_horainicio = new DBInputHora($('q168_horainicio')),
        q168_horafim = new DBInputHora($('q168_horafim')),
        selectMensagemPadrao =  $('mensagempadrao'),
        inputObservacao = $('q170_observacao');
    let
        mensagens = [];

    q170_dataemissao.setReadOnly(true);
    q168_dataemissao.setReadOnly(true);
    q168_datainicio.setReadOnly(true);
    q168_datafim.setReadOnly(true);

    $('q170_dataemissao').classList.add('field-size2');
    $('q168_dataemissao').classList.add('field-size2');
    $('q168_datainicio').classList.add('field-size2');
    $('q168_datafim').classList.add('field-size2');

    carregarMensagensPadroes();

    selectMensagemPadrao.addEventListener('change', event => {
        if(mensagens[event.target.value] != 0){
            inputObservacao.value += ` ${mensagens[event.target.value]}`;
        }
    });

    var lookupOrdemServico = new DBLookUp(
        $('ancoraOrdemServico'),
        $('q170_ordemservico'),
        $('descricaoOrdemServico'),
        {
          'sArquivo': 'func_ordemservico.php',
          'sLabel': 'Pesquisar ordem serviço'
        }
    );

    lookupOrdemServico.setCallBack('onClick', (arguments) => {
        limparDadosOrdemServico();
        preencherDadosOrdemServico(arguments[0]);
    });

    lookupOrdemServico.setCallBack('onChange', (event, arguments) => {
        limparDadosOrdemServico();

        if (arguments[1] == true) {
            return;
        }

        preencherDadosOrdemServico($('q170_ordemservico').value);
    });

    function limparDadosOrdemServico() {
        $('q168_cgm').value = '';
        $('descricaoCGM').value = '';
        $('q168_inscricao').value = '';
        $('descricaoInscricao').value = '';
        $('q168_descricao').value = '';
        $('q168_localizacao').value = '';
        $('q168_horainicio').value = '';
        $('q168_horafim').value = '';
        $('q168_dataemissao').value = '';
        $('q168_datainicio').value = '';
        $('q168_datafim').value = '';
    }

    function preencherDadosOrdemServico(id) {

        const data = new FormData();
        data.append('q168_codigo', id);

        HttpClient.get(`${apiUrl}tributario/issqn/alvaraeventos/ordemservico/getOrdemServico?q168_codigo=${id}`, {body: data}).then(response => {
            if (response.error == true) {
                alert(response.message);
                return false;
            }

            const dataEmissao = new Date(`${response.data.q168_dataemissao} 12:00`);
            const dataInicio = new Date(`${response.data.q168_datainicio} 12:00`);
            const dataFim = new Date(`${response.data.q168_datafim} 12:00`);


            if (response.data.q168_cgm != null) {
                $('q168_cgm').value = response.data.q168_cgm;
                $('descricaoCGM').value = response.data.cgm;
            } else {
                $('q168_cgm').value = response.data.inscricao_cgm_codigo;
                $('descricaoCGM').value = response.data.inscricao;
            }

            $('q168_inscricao').value = response.data.q168_inscricao;
            $('descricaoInscricao').value = response.data.inscricao;
            $('q168_descricao').value = response.data.q168_descricao;
            $('q168_localizacao').value = response.data.q168_localizacao;
            $('q168_horainicio').value = response.data.q168_horainicio;
            $('q168_horafim').value = response.data.q168_horafim;
            $('q168_dataemissao').value = dataEmissao.getDateBR();
            $('q168_datainicio').value = dataInicio.getDateBR();
            $('q168_datafim').value = dataFim.getDateBR();

        }).catch(error => {
            alert(error.message);
        });
    }

    var lookupTipoEvento = new DBLookUp(
        $('ancoraTipoEvento'),
        $('q170_tipoalvara'),
        $('descricaoTipoEvento'),
        {
          'sArquivo': 'func_isstipoalvara.php',
          'sLabel': 'Pesquisar tipo de evento'
        }
    );

    lookupTipoEvento.setParametrosAdicionais(['lMov=1', 'lLibera=1', 'filtro=1', 'cadastro=1', 'alvara_evento=1']);
    lookupTipoEvento.setCallBack('onChange', (event, arguments) => {
        $('descricaoTipoEvento').value = arguments[0];
    });

    $('limpar').onclick = () => {
        $('frmAlvaraEventos').reset();
        $('excluir').disabled = true;
    };

    $('salvar').onclick = () => {
        const data = new FormData();
        const id = $('q170_codigo').value;

        data.append('q170_codigo', $('q170_codigo').value);
        data.append('q170_tipoalvara',$('q170_tipoalvara').value);
        data.append('q170_ordemservico', $('q170_ordemservico').value);
        data.append('q170_certidaobombeiro', $('q170_certidaobombeiro').value);
        data.append('q170_dataemissao', $('q170_dataemissao').value.replace(/[/]/g, '-'));
        data.append('q170_estimativapublico',$('q170_estimativapublico').value);
        data.append('q170_observacao', $('q170_observacao').value);

        var rota = `${apiUrl}tributario/issqn/alvaraeventos/alvaraevento`;


        if ($('q170_codigo').value != '') {
            rota = `${apiUrl}tributario/issqn/alvaraeventos/alvaraevento/update?q170_codigo=${id}`;
        }

        HttpClient.post(rota, {body: data}).then(response => {
            alert(response.message);

            if (response.error == true) {
                return false;
            }

            if (!!response.data && !!response.data.q170_codigo) {
                $('q170_codigo').value = response.data.q170_codigo;
            }

            $('imprimir').disabled = false;

        }).catch(error => {
            alert(error.message);
        });
    };

    $('pesquisar').onclick = () => {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_alvaraevento',
            'func_alvaraevento.php?funcao_js=parent.retorno_pesquisar|q170_codigo',
            'Pesquisa',
            true
        );
    };

    function retorno_pesquisar(id) {
        db_iframe_alvaraevento.hide();
        const data = new FormData();
        data.append('q170_codigo', id);

        HttpClient.get(`${apiUrl}tributario/issqn/alvaraeventos/alvaraevento/getAlvaraEvento?q170_codigo=${id}`, {body: data}).then(response => {
            if (response.error == true) {
                alert(response.message);
                return false;
            }

            const dataEmissao = new Date(`${response.data.q170_dataemissao} 12:00`);

            $('q170_codigo').value = response.data.q170_codigo;
            $('q170_ordemservico').value = response.data.q170_ordemservico;
            $('q170_ordemservico').dispatchEvent(new Event("change"));
            $('q170_tipoalvara').value = response.data.q170_tipoalvara;
            $('descricaoTipoEvento').value = response.data.tipoevento;
            $('q170_certidaobombeiro').value = response.data.q170_certidaobombeiro;
            $('q170_dataemissao').value = dataEmissao.getDateBR();
            $('q170_estimativapublico').value = response.data.q170_estimativapublico;
            $('q170_observacao').value = response.data.q170_observacao;
            $('imprimir').disabled = false;

        }).catch(error => {
            alert(error.message);
        });
    }

    $('imprimir').onclick = () => {
        const codigo = $('q170_codigo').value;

        if(!codigo || codigo == ''){
            alert('Código precisa estar preenchido!');
            return false;
        }

        window.open(`iss3_emissaoalvaraeventos001.php?codigoAlvara=${codigo}`,'','location=0,HEIGHT=600,WIDTH=600');
    };

    function carregarMensagensPadroes() {
        HttpClient.get(`${apiUrl}tributario/issqn/alvaraeventos/mensagempadrao`).then(response => {
            if(response.data.length > 0){
                for (let mensagemPadrao of response.data){
                    let element = document.createElement('option');
                    element.value = mensagemPadrao.q171_codigo;
                    element.innerText = mensagemPadrao.q171_descricao;
                    mensagens[mensagemPadrao.q171_codigo] = mensagemPadrao.q171_mensagem;

                    selectMensagemPadrao.appendChild(element);
                }

                mensagens[0] = '';
            }
        }).catch(error => {
            alert(error.message);
        });
    }

</script>
