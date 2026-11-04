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
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Emissão do Acompanhamento PPA/LDO</legend>
        <table class="form-container">
            <tr class="text-left">
                <td><label class="bold" for="planejamento">Planejamento:</label></td>
                <td>
                    <select id="planejamento" class="field-size8">
                        <option value="">Selecione um plano</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label class="bold" for="ano_inicial">Ano inicial:</label></td>
                <td>
                    <input type="text" name="ano_inicial" id="ano_inicial" class="field-size2 readonly" readonly>
                </td>
            </tr>
            <tr>
                <td><label class="bold" for="ano_final">Ano final:</label></td>
                <td>
                    <input type="text" name="ano_final" id="ano_final" class="field-size2 readonly" readonly>
                </td>
            </tr>
        </table>
        <fieldset class="separator">
            <legend>Filtros para impressão</legend>
            <table class="form-container">
                <tr>
                    <td><label id="ancoraOrgao" for="orgao"><a href="#">Órgão:</a></label></td>
                    <td>
                        <input type="text" id="orgao" lang="o40_orgao" class="field-size2">
                        <input type="text" id="descricaoOrgao" lang="o40_descr" class="readonly field-size8 readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraPrograma" for="programa"><a href="#">Programa:</a></label></td>
                    <td>
                        <input type="text" id="programa" lang="o54_programa" class="field-size2">
                        <input type="text" id="descricao" lang="o54_descr" class="readonly field-size8 readonly"
                               readonly>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset class="separator">
            <legend>Filtros para emissão do PDF</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label class="bold" for="apresentaRegionalizacao">Regionalização:</label>
                    </td>
                    <td>
                        <select id="apresentaRegionalizacao" class="field-size8">
                            <option value="1">Apresentar</option>
                            <option value="0"><b>Não</b> apresentar</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="bold" for="apresentaProduto">Produto:</label>
                    </td>
                    <td>
                        <select id="apresentaProduto" class="field-size8">
                            <option value="1">Apresentar</option>
                            <option value="0"><b>Não</b> apresentar</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="bold" for="apresentaValoresMetaFisicas">Metas Físicas:</label>
                    </td>
                    <td>
                        <select id="apresentaValoresMetaFisicas" class="field-size8">
                            <option value="1">Apresentar</option>
                            <option value="0"><b>Não</b> apresentar</option>
                        </select>
                    </td>
                </tr>

                <tr id="linhaApresentarExecucao">
                    <td><label for="apresentarExecucao" class="bold">Apresentar Execução:</label></td>
                    <td>
                        <select id="apresentarExecucao" name="apresentarExecucao">
                            <option value="0" selected>Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </td>
                </tr>

                <tr style="display: none" id="linhaExecucao">
                    <td><label for="execucao" class="bold">Valores:</label></td>
                    <td>
                        <select id="execucao" name="apresentarExecucao">
                            <option value="empenhado">Empenhado</option>
                            <option value="liquidado">Liquidado</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
    </fieldset>
    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>
</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script>
    const rota = 'financeiro/planejamento/relatorios/acompanhamento-ppa-ldo';

    const planejamentosTiposExibir = ['PPA', 'LDO'];
    const planejamento = new Planejamento(document.getElementById('planejamento'));
    const inputAnoInicial = document.getElementById('ano_inicial');
    const inputAnoFinal = document.getElementById('ano_final');
    const btnEmitir = document.getElementById('emitir');
    const ancoraPrograma = document.getElementById('ancoraPrograma');
    const programa = document.getElementById('programa');
    const descricao = document.getElementById('descricao');
    const ancoraOrgao = document.getElementById('ancoraOrgao');
    const orgao = document.getElementById('orgao');
    const descricaoOrgao = document.getElementById('descricaoOrgao');

    const apresentarExecucao = document.getElementById('apresentarExecucao');
    const linhaExecucao = document.getElementById('linhaExecucao');
    const linhaApresentarExecucao = document.getElementById('linhaApresentarExecucao');
    const execucao = document.getElementById('execucao');

    const lookUpPrograma = new DBLookUp(ancoraPrograma, programa, descricao, {
        'sArquivo': 'func_orcprograma.php',
        'sLabel': 'Pesquisar Programa',
        'sObjetoLookUp': "db_iframe_orcprograma"
    });

    const lookUpOrgao = new DBLookUp(ancoraOrgao, orgao, descricaoOrgao, {
        'sArquivo': 'func_orcorgao.php',
        'sLabel': 'Pesquisar Programa',
        'sObjetoLookUp': "db_iframe_orcprograma"
    });

    PHPSession.loadData().then(() => {
        planejamento.load();
    });

    planejamento.getElement().addEventListener('change', () => {
        if (planejamento.getValue() == '') {
            inputAnoInicial.value = '';
            inputAnoFinal.value = '';
            return
        }

        let filtros = ['previsao=', `ano=${planejamento.getPlano().pl2_ano_inicial}`];
        lookUpPrograma.setParametrosAdicionais(filtros);
        inputAnoInicial.value = planejamento.getPlano().pl2_ano_inicial;
        inputAnoFinal.value = planejamento.getPlano().pl2_ano_final;

    });

    btnEmitir.addEventListener('click', () => {
        let plano = planejamento.getValue();
        if (empty(plano)) {
            alert("selecione um plano.");
            return
        }

        const formData = new FormData();
        formData.append('planejamento_id', plano);
        let codigoPrograma = '';
        if (!empty(programa.value)) {
            codigoPrograma = Number(programa.value)
        }
        let codigoOrgao = '';
        if (!empty(orgao.value)) {
            codigoOrgao = Number(orgao.value)
        }
        formData.append('tipo', planejamento.getPlano().pl2_tipo);
        formData.append('orcprograma_id', codigoPrograma);
        formData.append('orcorgao_id', codigoOrgao);

        formData.append('apresentaRegionalizacao', $('apresentaRegionalizacao').value);
        formData.append('apresentaProduto', $('apresentaProduto').value);
        formData.append('apresentaValoresMetaFisicas', $('apresentaValoresMetaFisicas').value);

        formData.append('apresentarExecucao', apresentarExecucao.value);
        formData.append('execucaoPor', execucao.value);

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.pdf, "Acompanhamento PPA/LDO - PDF");
            download.show();
        });
    });

    apresentarExecucao.addEventListener('change', () => {
        linhaExecucao.style.display = 'none';
        if (apresentarExecucao.value === '1') {
            linhaExecucao.style.display = 'table-row';
        }
    });
</script>
