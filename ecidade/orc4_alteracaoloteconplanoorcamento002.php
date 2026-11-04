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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
?>
<html>
<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script  type="text/javascript" src="scripts/scripts.js"></script>
    <script  type="text/javascript" src="scripts/strings.js"></script>
    <script  type="text/javascript" src="scripts/prototype.js"></script>
    <script  type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script  type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script  type="text/javascript" src="scripts/classes/AlteracaoEmLote/AlteracaoEmLote.js"></script>


    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>

<div class="container" style="margin-top: 30px; width: 98%;">

    <fieldset style="width:600px">
        <legend class="bold">Dotações Encontradas</legend>
        <div id="ctnAlteracaoLoteConplanoorcamento"></div>
    </fieldset>
    <p>
        <input type="button" id="btnSalvar" value="Salvar" onclick="salvar()" />
    </p>
</div>
</body>
</html>
<?php db_menu(); ?>


<script>

    let get = js_urlToObject();
    let AlteracaoEmLoteConplanoorcamento = null;
    let template = 'ConplanoOrcamento/IndicadorResultadoPrimario.json';

    function salvar() {

        let linhasSelecionadas = AlteracaoEmLoteConplanoorcamento.getLinhas();

        if (linhasSelecionadas.length === 0) {
            alert('Nenhuma linha selecionada para alteração.');
            return false;
        }

        AjaxRequest.create(
            'orc4_alteracaoLoteConplanoorcamento.RPC.php',
            {'exec' : 'processar', 'registros' : linhasSelecionadas},
            function (response, erro) {
                alert(response.mensagem);
            }
        ).setMessage('Aguarde, salvando informações...').execute();
    }

    let parametros = {
        'exec': 'consulta',
        'estrutural': get.estrutural
    };

    AjaxRequest.create(
        'orc4_alteracaoLoteConplanoorcamento.RPC.php',
        parametros,
        function (response, erro) {

            if (erro) {
                alert(response.mensagem);
                return false;
            }

            AlteracaoEmLoteConplanoorcamento = new AlteracaoEmLote(
                'AlteracaoEmLoteConplanoorcamento',
                'conta',
                template,
                $('ctnAlteracaoLoteConplanoorcamento'),
                response.contas
            );
            AlteracaoEmLoteConplanoorcamento.start();
        }
    ).setMessage('Aguarde, buscando registros...').execute();


</script>



