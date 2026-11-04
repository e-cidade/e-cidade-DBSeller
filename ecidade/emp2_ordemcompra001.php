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
db_postmemory($HTTP_POST_VARS);

$clrotulo = new rotulocampo;
$clrotulo->label("m51_codordem");

?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>

    <script>

        function validarCampos() {
            const codigoInicio = Number(document.form1.m51_codordem_ini.value);
            const codigoFim = Number(document.form1.m51_codordem_fim.value);
            let erro = false;

            if (!codigoInicio || !codigoFim) {
                erro = 'Os códigos de inicio e fim devem ser preenchidos!';
            }

            if (codigoInicio > codigoFim && !erro) {
                erro = 'O código inicial não pode ser maior que o final!';
            }

            if (codigoFim - codigoInicio > 100 && !erro) {
                erro = 'Defina um intervalo de ordens entre 1 e 100!'
            }

            if (erro) {
                alert(erro);
            }

            return !erro;
        }

        function js_emite() {
            if (validarCampos()) {
                jan = window.open('emp2_ordemcompra002.php?m51_codordem_ini=' + document.form1.m51_codordem_ini.value + '&m51_codordem_fim=' + document.form1.m51_codordem_fim.value, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
                jan.moveTo(0, 0);
            }
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body style="margin-top: 25px; background-color: #CCCCCC;">
<center>
    <fieldset style="width: 300px;">
        <legend><strong>Emite Ordem de Compra</strong></legend>
        <form name="form1" method="post" action="">
            <table align="center">
                <tr>
                    <td nowrap><b>
                            <?php db_ancora('Ordem de ', "js_pesquisa_matordem(true);", 1); ?>
                        </b>
                    </td>
                    <td>
                        <?php db_input('m51_codordem', 8, $Im51_codordem, true, 'text', 4, "onchange='js_pesquisa_matordem(false);'", "m51_codordem_ini") ?>
                        <strong> à </strong>
                        <?php db_input('m51_codordem', 8, $Im51_codordem, true, 'text', 4, "", "m51_codordem_fim") ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <p><input name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();"></p>
</center>
<?php
db_menu();
?>
<script>

    function js_pesquisa_matordem(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matordem', 'func_matordem.php?funcao_js=parent.js_mostramatordem1|m51_codordem|', 'Pesquisa', true);
        } else {
            if (document.form1.m51_codordem_ini.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_matordem', 'func_matordem.php?pesquisa_chave=' + document.form1.m51_codordem_ini.value + '&funcao_js=parent.js_mostramatordem', 'Pesquisa', false);
            } else {
                document.form1.m51_codordem_ini.value = '';
                document.form1.m51_codordem_fim.value = '';
            }
        }
    }

    function js_mostramatordem(chave, erro) {
        if (erro == true) {
            alert('Ordem de Compra não existe!!');
            document.form1.m51_codordem_ini.value = '';
            document.form1.m51_codordem_fim.value = '';
            document.form1.m51_codordem_ini.focus();
        } else {
            document.form1.m51_codordem_fim.value = document.form1.m51_codordem_ini.value;
        }
    }

    function js_mostramatordem1(chave1) {
        document.form1.m51_codordem_fim.value = chave1;
        document.form1.m51_codordem_ini.value = chave1;
        db_iframe_matordem.hide();
    }
</script>
</body>
</html>
