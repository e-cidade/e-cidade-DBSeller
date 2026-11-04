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
    <style type="text/css">
        .linhaProcessoExterno{
            display: none;
        }
    </style>
</head>
<body class="body-default">
    <div class="container">
        <form id="frmOrdemServico" name="frmOrdemServico">
            <fieldset>
                <legend>Ordem de serviço</legend>
                <table>
                    <tr>
                        <td>
                            <label for="q168_codigo"><b>Código:</b></label>
                        </td>
                        <td colspan="3">
                            <input id="q168_codigo" name="q168_codigo" type="text" data="q168_codigo" class="field-size2 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="cbxProcessoSistema"><b>Processo do Sistema:<b></label>
                        </td>
                        <td colspan="3">
                            <select id="cbxProcessoSistema" class="field-size2">
                                <option value="1">SIM</option>
                                <option value="0">NÃO</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="linhaProcesso">
                        <td>
                            <a id="ancoraProcesso" href="#">
                                <label for="processo">Processo:</label>
                            </a>
                        </td>
                        <td colspan="3">
                            <input id="processo" name="processo" type="text" data="p58_numero" class="field-size2"/>
                            <input id="q168_processo" name="q168_processo" type="hidden"/>
                            <input id="descricaoProcesso" name="descricaoProcesso" type="text" data="p58_requer" class="field-size7 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr class="linhaProcessoExterno">
                        <td>
                            <label for="q168_processoexterno"><b>Processo:<b></label>
                        </td>
                        <td colspan="3">
                            <input type="text" name="q168_processoexterno" id="q168_processoexterno" class="field-size2"/>
                        </td>
                    </tr>
                    <tr class="linhaProcessoExterno">
                        <td>
                            <label for="q168_titularprocessoexterno"><b>Titular do Processo:<b></label>
                        </td>
                        <td colspan="3">
                            <input type="text" name="q168_titularprocessoexterno" id="q168_titularprocessoexterno" class="field-size9"/>
                        </td>
                    </tr>
                    <tr class="linhaProcessoExterno">
                        <td>
                            <label for="q168_dataprocessoexterno"><b>Data do Processo:</b></label>
                        </td>
                        <td colspan="3">
                            <input id="q168_dataprocessoexterno" name="q168_dataprocessoexterno" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a id="ancoraCGM" href="#">
                                <label for="q168_cgm">CGM:</label>
                            </a>
                        </td>
                        <td colspan="3">
                            <input id="q168_cgm" name="q168_cgm" type="text" data="z01_numcgm" class="field-size2"/>
                            <input id="descricaoCGM" name="descricaoCGM" type="text" data="z01_nome" class="field-size7 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a id="ancoraInscricao" href="#">
                                <label for="q168_inscricao">Inscrição:</label>
                            </a>
                        </td>
                        <td colspan="3">
                            <input id="q168_inscricao" name="q168_inscricao" type="text" data="q02_inscr" class="field-size2"/>
                            <input id="descricaoInscricao" name="descricaoInscricao" type="text" data="z01_nome" class="field-size7 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="q168_descricao"><b>Descrição:</b></label>
                        </td>
                        <td colspan="3">
                            <input id="q168_descricao" name="q168_descricao" type="text" class="field-size9"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="q168_localizacao"><b>Localização:</b></label>
                        </td>
                        <td colspan="3">
                            <input id="q168_localizacao" name="q168_localizacao" type="text" class="field-size9"/>
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
                            <input id="q168_datainicio" name="q168_datainicio"/>
                        </td>
                        <td>
                            <label for="q168_horainicio"><b>Hora de início:</b></label>
                        </td>
                        <td>
                            <input id="q168_horainicio" name="q168_horainicio" class="field-size1" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="q168_datafim"><b>Data de fim:</b></label>
                        </td>
                        <td>
                            <input id="q168_datafim" name="q168_datafim"/>
                        </td>
                        <td>
                            <label for="q168_horafim"><b>Hora de fim:</b></label>
                        </td>
                        <td>
                            <input id="q168_horafim" name="q168_horafim" class="field-size1" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div id="divFiscais"></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" name="salvar" id="salvar" value="Salvar">
            <input type="button" name="limpar" id="limpar" value="Limpar">
            <input type="button" name="excluir" id="excluir" value="Excluir" disabled="disabled">
            <input type="button" name="pesquisar" id="pesquisar" value="Pesquisar">
        </form>
    </div>
</body>
<script type="text/javascript">

    const
        url = "<?php echo ECIDADE_REQUEST_PATH;?>",
        apiUrl = `${url}v4/api/`,
        dataAtual = new Date(),
        q168_dataemissao = new DBInputDate($('q168_dataemissao')),
        q168_datainicio = new DBInputDate($('q168_datainicio')),
        q168_datafim = new DBInputDate($('q168_datafim')),
        q168_dataprocessoexterno = new DBInputDate($('q168_dataprocessoexterno')),
        q168_horainicio = new DBInputHora($('q168_horainicio')),
        q168_horafim = new DBInputHora($('q168_horafim'));

    q168_dataemissao.setReadOnly(true);
    $('q168_dataemissao').classList.add('field-size2');
    $('q168_datainicio').classList.add('field-size2');
    $('q168_datafim').classList.add('field-size2');
    $('q168_dataprocessoexterno').classList.add('field-size2');
    adicionarDataHoraAtual();

    var lancadorFiscal = new DBLancador('lancadorFiscal');
    lancadorFiscal.setNomeInstancia("lancadorFiscal");
    lancadorFiscal.setGridHeight(100);
    lancadorFiscal.setTextoFieldset("Fiscais");
    lancadorFiscal.setLabelAncora("Fiscal: ");
    lancadorFiscal.setParametrosPesquisa("func_cadfiscais.php", ['id_usuario', 'nome']);
    lancadorFiscal.show($("divFiscais"));

    var lookupProcesso = new DBLookUp(
        $('ancoraProcesso'),
        $('processo'),
        $('descricaoProcesso'),
        {
          'sArquivo': 'func_protprocesso_protocolo.php',
          'sLabel': 'Pesquisar processo',
          'aCamposAdicionais': ['dl_codigo_do_processo']
        }
    );

    lookupProcesso.setCallBack('onClick', (arguments) => {
        $('q168_processo').value = arguments[2];
    });

    lookupProcesso.setCallBack('onChange', (event, arguments) => {
        $('descricaoProcesso').value = arguments[1];
        $('q168_processo').value = arguments[3];
    });

    var lookupCGM = new DBLookUp(
        $('ancoraCGM'),
        $('q168_cgm'),
        $('descricaoCGM'),
        {
          'sArquivo': 'func_nome.php',
          'sLabel': 'Pesquisar CGM',
          'aParametrosAdicionais': ['testanome=true']
        }
    );

    var lookupInscricao = new DBLookUp(
        $('ancoraInscricao'),
        $('q168_inscricao'),
        $('descricaoInscricao'),
        {
          'sArquivo': 'func_issbase.php',
          'sLabel': 'Pesquisar Inscricao',
          'aParametrosAdicionais' : ['calculo=1']
        }
    );


    function addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }

    function adicionarDataHoraAtual() {
        q168_datainicio.value = dataAtual.getDateBR();
        q168_horainicio.value = addZero(dataAtual.getHours()) + ':' + addZero(dataAtual.getMinutes());
    }


    $('limpar').onclick = () => {
        $('frmOrdemServico').reset();
        $('excluir').disabled = true;
        $('cbxProcessoSistema').value = 1;
        $('cbxProcessoSistema').dispatchEvent(new Event("change"));
        lancadorFiscal.clearAll();
        adicionarDataHoraAtual();
    };

    $('pesquisar').onclick = () => {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_ordemservico',
            'func_ordemservico.php?funcao_js=parent.retorno_pesquisar|q168_codigo',
            'Pesquisa',
            true
        );
    };

    $('excluir').onclick = () => {
        if ($('q168_codigo').value == '') {
            alert('Código da ordem de serviço não informado.');
            return false;
        }

        if (confirm('Deseja excluir a ordem de serviço?')) {

            const data = new FormData();
            data.append('q168_codigo', $('q168_codigo').value);

            HttpClient.post(`${apiUrl}tributario/issqn/alvaraeventos/ordemservico/desprocessar`, {body: data}).then(response => {
                alert(response.message);

                if (response.error == true) {
                    return false;
                }

                $('limpar').click();

            }).catch(error => {
                alert(error.message);
            });
        }
    };

    function retorno_pesquisar(id) {
        db_iframe_ordemservico.hide();
        lancadorFiscal.clearAll();

        const data = new FormData();
        data.append('q168_codigo', id);

        HttpClient.get(`${apiUrl}tributario/issqn/alvaraeventos/ordemservico/getOrdemServico?q168_codigo=${id}`, {body: data}).then(response => {
            if (response.error == true) {
                alert(response.message);
                return false;
            }

            $('limpar').click();
            $('excluir').disabled = false;

            const dataEmissao = new Date(`${response.data.q168_dataemissao} 12:00`);
            const dataInicio = new Date(`${response.data.q168_datainicio} 12:00`);
            const dataFim = new Date(`${response.data.q168_datafim} 12:00`);

            $('q168_codigo').value = response.data.q168_codigo;
            $('q168_processo').value = response.data.q168_processo;
            $('processo').value = response.data.processo;
            $('descricaoProcesso').value = response.data.requerente;
            $('q168_cgm').value = response.data.q168_cgm;
            $('descricaoCGM').value = response.data.cgm;
            $('q168_inscricao').value = response.data.q168_inscricao;
            $('descricaoInscricao').value = response.data.inscricao;
            $('q168_descricao').value = response.data.q168_descricao;
            $('q168_localizacao').value = response.data.q168_localizacao;
            $('q168_horainicio').value = response.data.q168_horainicio;
            $('q168_horafim').value = response.data.q168_horafim;
            $('q168_dataemissao').value = dataEmissao.getDateBR();
            $('q168_datainicio').value = dataInicio.getDateBR();
            $('q168_datafim').value = dataFim.getDateBR();
            $('q168_processoexterno').value = response.data.q168_processoexterno;
            $('q168_titularprocessoexterno').value = response.data.q168_titularprocessoexterno;

            if (response.data.q168_dataprocessoexterno != null) {
                const dataProcessoExterno = new Date(`${response.data.q168_dataprocessoexterno} 12:00`);
                $('q168_dataprocessoexterno').value = dataProcessoExterno.getDateBR();

                $('cbxProcessoSistema').value = 0;
                $('cbxProcessoSistema').dispatchEvent(new Event("change"));
            }

            response.data.fiscais.each((fiscal) => {
                lancadorFiscal.adicionarRegistro(fiscal.codigo, fiscal.nome)
            });

            lancadorFiscal.renderizarRegistros();
        }).catch(error => {
            alert(error.message);
        });
    }

    $('salvar').onclick = () => {
        const data = new FormData();

        if ($('q168_dataprocessoexterno').value != '' && js_comparadata($('q168_dataprocessoexterno').value, dataAtual.getDateBR(), '>')) {
            alert('Data do Processo não pode ser superior a data atual.');
            return false;
        }

        if ($('q168_datainicio').value == '') {
            alert("Data de início deve ser informada.");
            return false;
        }

        if ($('q168_datafim').value == '') {
            alert("Data de fim deve ser informada.");
            return false;
        }

        if (js_comparadata($('q168_datafim').value, $('q168_datainicio').value, '<')) {

            alert('Data de fim deve ser maior que data de início.');
            return false;
        }

        data.append('q168_codigo',$('q168_codigo').value);
        data.append('q168_processo',$('q168_processo').value);
        data.append('q168_cgm',$('q168_cgm').value);
        data.append('q168_inscricao',$('q168_inscricao').value);
        data.append('q168_descricao',$('q168_descricao').value);
        data.append('q168_localizacao',$('q168_localizacao').value);
        data.append('q168_horainicio',$('q168_horainicio').value);
        data.append('q168_horafim',$('q168_horafim').value);
        data.append('q168_dataemissao', $('q168_dataemissao').value.replace(/[/]/g, '-'));
        data.append('q168_datainicio', $('q168_datainicio').value.replace(/[/]/g, '-'));
        data.append('q168_datafim', $('q168_datafim').value.replace(/[/]/g, '-'));
        data.append('q168_processoexterno',$('q168_processoexterno').value);
        data.append('q168_titularprocessoexterno',$('q168_titularprocessoexterno').value);
        data.append('q168_dataprocessoexterno', $('q168_dataprocessoexterno').value.replace(/[/]/g, '-'));

        lancadorFiscal.getRegistros().each((registro) => {
            data.append('fiscal[]', registro.sCodigo);
        });

        HttpClient.post(`${apiUrl}tributario/issqn/alvaraeventos/ordemservico/processar`, {body: data}).then(response => {
            alert(response.message);

            if (response.error == true) {
                return false;
            }

            if (!!response.data && !!response.data.q168_codigo) {
                $('q168_codigo').value = response.data.q168_codigo;
            }

        }).catch(error => {
            alert(error.message);
        });
    };

    $('cbxProcessoSistema').onchange = (event) => {
        var linhaProcesso = document.getElementsByClassName("linhaProcesso");
        var linhasProcessosExterno = document.getElementsByClassName("linhaProcessoExterno");

        if ($('cbxProcessoSistema').value == 0) {

            $('processo').value = '';
            $('q168_processo').value = '';
            $('descricaoProcesso').value = '';

            linhaProcesso[0].style.display = 'none';

            for(var i = 0; i < linhasProcessosExterno.length; i++){
                linhasProcessosExterno[i].style.display = 'table-row';
            }

            return;
        }


        $('q168_processoexterno').value = '';
        $('q168_titularprocessoexterno').value = '';
        $('q168_dataprocessoexterno').value = '';

        linhaProcesso[0].style.display = 'table-row';

        for(var i = 0; i < linhasProcessosExterno.length; i++){
            linhasProcessosExterno[i].style.display = 'none';
        }
    };
</script>
</html>
