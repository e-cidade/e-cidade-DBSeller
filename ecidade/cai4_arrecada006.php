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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
parse_str($_SERVER["QUERY_STRING"]);
?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
    table.tableRegistros {
        margin-left: auto;
        margin-right: auto;
        border: 1px solid threedshadow;
        background-color: #EEE;
        border-spacing: 1;
        min-width: 650px;
    }

    table.tableRegistros th {
        background-color: #CCC;
        height: 30px;
    }

    table.tableRegistros td {
        height: 20px;
    }

    .totalizador {
        height: 30px;
        text-align: right;
        font-weight: bold;
        background-color: #CCC;
    }    
</style>
</head>
<body>
  <table class="tableRegistros" id="tab">
    <thead>
        <tr>
          <th width="15%" nowrap style="text-align: left;"> Receita</th>
          <th width="60%" nowrap style="text-align: left;">Descri&ccedil;&atilde;o</th>
          <th width="25%" nowrap style="text-align: right;">Valor Corrigido</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (isset($codcla)) {
        $result = db_query("select *
	                     from disrec
						      inner join tabrec on k00_receit = k02_codigo
				    	 where codcla = $codcla");
        if (pg_num_rows($result)!=0) {
            $totalvlr = 0;
            for ($i=0; $i<pg_num_rows($result); $i++) {
                db_fieldsmemory($result, $i);
                $totalvlr += $vlrrec;
                ?>
          <tr>
            <td style="text-align: left;"><?=$k00_receit?></td>
            <td><?=$k02_drecei?></td>
            <td style="text-align: right;"><?=db_formatar($vlrrec, 'f')?></td>
          </tr>
                <?php
            }
            ?>
        <tr class="totalizador">
          <td colspan=2>Total :</td>
          <td><?=db_formatar($totalvlr, 'f')?></td>
        </tr>
            <?php
        }
    }
    ?>
    </tbody>
  </table>
</body>
</html>
<script>
parent.js_removeObj('msgBox');
</script>
