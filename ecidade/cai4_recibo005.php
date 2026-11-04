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
require_once(modification("libs/db_sessoes.php"));
parse_str($_SERVER['QUERY_STRING']);
//db_postmemory($HTTP_SERVER_VARS,2);
$instit = db_getsession("DB_instit");
?>
<html>
<head>
<title>Lista de Valores</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_retorna(codtaxa){
    location.href='cai4_recibo004.php?codtaxa='+codtaxa;
}
</script>

</head>
<body >
<div class="container">
    <?php
    $sql = "";
    if (isset($receita)) {
        if (!empty($receita)) {
            $sql = "select tabdesc.*
		        from tabdesc
            inner join tabrec       on k02_codigo  = k07_codigo
						left  join tabdescdepto on k69_tabdesc = tabdesc.codsubrec
            where k07_codigo = $receita
						  and k07_instit = $instit
							and ( (k07_dtval >= current_date or k07_dtval is null)
							and (k02_limite >= current_date or k02_limite is null) )
							and (k69_coddepto = ".db_getsession("DB_coddepto")." or k69_coddepto is null )
            order by k07_descr";
        }
    }
    if (empty($sql)) {
        $sql = "select tabdesc.* from tabdesc
          inner join tabrec       on k02_codigo  = k07_codigo
					left  join tabdescdepto on k69_tabdesc = tabdesc.codsubrec
          where  k07_instit = $instit
					  and ( (k07_dtval  >= current_date or k07_dtval  is null)
						and (k02_limite >= current_date or k02_limite is null) )
						and (k69_coddepto = ".db_getsession("DB_coddepto")." or k69_coddepto is null )
	        order by k07_descr";
    }

    db_lovrot($sql, 15, "()", "", "js_retorna|codsubrec");
    ?>
</div>
</body>
</html>
