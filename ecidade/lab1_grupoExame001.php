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
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <!-- <link href="estilos.css" rel="stylesheet" type="text/css"> -->
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
                            <legend><b>Grupos de Exames</b></legend>
                            <div style="width: 90%">
                                <div style="text-align: right;">
                                    <button style="margin-bottom: 10px;">
                                        <a href="lab1_grupoExame002.php" style="text-decoration: none; color: black">
                                            <i class="fas fa-plus"></i> Adicionar
                                        </a>
                                    </button>
                                </div>
                                <div style="padding-top: 10px; text-align: left">
                                    <table id="data-table" class="table table-responsive-md" data-height="300" data-virtual-scroll="true" style="width: 100%;">
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
            var table = jQuery('#data-table');

            window.operateEvents = {
                'click .excluir': function(e, value, row, index) {
                    if (confirm("Você tem certeza que deseja excluir o grupo: " + row.codigo + ' - ' + row.descricao)) {
                        const formData = new FormData();
                        formData.append('acao', 'excluirGrupo');
                        formData.append('codigo', row.codigo);
                        HttpClient.post('lab1_grupoExame002.RPC.php', {
                            body: formData
                        }).then((response) => {
                            if (response.erro) {
                                alert(response.mensagem);
                                return;
                            }
                            buscarGrupos();
                        });
                    }
                }
            }

            const formatterAcoes = (value, row) => {
                return '<a href="lab1_grupoExame002.php?codigo=' + row.codigo + '&descricao=' + row.descricao + '"><i title="Editar Grupo" class="fas fa-edit" style="font-size:15px"></i></a> <a class="excluir" href="javascript:void(0)"><i title="Excluir Grupo" class="fas fa-trash-alt" style="font-size:15px; margin-left:10px"></i></a>';
            }

            table.bootstrapTable({
                locale: 'pt-BR',
                columns: [{
                        title: 'Código',
                        field: 'codigo',
                        align: 'left',
                        valign: 'middle'
                    },
                    {
                        title: 'Descrição',
                        field: 'descricao',
                        align: 'center',
                        valign: 'middle',
                    },
                    {
                        title: 'Ações',
                        align: 'center',
                        valign: 'middle',
                        events: window.operateEvents,
                        formatter: formatterAcoes
                    }
                ]
            })

            const buscarGrupos = (value, row) => {
                const formData = new FormData();
                formData.append('acao', 'buscarGrupos');
                HttpClient.post('lab1_grupoExame002.RPC.php', {
                    body: formData
                }).then((response) => {
                    if (response.erro) {
                        alert(response.mensagem);
                        return;
                    }
                    table.bootstrapTable('load', response.grupos);
                });
            }

            buscarGrupos();
        });
    </script>
</body>

</html>