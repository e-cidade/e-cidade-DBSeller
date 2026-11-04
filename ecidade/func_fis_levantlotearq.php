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

//MODULO: educação
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
 <tr>
  <td align="center" valign="top">
   <?php

  if ( !isset($pesquisa_chave) ) {

    $sql = "SELECT y122_codigo AS t43_codlote,
                   CASE y122_pecafiscal WHEN 1 THEN 'AUTO DE INFRAÇÃO' ELSE 'NOTIF. DE LANÇAMENTO' END AS y29_tipofisc,
                   y27_descr,
                   y122_data AS q64_data,
                   y122_hora AS v02_hora,
                   y122_nomearq as rh133_nomearquivo,
                   (SELECT count(*) FROM fis_levantlotearqcont WHERE y123_levantlotearq = y122_codigo) AS db50_quantlinhas,
                   CASE y122_pecafiscal WHEN 1 THEN 3 ELSE 6 END AS tipopeca,
                   y122_pecafiscal
              FROM fis_levantlotearq
        INNER JOIN fiscalizacao.fis_tipofiscaliza ON y122_tipofiscal = y27_codtipo
             WHERE y122_instit = ".db_getsession("DB_instit")."
             ORDER BY y122_data DESC, y122_hora DESC";

      db_lovrot($sql,15,"()","",$funcao_js);
    }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $sql = "SELECT CASE y122_pecafiscal WHEN 1 THEN 'AUTO DE INFRAÇÃO' ELSE 'NOTIF. DE LANÇAMENTO' END AS y29_tipofisc,
                         CASE y122_pecafiscal WHEN 1 THEN 4 else 6 end as tipopeca,
                         y122_pecafiscal
              FROM fis_levantlotearq
        INNER JOIN fiscalizacao.fis_tipofiscaliza ON y122_tipofiscal = y27_codtipo
             WHERE y122_codigo = $pesquisa_chave AND y122_instit = ".db_getsession("DB_instit");

          $result = db_query($sql);

          if(pg_num_rows($result)!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$y29_tipofisc','$tipopeca','$y122_pecafiscal',$false);</script>";
          }else{
           echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
         echo "<script>".$funcao_js."('',false);</script>";
        }
      }
  ?>
  </td>
 </tr>
</table>
</body>
</html>

