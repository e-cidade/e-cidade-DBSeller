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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));

?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class='container'>
    <fieldset>
        <legend>Anexo 2 - Resumo da Receita</legend>
        <form name="formulario" id="formulario">
            <table class="form-container">
                <tr>
                    <td id="ctnInstituicao" colspan="4" style="font-weight: normal">
                        <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                    </td>
                </tr>
                <tr>
                    <td><label for="ementario">Selecione o plano que deseja agrupar os dados:</label></td>
                    <td colspan="3">
                        <select name="ementario" id="ementario">
                            <option value="ecidade" checked>Plano e-Cidade</option>
                            <option value="uniao">Plano União/Federação</option>
                            <option value="estadual">Plano Estadual/Regional</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="dadosEmissao">Dados da emissão:</label></td>
                    <td colspan="3">
                        <select name="dadosEmissao" id="dadosEmissao">
                            <option value="orcamento" checked>Orçamento</option>
                            <option value="balanco">Balanço</option>
                        </select>
                    </td>
                </tr>
            </table>
            <fieldset class="separator" id="ctnSaldoPorDatas" style="display: none">
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
        </form>
    </fieldset>

    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>
</div>
<?php
db_menu();
?>
<script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>

<script>
    const dadosEmissao = document.getElementById('dadosEmissao')
    const ctnSaldoPorDatas = document.getElementById('ctnSaldoPorDatas')
    const inputDataInicio = new DBInputDate(document.getElementById('dataInicio'));
    const inputDataFinal = new DBInputDate(document.getElementById('dataFinal'));
    const dataHoje = new Date();

    inputDataInicio.setValue(`${dataHoje.getUTCFullYear()}-01-01`);
    inputDataFinal.setValue(`${dataHoje.getUTCFullYear()}-01-01`);

    var viewInstituicao = new DBViewInstituicao('viewInstituicao', document.getElementById('ctnInstituicao'));
    viewInstituicao.iHeight = 150;
    viewInstituicao.show();

    dadosEmissao.addEventListener('change', function () {
        ctnSaldoPorDatas.style.display = 'none';
        inputDataFinal.setValue(`${dataHoje.getUTCFullYear()}-01-01`);
        if (dadosEmissao.value === 'balanco') {
            ctnSaldoPorDatas.style.display = '';
            inputDataFinal.setValue(dataHoje.toLocaleString());
        }
    });

    const rota = 'v4/api/financeiro/orcamento/relatorios/anexo-2-receita';

    document.getElementById('emitir').addEventListener('click', async () => {

        if (viewInstituicao.getInstituicoesSelecionadas(true).length === 0) {
            alert('Selecione ao menos uma instituição');
            return
        }

        const filtros = {
            "ementario" : document.getElementById('ementario').value,
            "instituicoes" : [],
            "dataInicio": inputDataInicio.__toLocaleDateString(),
            "dataFinal": inputDataFinal.__toLocaleDateString(),
            "dadosEmissao": document.getElementById('dadosEmissao').value
        }

        for (let codigo of viewInstituicao.getInstituicoesSelecionadas(true)) {
            filtros.instituicoes.push(codigo);
        }

        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(rota, filtros);
        js_removeObj('loading_message');

        const download = new DBDownload();
        download.addFile(response.data.data.pdf, "Anexo 2 - Resumo da Receita - PDF");
        download.addFile(response.data.data.csv, "Anexo 2 - Resumo da Receita - CSV");
        download.show();
    });

</script>
</body>
</html>
