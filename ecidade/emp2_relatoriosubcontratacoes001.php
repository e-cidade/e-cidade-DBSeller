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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));

$db_opcao = 1;
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC>
<div class="container" style="width: 640px;">
    <form name='form1' id="form1">
        <fieldset>
            <legend>Relatório de Subcontratações</legend>
            <table class="form-container">
               <tr>
                    <td>
                        <b>Data Inicial:</b>
                    </td>
                    <td>
                        <?php
                          db_inputdata("datainicial", null, null, null, true, "text", 1);
                        ?>
                        <b>Data Final:</b>
                        <?php
                          db_inputdata("datafinal", null, null, null, true, "text", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <label for="contratado">
                        <td nowrap title="<?= $Tz01_numcgm ?>">
                            <?php
                            db_ancora("Contratado: ", "js_pesquisa_numcgm(true);", 1);
                            ?>
                        </td>
                    </label>
                    <td>
                        <?php
                        $Sz01_numcgm = "CGM";
                        db_input('z01_numcgm', 10, 1, true, 'text', 1, " onchange='js_pesquisa_numcgm(false);'");
                        ?>
                        <?php
                        db_input('z01_nome', 60, '', true, 'text', 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <label for="subcontratado">
                        <td nowrap title="<?= $Tz01_numcgm ?>">
                            <?php
                            db_ancora("Subcontratado: ", "js_pesquisa_numcgm_subcontratado(true);", 1);
                            ?>
                        </td>
                    </label>
                    <td>
                        <?php
                        $Sz01_numcgm = "CGM";
                        db_input('z01_numcgm_subcontratado', 10, 1, true, 'text', 1, " onchange='js_pesquisa_numcgm_subcontratado(false);'");
                        ?>
                        <?php
                        db_input('z01_nome_subcontratado', 60, '', true, 'text', 3);
                        ?>
                    </td>
                </tr>
            </table>
            <br>
        <input type='button' value='Emitir' onclick='js_emitir()'>
    </form>
</div>
</body>
</html>
<?php
db_menu();
?>
<script type="text/javascript" src="scripts/session.js"></script>
<script>


    function js_pesquisa_numcgm(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'func_cgm.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
        } else {
            if (document.form1.z01_numcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'func_cgm.php?lNovoDetalhe=1&pesquisa_chave=' + document.form1.z01_numcgm.value + '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
            } else {
                document.form1.z01_nome.value = '';
            }
        }

    }

    function js_mostracgm(chave, erro) {

        document.form1.z01_nome.value = chave;
        if (erro == true) {
            document.form1.z01_nome.value = '';
            document.form1.z01_numcgm.focus();
        }
    }

    function js_mostracgm1(chave1, chave2) {

        document.form1.z01_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        func_nome.hide();
    }

    function js_pesquisa_numcgm_subcontratado(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'func_cgm.php?funcao_js=parent.js_mostracgm1_subcontratado|z01_numcgm|z01_nome', 'Pesquisa', true);
        } else {
            if (document.form1.z01_numcgm_subcontratado.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'func_cgm.php?lNovoDetalhe=1&pesquisa_chave=' + document.form1.z01_numcgm_subcontratado.value + '&funcao_js=parent.js_mostracgm_subcontratado', 'Pesquisa', false);
            } else {
                document.form1.z01_nome_subcontratado.value = '';
            }
        }

    }

    function js_mostracgm_subcontratado(chave, erro) {

        document.form1.z01_nome_subcontratado.value = chave;
        if (erro == true) {
            document.form1.z01_nome_subcontratado.value = '';
            document.form1.z01_numcgm_subcontratado.focus();
        }
    }

    function js_mostracgm1_subcontratado(chave1, chave2) {

        document.form1.z01_numcgm_subcontratado.value = chave1;
        document.form1.z01_nome_subcontratado.value = chave2;
        func_nome.hide();
    }




    function js_emitir() {

        let rota = 'financeiro/empenho/relatorio/relatorio-subcontratacao';

        if ($F('datainicial') == "" || $F('datafinal') == "") {
            alert('A data inicial e a final do pagamento devem ser informadas!');
            return false;
        } else if(js_comparadata($F('datainicial'), $F('datafinal'), '>')){
            alert ("A data final deve ser maior que a data inicial.");
            return false;
        }

        const formData = new FormData();

        formData.append('dataInicial', $F('datainicial'));
        formData.append('dataFinal',$F('datafinal'));
        formData.append('contratado', $F('z01_numcgm'));
        formData.append('subcontratado', $F("z01_numcgm_subcontratado"));

        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            console.log(response.data);
            window.open(window.CurrentWindow.ECIDADE_REQUEST_PATH + response.data, '', 'height=800, width=600');
        })
    }

</script>
