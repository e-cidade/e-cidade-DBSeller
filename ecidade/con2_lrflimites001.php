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

use ECidade\Configuracao\RelatorioLegal\Repositorio\RelatorioRepositorio;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2019\AnexoVI as AnexoVI2019;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/db_liborcamento.php');
require_once modification('dbforms/db_classesgenericas.php');

$clcriaabas = new cl_criaabas();
$clrotulo = new rotulocampo();

$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');

db_postmemory($HTTP_POST_VARS);

$abas = array();
$titulos = array();
$fontes = array();
$sizecp = array();

$relatorio = RelatorioRepositorio::find(AnexoVI2019::CODIGO_RELATORIO);
$anousu = db_getsession("DB_anousu");

?>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DBSeller Informática Ltda</title>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
            <center>
                <?php

                if ($anousu <= 2007) {
                    $clcriaabas->identifica = array(
                        "relatorio" => "Relatorio",
                        "variaveis" => "Variáveis",
                        "parametro" => "Parametros"
                    );

                    $clcriaabas->title = array(
                        "relatorio" => "Relatorio",
                        "variaveis" => "Variáveis",
                        "parametro" => "Parametros"
                    );

                    $clcriaabas->src = array(
                        "relatorio" => "con2_lrflimites011.php",
                        "variaveis" => "con2_conrelinfo001.php?c83_codrel={$relatorio->getSequencial()}",
                        "parametro" => "con2_conrelparametros.php?c83_codrel={$relatorio->getSequencial()}"
                    );

                    $clcriaabas->sizecampo = array(
                        "relatorio" => 23,
                        "variaveis" => 23,
                        "parametro" => 23
                    );
                } elseif ($anousu < 2010) {
                    $clcriaabas->identifica = array(
                        "relatorio" => "Relatorio",
                        "notas" => "Fonte/Notas Explicativas",
                        "variaveis" => "Variáveis",
                        "parametro" => "Parametros"
                    );

                    $clcriaabas->title = array(
                        "relatorio" => "Relatorio",
                        "notas" => "Fonte/Notas Explicativas",
                        "variaveis" => "Variáveis",
                        "parametro" => "Parametros"
                    );

                    $clcriaabas->src = array(
                        "relatorio" => "con2_lrflimites011.php",
                        "notas" => "con2_conrelnotas.php?c83_codrel={$relatorio->getSequencial()}",
                        "variaveis" => "con2_conrelinfo001.php?c83_codrel={$relatorio->getSequencial()}",
                        "parametro" => "con2_conrelparametros.php?c83_codrel={$relatorio->getSequencial()}"
                    );

                    $clcriaabas->sizecampo = array(
                        "relatorio" => 23,
                        "notas" => 23,
                        "variaveis" => 23,
                        "parametro" => 23
                    );
                } else {
                    $clcriaabas->identifica = array(
                        "relatorio" => "Relatório",
                        "notas" => "Fonte/Notas Explicativas"
                    );

                    $clcriaabas->title = array(
                        "relatorio" => "Relatório",
                        "notas" => "Fonte/Notas Explicativas"
                    );

                    $clcriaabas->src = array(
                        "relatorio" => "con2_lrflimites011.php",
                        "notas" => "con2_conrelnotas.php?c83_codrel={$relatorio->getSequencial()}"
                    );

                    $clcriaabas->sizecampo = array(
                        "relatorio" => 23,
                        "notas" => 23
                    );
                }

                $clcriaabas->cria_abas();
                ?>
            </center>
        </td>
    </tr>
</table>
  <?php
  db_menu();
?>
</body>
</html>