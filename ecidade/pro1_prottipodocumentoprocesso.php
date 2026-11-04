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

use ECidade\Patrimonial\Protocolo\Modelo\TipoDocumentoProcesso;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));

$cl_prottipodocumentoprocesso = new cl_prottipodocumentoprocesso();

$cl_prottipodocumentoprocesso->rotulo->label();
$cl_rotulo = new rotulocampo;
$cl_rotulo->label("p91_sequencial");
$cl_rotulo->label("p91_descricao");

$tipoDocumentoProcesso = null;
if (isset($_GET['p91_sequencial'])) {
    $tipoDocumentoProcesso = new TipoDocumentoProcesso($_GET['p91_sequencial']);
}

$p91_sequencial = !empty($tipoDocumentoProcesso) ? $tipoDocumentoProcesso->getSequencial() : null;
$p91_descricao = !empty($tipoDocumentoProcesso) ? $tipoDocumentoProcesso->getDescricao() : null;

?>

<html>
<head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript"src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript"src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
</head>
<body class="body-default">
    <div class="container" style="margin-top: 100px;">
        <form name="formTipoDocumentoProcesso" method="post" action="">
            <fieldset style="padding: 20px;">
                <legend>Tipo de Documento do Processo</legend>
                <table>
                    <tr>
                        <td>
                            <label for=""><?php echo $Lp91_sequencial ?></label>
                        </td>
                        <td>
                            <?php
                                db_input('p91_sequencial', 30, $Ip91_sequencial, true, 'text');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for=""><?php echo $Lp91_descricao ?></label>
                        </td>
                        <td>
                            <?php
                                db_input('p91_descricao', 30, $Ip91_descricao, false, 'text');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for=""><?php echo $Lp91_sigla ?></label>
                        </td>
                        <td>
                            <?php
                                db_input(
                                    'p91_sigla',
                                    30,
                                    $Ip91_sigla,
                                    false,
                                    'text',
                                    3,
                                    "",
                                    "",
                                    "",
                                    "",
                                    5
                                );
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <div style="text-align: center; margin: 20px 0;">
                <input name="salvar" id="btnSalvar" type="button" value="Salvar">
                <input name="novo" id="btnNovo" type="button" value="Novo" style="display: none;">
            </div>

            <div id="gridContainer"></div>
        </form>
    </div>
</body>
</html>

<script>
    var arquivoRPC = 'pro1_prottipodocumentoprocesso.RPC.php';
    var codigoTiposDocumentoDB = [1, 2, 3, 4, 5, 6];

    var collection = new Collection().setId("p91_sequencial");
    var grid = new DatagridCollection(collection).configure({
        order    : false,
        height   : 120
    });

    // Adiciona colunas
    grid.addColumn("p91_sequencial", {
        label : "Código",
        align : "left",
        width : "20%"
    });

    grid.addColumn("p91_sigla", {
        label : "Sigla",
        align : "left",
        width : "20%"
    });

    grid.addColumn("p91_descricao", {
        label : "Descrição",
        align : "left",
        width : "40%"
    });

    // Adiciona ações
    grid.addAction("Alterar", 'Alterar', function(evento, registro) {
        if (codigoTiposDocumentoDB.includes(registro.p91_sequencial)) {
            alert("Não é possível alterar os Tipos de Documento cujos códigos são:\n"+codigoTiposDocumentoDB.join(', '));
            return;
        }

        $('p91_sequencial').value = registro.p91_sequencial;
        $('p91_descricao').value = registro.p91_descricao;
        $('p91_sigla').value = registro.p91_sigla;
        $('btnSalvar').value = 'Alterar';
        $('btnNovo').style.display = 'inline-block';
    }, true, 'fa-edit');

    grid.addAction("Excluir", 'Excluir', function(evento, registro) {
        if (codigoTiposDocumentoDB.includes(registro.p91_sequencial)) {
            alert("Não é possível excluir os Tipos de Documento cujos códigos são:\n"+codigoTiposDocumentoDB.join(', '));
            return;
        }

        if (!confirm(`Deseja excluir o tipo de documento ${registro.p91_descricao}?`)) {
            return;
        }

        new AjaxRequest(
            arquivoRPC,
            {
                exec: 'excluir',
                codigoTipoDocumento: registro.p91_sequencial
            },
            function(retorno, erro) {
                if (retorno.erro) {
                    alert(retorno.message.urlDecode());
                    return;
                }

                alert(`Tipo de documento ${registro.p91_descricao} foi removido com sucesso.`)

                collection.remove(registro.ID)
                grid.reload();
            }
        ).execute();

    }, true, 'fa-trash');

    grid.show($('gridContainer'));

    function preencheGrid(retorno, erro) {
        if (erro) {
            alert(retorno.message.urlDecode());
            return;
        }

        grid.clear();
        for (var i = 0; i < retorno.tiposDocumentoProcesso.length; i++) {
            collection.add({
                p91_sequencial: retorno.tiposDocumentoProcesso[i].p91_sequencial,
                p91_descricao: retorno.tiposDocumentoProcesso[i].p91_descricao,
                p91_sigla: retorno.tiposDocumentoProcesso[i].p91_sigla
            });
        }
        grid.reload();
    }

    function buscarTiposDocumentoProcesso() {
        new AjaxRequest(
            arquivoRPC,
            {exec: 'buscar'},
            preencheGrid
        ).execute();
    }

    $('btnSalvar').addEventListener('click', function () {
        var parametros  = {
            exec: 'salvar',
            codigoTipoDocumento: $('p91_sequencial').value,
            descricaoTipoDocumento: $('p91_descricao').value,
            siglaTipoDocumento: $('p91_sigla').value,
        };

        new AjaxRequest(
            arquivoRPC,
            parametros,
            function(retorno, erro) {
                if (retorno.status == '2') {
                    alert(retorno.message.urlDecode());
                }

                var mensagem = 'Tipo de Documento de Processo incluído com sucesso.';
                if ($('btnSalvar').value === 'Alterar') {
                    mensagem = 'Tipo de Documento de Processo alterado com sucesso.';
                }

                $('p91_sequencial').value = retorno.tipoDocumentoProcesso.p91_sequencial;
                $('p91_descricao').value = retorno.tipoDocumentoProcesso.p91_descricao.urlDecode();
                $('p91_sigla').value = retorno.tipoDocumentoProcesso.p91_sigla;
                $('btnSalvar').value = 'Alterar';
                $('btnNovo').style.display = 'inline-block';

                alert(mensagem);

                collection.add({
                    p91_sequencial: retorno.tipoDocumentoProcesso.p91_sequencial,
                    p91_descricao: retorno.tipoDocumentoProcesso.p91_descricao.urlDecode(),
                    p91_sigla: retorno.tipoDocumentoProcesso.p91_sigla,
                });

                grid.reload();

            }
        ).execute();
    });

    $('btnNovo').observe('click', function () {
        location.href = 'pro1_prottipodocumentoprocesso.php';
    });

    (function() {
        buscarTiposDocumentoProcesso()
    })();
</script>
