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

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : "";
$descricao = isset($_GET['descricao']) ? $_GET['descricao'] : "";

?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
    </table>
    <center>
        <br><br>
        <table width="790" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
                    <center>
                        <fieldset style='width: 80%;'> 
                            <legend>Adicionar grupo de exame</legend>
                            <div style="width: 90%">
                                <div style="padding-left: 10%; padding-top: 20px; text-align: left">
                                    <table>
                                        <tr>
                                            <td><label style="font-weight: bold; font-size: 12px" for="codigo">Código:&nbsp;</label></td>
                                            <td><input type="text" name="codigo" id="codigo" value="<?= $codigo ?>" style="background-color:#DEB887" readonly></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="margin-top: 5px">
                                                    <label style="font-weight: bold; font-size: 12px" for="descricao">Descrição:&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="margin-top: 5px">
                                                    <input type="text" name="descricao" id="descricao" maxlength="25" size="55" value="<?= $descricao ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div style="text-align: center; margin-top: 5px; text-decoration: none">
                                                    <button type="button" id="salvar" style="margin-bottom: 10px;">
                                                        <i class="fas fa-save"></i> Salvar
                                                    </button>
                                                    <button type="button" style="margin-bottom: 5px">
                                                        <a href="lab1_grupoExame001.php" style="text-decoration: none; color: black">
                                                            <i class="fas fa-undo"></i> Voltar
                                                        </a>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </fieldset>
                    </center>
                </td>
            </tr>
        </table>
    </center>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script type="text/javascript">
        $.noConflict()
        jQuery(document).ready(function() {
            const inputDescricao = document.getElementById('descricao');
            const inputCodigo = document.getElementById('codigo');
            const btnSalvar = document.getElementById('salvar');

            const salvar = () => {
                const formData = new FormData();
                formData.append('acao', 'salvar');
                if (inputCodigo.value) {
                    formData.append('codigo', inputCodigo.value);
                }
                formData.append('descricao', inputDescricao.value);

                HttpClient.post('lab1_grupoExame002.RPC.php', {
                    body: formData
                }).then(response => {
                    if (response.erro) {
                        alert(response.mensagem);
                        return;
                    }
                    window.location.href = "lab1_grupoExame001.php";
                    alert("Grupo salvo com sucesso.");
                })
            }

            btnSalvar.addEventListener('click', salvar);

        });
    </script>
</body>

</html>