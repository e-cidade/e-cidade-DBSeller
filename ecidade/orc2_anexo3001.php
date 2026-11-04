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
db_postmemory($_POST);
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

<div class="container">
    <fieldset>
        <legend>Anexo 3 - Fontes da Receita</legend>
    <form name="form1" method="post" action="">
        <table class="form-container">

            <tr>
                <td id="ctnInstituicao" colspan="4" style="font-weight: normal">
                    <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                </td>
            </tr>

            <tr>
                <td><label for="ementario">Selecione o plano que deseja agrupar os dados:</label></td>
                <td>
                    <select name="ementario" id="ementario">
                        <option value="ecidade" checked>Plano e-Cidade</option>
                        <option value="uniao">Plano União/Federação</option>
                        <option value="estadual">Plano Estadual/Regional</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
    </fieldset>
    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>

</div>

<script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<?php
db_menu();
?>
</body>
<script>

    const rota = 'v4/api/financeiro/orcamento/relatorios/anexo-3';
    var viewInstituicao = new DBViewInstituicao('viewInstituicao', $('ctnInstituicao'));
    viewInstituicao.show();

    const valida = () => {
        try {
            if (viewInstituicao.getInstituicoesSelecionadas(true).length === 0) {
                throw 'Selecione ao menos uma instituição';
            }
        } catch (e) {
            alerta(e);
            return false;
        }

        return true;
    };

    document.getElementById('emitir').addEventListener('click', async () => {
        if (!valida()) {
            return
        }

        const filtros = {
            "ementario" : document.getElementById('ementario').value,
            "instituicoes" : []
        }

        for (let codigo of viewInstituicao.getInstituicoesSelecionadas(true)) {
            filtros.instituicoes.push(codigo);
        }


        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(rota, filtros);
        js_removeObj('loading_message');

        const download = new DBDownload();
        download.addFile(response.data.data.pdf, "Anexo 3 - Fontes da Receita - PDF");
        download.addFile(response.data.data.csv, "Anexo 3 - Fontes da Receita - CSV");
        download.show();
    });

</script>
</html>
