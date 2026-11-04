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
require_once(modification("libs/db" . "_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$oRelatorio = new relatorioContabil(112);
$clrotulo   = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <div class='table_header'>
        Demonstrativo Dívida Flutuante(Anexo17)
    </div>
    <form id="form1" name="form1" method="post" >
        <fieldset>
            <legend>Filtros</legend>
            <table class="form-container">
                <tr>
                    <td >
                        <label for="o116_periodo"><b>Período:</b></label>
                    </td>
                    <td >
                        <?php
                        $aPeriodos         = $oRelatorio->getPeriodos();
                        $aListaPeriodos    = array();
                        $aListaPeriodos[0] = "Selecione";
                        foreach ($aPeriodos as $oPeriodo) {
                            $aListaPeriodos[$oPeriodo->o114_sequencial] = $oPeriodo->o114_descricao;
                        }

                        db_select("o116_periodo", $aListaPeriodos, true, 1);
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="imprimir" id="imprimir" type="button" value="Imprimir" />
    </form>
</div>
</body>
</html>

<?php
db_menu();
?>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">

    const rota = '/financeiro/contabilidade/relatorio/anexo17';

    const formulario = document.getElementById('form1');
    const inputPeriodo = document.getElementById('o116_periodo');
    const btnImprimir = document.getElementById('imprimir');

    btnImprimir.addEventListener('click', () => {

        if ($('o116_periodo').value == 0) {

            alert('Você não escolheu nenhum período. Verifique!');
            return false;
        }

        const formData = new FormData(formulario);
        formData.set('o116_periodo', inputPeriodo.value);

        PHPSession.appendFormData(formData);

        HttpClient.post(PHPSession.requestApi + rota , {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const download = new DBDownload();
            download.addFile(response.data, response.message);
            download.show();
        });
    });
</script>
