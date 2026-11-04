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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
?>
<html>
<head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body class="body-default">
    <div class="container">
        <form id="frmAlvaraEventos" name="frmAlvaraEventos">
            <fieldset>
                <legend>Alvará de eventos</legend>
                <table>
                    <tr>
                        <td>
                            <a id="ancoraAlvaraEventos" href="#">
                                <label for="q170_codigo">Alvará de Eventos:</label>
                            </a>
                        </td>
                        <td colspan="3">
                            <input id="q170_codigo" name="q170_codigo" type="text"class="field-size2"/>
                            <input id="q168_descricao" name="q168_descricao" type="text"class="field-size7 readonly" disabled="disabled"/>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="button" name="imprimir" id="imprimir" value="Imprimir">
        </form>
    </div>
</body>
</html>
<script type="text/javascript">

    var lookupOrdemServico = new DBLookUp(
        $('ancoraAlvaraEventos'),
        $('q170_codigo'),
        $('q168_descricao'),
        {
          'sArquivo': 'func_alvaraevento.php',
          'sLabel': 'Pesquisar alvará de eventos'
        }
    );

    $('imprimir').onclick = () => {
        const codigo = $('q170_codigo').value;

        if(!codigo || codigo == ''){
            alert('Código precisa estar preenchido!');
            return false;
        }

        window.open(`iss3_emissaoalvaraeventos001.php?codigoAlvara=${codigo}`,'','location=0,HEIGHT=600,WIDTH=600');
    };
</script>