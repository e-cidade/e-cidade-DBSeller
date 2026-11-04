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
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_app.utils.php");

$matricula = isset($_GET['matricula']) ? $_GET['matricula'] : null;
$abaSelecionada = isset($_GET['abaSelecionada']) ? $_GET['abaSelecionada'] : null;

$abas = [
    ['nome' => 'Certidão de Construção', 'redireciona' => 'true', 'link' => "cad3_conscadastro_002_detalhes.php?solicitacao=certidaoConstrucao&parametro=" . $matricula],
    ['nome' => 'Certidão de Lançamento', 'redireciona' => 'false', 'link' => 'cad3_conscertlanc001.php'],
    ['nome' => 'Certidão de Valor Venal', 'redireciona' => 'false', 'link' => 'cad3_conscertvalven001.php'],
    ['nome' => 'Certidão de Foro e Laudêmio', 'redireciona' => 'false', 'link' => 'cad3_conscertforlaud001.php'],
];

?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <?php
    db_app::load("scripts.js, prototype.js, strings.js, estilos.css");
    ?>
</head>

<body>
    <script>
        function mudaAba(link, matricula, redireciona) {
            if (redireciona) {
                location.href = link;
                return;
            }
            location.href = `cad3_conscertidoes001.php?abaSelecionada=${link}&matricula=${matricula}`
        }
    </script>

    <div class="container"><u>Certidões</u></div>
    <div class="container" style="display: flex; flex-wrap:wrap; justify-content: space-evenly; margin-top: 50px; margin-bottom: 50px;">

        <?php
        foreach ($abas as $aba) {
            echo "<input type='button' onclick='mudaAba(`" . $aba['link'] . "`, `$matricula`, " . $aba['redireciona'] . ")' value='" . $aba['nome'] . "'></input>";
        }
        ?>
    </div>

    <?php
    if (isset($abaSelecionada) && isset($matricula)) {
        require_once modification($abaSelecionada);
    }
    ?>
</body>

</html>