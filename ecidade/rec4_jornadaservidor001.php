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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
    <?php db_app::load('scripts.js, datagrid.widget.js, strings.js, prototype.js, estilos.css, AjaxRequest.js, classes/recursoshumanos/Efetividade/DBViewJornadaServidor.js'); ?>
    <style type="text/css">
    #aviso {

        background-color: rgb(255, 255, 255);
        padding: 5px 10px;
        font-weight: bold;
    }
    </style>
</head>
<body>

<body bgcolor="#cccccc" style='margin-top: 30px'>

<div id="ctnAlterarJornadaServidor" class="container">

    <fieldset>
        <legend>Manutenção de Jornadas do Servidor</legend>
        <div>
          <table>
            <tr>
              <td id="aviso">
                Utiliza os botões abaixo para manutenção das jornadas
              </td>
            </tr>
          </table>
        </div>
    </fieldset>

    <input type="button" name="novo"      id="novo"      value="Novo"      onclick="novaJornada()" />
    <input type="button" name="pesquisar" id="pesquisar" value="Pesquisar" onclick="pesquisar()" />

    <div id="listaJornadaServidor" class="container"></div>

</div>
<script type="text/javascript">
function novaJornada(){
    viewJornadaServidor = new DBViewJornadaServidor();
    viewJornadaServidor.abrir();
};

function alterarJornada(codigoJornada) {
    viewJornadaServidor = new DBViewJornadaServidor();
    viewJornadaServidor.setCodigoJornadaServidor(codigoJornada).abrir();
    Jandb_iframe_jornadaservidor.hide();
}

function pesquisar() {
    oJanela = js_OpenJanelaIframe('',
                                  'db_iframe_jornadaservidor',
                                  'func_jornadaservidor.php?funcao_js=parent.alterarJornada|rh212_sequencial',
                                  'Pesquisar Jornadas',
                                  true
    );
}
</script>
<?php db_menu() ?>
</body>
</html>