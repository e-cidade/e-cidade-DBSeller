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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js, strings.js, numbers.js, prototype.js, estilos.css, AjaxRequest.js");
    ?>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">
    <form id="manutencaoRegin">
        <fieldset>
            <legend>Parâmetros do Regin</legend>

            <table class="form-container">
                <tr>
                    <td>
                        <label for="urlWebservice"><b>Url do Webservice:</b></label>
                    </td>
                    <td>
                        <input type="url" id="urlWebservice" size="50"/>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input type="button" value="Salvar" id="btnSalvar">
    </form>
</div>
<?php
db_menu(db_getsession("DB_id_usuario"),
  db_getsession("DB_modulo"),
  db_getsession("DB_anousu"),
  db_getsession("DB_instit"));
?>
</body>
<script>
  var URL_RPC = 'con4_manutencaoiniRegin.RPC.php';
  var urlWebservice = $('urlWebservice');

  $('btnSalvar').observe('click', function () {

    if (empty($F('urlWebservice'))) {
      alert('o campo Url do webservice é de preenchimento obrigatório.');
      return false;
    }

    var parametros = {
      execucao: 'salvar',
      urlWebservice: urlWebservice.value
    };

    new AjaxRequest(URL_RPC, parametros, function (retorno, erro) {
      alert(retorno.mensagem);
    }).execute();
  });

  (function () {

    new AjaxRequest(URL_RPC, {execucao: 'getParametros'}, function (retorno, erro) {
      if (erro) {

        alert(retorno.mensagem);
        return false;
      }

      fillFormFromObject($('manutencaoRegin'), retorno);
    }).execute();

  })();
</script>
</html>
