<?php
/**
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
require_once(modification("libs/db_app.utils.php"));
$rotulo = new rotulocampo;
$rotulo->label('k60_codigo');
$rotulo->label('k60_descr');
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js, strings.js, numbers.js, prototype.js, AjaxRequest.js, datagrid.widget.js");
    db_app::load("widgets/Collection.widget.js, widgets/DatagridCollection.widget.js, widgets/DBDownload.widget.js");
    ?>
    <link type="text/css" rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="container">
    <form id="frmConfiguracao">
        <fieldset>
            <legend>
                Configurações Integração Processo Eletrônico
            </legend>

            <table>
                <tr>
                    <td>
                        <label for="usuario">
                            <b>Usuário</b>
                        </label>
                    </td>
                    <td>
                        <input type="hidden" size="20" id="codigo">
                        <input type="text" size="20" id="usuario">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="senha">
                            <b>Senha:</b>
                        </label>
                    </td>
                    <td>
                        <input type="password" size="20" id="senha">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="localidade">
                            <b>Código da Localidade:</b>
                        </label>
                    </td>
                    <td>
                        <input type="text" size="20" id="localidade">
                    </td>
                </tr>
            </table>
        </fieldset>
        <input type="button" id="Salvar" value="Salvar" onclick="salvar();">
    </form>
</div>
</body>
</html>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"),
    db_getsession("DB_instit"));
?>
<script>
    const URL_RPC = 'jur4_configuracoesprocessoeletronico.RPC.php';
    var senha            = $('senha');
    var usuario          = $('usuario');
    var codigo           = $('codigo');
    var codigoLocalidade = $('localidade');

    
    function salvar() {

        var parametros = {
            'exec':'salvarConfiguracao',
            'senha': senha.value,
            'codigo': codigo.value,
            'usuario': usuario.value,
            'localidade': codigoLocalidade.value,
        };
        new AjaxRequest(URL_RPC, parametros, function(response, error){

            alert(response.mensagem.urlDecode());
            if (error) {
                return false;
            }
            fillFormFromObject($('frmConfiguracao'), response);

        }).setMessage('Aguarde, salvando configuração..').execute();

    }


    (function(){

        var parametros = {
            'exec': 'getConfiguracao'
        }
        new AjaxRequest(URL_RPC, parametros, function(response, error) {

            if (error) {
                alert(response.mensagem.urlDecode());
                return false;
            }
            fillFormFromObject($('frmConfiguracao'), response);

        }).setMessage('Aguarde, salvando configuração..').execute();
    })();
</script>