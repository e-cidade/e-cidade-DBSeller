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
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
</head>
<body>
<div id='ctnAbas'></div>
<div id='ctnAbaEmissao' class='subcontainer'>
    <form name="formulario" id="formulario">
        <fieldset>
            <legend>Balancete da despesa</legend>
            <table class="form-container">
                <tr>
                    <td id="ctnInstituicao" colspan="4" style="font-weight: normal" class="field-size-max">
                        <input type="hidden" id="db_selinstit" value="">
                    </td>
                </tr>
                <tr>
                    <td class="field-size3"><label for="modelo">Modelo:</label></td>
                    <td><select id="modelo" name="modelo">
                            <option selected value="analitico">Analítico</option>
                            <option value="sintetico">Sintético</option>
                        </select>
                    </td>
                </tr>
                <tr style="display: none" id="linhaNivel">
                    <td class="field-size3"><label for="nivel">Totalizar por Nível:</label></td>
                    <td><select id="nivel"  multiple>
                            <option value="orgao">Órgao</option>
                            <option value="unidade">Unidade</option>
                            <option value="funcao">Função</option>
                            <option value="subfuncao">Subfunção</option>
                            <option value="programa">Programa</option>
                            <option value="projeto">Projéto/Atividade</option>
                            <option value="elemento">Elemento</option>
                            <option value="recurso">Recurso</option>
                        </select>
                    </td>
                </tr>

                <tr id="linhaFiltroRecursos" >
                    <td>Apresentar :</td>
                    <td>
                        <select id="apresentarRecurso" name="apresentarRecurso">
                            <option selected value="fonteRecurso">Fonte Recurso</option>
                            <option value="depara">Depara Subrecurso</option>
                            <option value="siconfi">Código Siconfi</option>
                        </select>
                    </td>
                </tr>
            </table>
            <fieldset class="separator">
                <legend>Saldo por datas</legend>
                <table class="form-container">
                    <tr>
                        <td>Data Inicial:</td>
                        <td><input id="dataInicio" name="dataInicio" type="text"/></td>
                        <td>Data Final:</td>
                        <td><input id="dataFinal" name="dataFinal" type="text"/></td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>
    </form>
    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>
</div>
<div id='ctnAbaFiltros' style="display: none">
    <?php
    $_GET['iCodigoRelatorio'] = 250;
    require_once 'con2_filtrosrelatorios.php';
    ?>
</div>

</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script type="text/javascript">

    const rota = 'financeiro/contabilidade/relatorio/balancete-despena-por-complemento';

    const btnEmitir = document.getElementById('emitir');

    const formulario = document.getElementById('formulario');
    const linhaNivel = document.getElementById('linhaNivel');
    const modelo = document.getElementById('modelo');
    const nivel = document.getElementById('nivel');
    const inputDataInicio = new DBInputDate(document.getElementById('dataInicio'));
    const inputDataFinal = new DBInputDate(document.getElementById('dataFinal'));
    const dataHoje = new Date();

    inputDataInicio.setValue(`${dataHoje.getUTCFullYear()}-01-01`);
    inputDataFinal.setValue(dataHoje.toLocaleString());

    const ctnAbaFiltros = document.getElementById('ctnAbaFiltros')
    const ctnAbas = new DBAbas(document.getElementById('ctnAbas'));
    const abaDetalhamento = ctnAbas.adicionarAba("Relatório", document.getElementById('ctnAbaEmissao'));
    const abaCronograma = ctnAbas.adicionarAba("Filtros", ctnAbaFiltros);

    var viewInstituicao = new DBViewInstituicao('viewInstituicao', document.getElementById('ctnInstituicao'));
    viewInstituicao.iHeight = 150;
    viewInstituicao.show();

    PHPSession.loadData().then(() => {
        ctnAbaFiltros.style.display = '';

        modelo.addEventListener('change', () => {
            linhaNivel.style.display = modelo.value === 'sintetico' ? 'table-row' : 'none';
        });
    });

    btnEmitir.addEventListener('click', () => {

        if (!validarInputs()) {
            return
        }
        const formData = new FormData(formulario);

        if (modelo.value === 'sintetico') {
            for (let opcao of nivel.options) {
                if (opcao.selected) {
                    formData.append('nivel[]', opcao.value)
                }
            }
        }

        formData.append('apresentarRecurso', document.getElementById('apresentarRecurso').value);
        formData.append('instituicoes', JSON.stringify(viewInstituicao.getInstituicoesSelecionadas()));
        formData.append('filtros', JSON.stringify(getFiltros()));
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.pdf, "Balancete da despesa - PDF");
            if (response.data.csv) {
                download.addFile(response.data.csv, "Balancete da despesa - CSV");
            }
            download.show();
        });
    });

    const validarInputs = () => {
        try {
            if (modelo.value === 'sintetico' && nivel.value === '') {
                throw 'Quando informado modelo sintético, deve-se selecionar o(s) nível(is) que deseja agrupar as informações.';
            }

            if (inputDataInicio.value.getUTCFullYear() != inputDataFinal.value.getUTCFullYear()) {
                throw 'As datas devem estar dentro do mesmo exercício.';
            }

            if (js_comparadata(inputDataInicio.inputElement.value, inputDataFinal.inputElement.value, '>')) {
                throw 'Data de inicio deve ser menor que a data final.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    }
</script>
